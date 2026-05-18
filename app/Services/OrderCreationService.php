<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Customer\CustomerContext;
use App\Customer\CustomerSessionKeys;
use App\Repositories\MenuRepository;
use App\Support\TokenGenerator;
use mysqli;

final class OrderCreationService
{
    public function __construct(
        private readonly MenuRepository $menus,
        private readonly CartService $cart,
    ) {
    }

    /**
     * @throws \InvalidArgumentException
     * @return array{public_token:string, order_number:string, order_id:int}
     */
    public function placeOrder(CustomerContext $ctx, string $paymentMethod): array
    {
        if (!in_array($paymentMethod, ['qris', 'cashier'], true)) {
            throw new \InvalidArgumentException('Metode pembayaran tidak valid.');
        }

        $draft = (new CheckoutDraftService())->get();
        if ($draft === null || $draft['name'] === null || $draft['name'] === '') {
            throw new \InvalidArgumentException('Lengkapi data pemesan di halaman ringkasan.');
        }

        $lines = $this->cart->lines();
        if ($lines === []) {
            throw new \InvalidArgumentException('Keranjang kosong.');
        }

        $menuIds = array_map(static fn (array $l): int => $l['menu_id'], $lines);
        $menuMap = $this->menus->mapByIds($menuIds);

        $subtotal = 0.0;
        $preparedLines = [];
        foreach ($lines as $line) {
            $mid = $line['menu_id'];
            if (!isset($menuMap[$mid])) {
                throw new \InvalidArgumentException('Menu tidak ditemukan.');
            }
            $m = $menuMap[$mid];
            if ((int) $m['venue_id'] !== $ctx->venueId) {
                throw new \InvalidArgumentException('Menu tidak tersedia untuk venue ini.');
            }
            if ((int) $m['is_available'] !== 1) {
                throw new \InvalidArgumentException('Menu tidak tersedia: ' . $m['name']);
            }
            if ((int) ($m['stock_quantity'] ?? 0) < $line['qty']) {
                throw new \InvalidArgumentException('Stok tidak mencukupi untuk menu: ' . $m['name']);
            }
            $unit = (float) $m['price'];
            $qty = $line['qty'];
            $lineSub = $unit * $qty;
            $subtotal += $lineSub;
            $preparedLines[] = [
                'menu_id' => $mid,
                'warung_id' => (int) $m['warung_id'],
                'name' => (string) $m['name'],
                'unit_price' => $unit,
                'qty' => $qty,
                'note' => $line['note'],
                'line_subtotal' => $lineSub,
            ];
        }

        $venueRepo = new \App\Repositories\VenueRepository();
        $venue = $venueRepo->findById($ctx->venueId);
        $serviceRate = ($venue['service_fee_percent'] ?? 10.0) / 100.0;
        
        $serviceTax = round($subtotal * $serviceRate, 2);
        $total = round($subtotal + $serviceTax, 2);

        $publicToken = TokenGenerator::publicToken();
        $orderDate = new \DateTimeImmutable('now', new \DateTimeZone(date_default_timezone_get()));
        $orderDateYmd = $orderDate->format('Y-m-d');
        $orderNumber = null;

        $expiryMinutes = (int) ($venue['payment_expiry_minutes'] ?? 15);
        $deadline = (new \DateTimeImmutable('now', new \DateTimeZone(date_default_timezone_get())))
            ->modify('+' . $expiryMinutes . ' minutes')
            ->format('Y-m-d H:i:s');

        $customerName = $draft['name'];
        $customerEmail = $draft['email'];
        $diningType = $draft['dining_type'];

        /*
         * mysqli bind_param membutuhkan argumen by-reference; properti readonly
         * pada CustomerContext tidak boleh di-pass langsung (PHP akan error).
         */
        $venueId = $ctx->venueId;
        $diningTableId = $ctx->diningTableId;

        $gatewayOrderId = null;

        $orderId = Database::transaction(function (mysqli $db) use (
            $venueId,
            $diningTableId,
            $publicToken,
            $customerName,
            $customerEmail,
            $diningType,
            $paymentMethod,
            $subtotal,
            $serviceTax,
            $total,
            $deadline,
            $gatewayOrderId,
            $preparedLines,
            $orderDateYmd,
            $orderDate,
            &$orderNumber
        ): int {
            $sequence = $this->nextDailySequence($db, $venueId, $orderDateYmd);
            $orderNumber = TokenGenerator::orderNumberForDate($orderDate, $sequence);

            $sql = <<<SQL
                INSERT INTO orders (
                    venue_id, dining_table_id, order_number, public_token,
                    customer_name, customer_email, dining_type, payment_method,
                    status, subtotal, service_tax, total, payment_deadline_at,
                    gateway_order_id
                ) VALUES (?,?,?,?,?,?,?,?,'pending_payment',?,?,?,?,?)
                SQL;

            $stmt = $db->prepare($sql);
            $stmt->bind_param(
                'iissssssdddss',
                $venueId,
                $diningTableId,
                $orderNumber,
                $publicToken,
                $customerName,
                $customerEmail,
                $diningType,
                $paymentMethod,
                $subtotal,
                $serviceTax,
                $total,
                $deadline,
                $gatewayOrderId
            );
            $stmt->execute();
            $orderId = (int) $stmt->insert_id;
            $stmt->close();

            $itemSql = <<<SQL
                INSERT INTO order_items (
                    order_id, menu_id, warung_id, menu_name_snapshot,
                    unit_price, quantity, note, line_subtotal
                ) VALUES (?,?,?,?,?,?,?,?)
                SQL;
            $itemStmt = $db->prepare($itemSql);
            
            $stockSql = "UPDATE menus SET stock_quantity = stock_quantity - ? WHERE id = ?";
            $stockStmt = $db->prepare($stockSql);
            
            $warungIds = [];
            foreach ($preparedLines as $pl) {
                $note = $pl['note'] === '' ? null : $pl['note'];
                $mid = $pl['menu_id'];
                $wid = $pl['warung_id'];
                $warungIds[$wid] = true;
                $name = $pl['name'];
                $unit = $pl['unit_price'];
                $qty = $pl['qty'];
                $ls = $pl['line_subtotal'];
                
                $itemStmt->bind_param(
                    'iiisdisd',
                    $orderId,
                    $mid,
                    $wid,
                    $name,
                    $unit,
                    $qty,
                    $note,
                    $ls
                );
                $itemStmt->execute();
                
                $stockStmt->bind_param('ii', $qty, $mid);
                $stockStmt->execute();
            }
            $itemStmt->close();
            $stockStmt->close();

            $fulSql = 'INSERT IGNORE INTO order_warung_fulfillment (order_id, warung_id, status) VALUES (?, ?, \'new\')';
            $fulStmt = $db->prepare($fulSql);
            foreach (array_keys($warungIds) as $wid) {
                $w = (int) $wid;
                $fulStmt->bind_param('ii', $orderId, $w);
                $fulStmt->execute();
            }
            $fulStmt->close();

            return $orderId;
        });

        $this->cart->clear();
        $_SESSION[CustomerSessionKeys::LAST_ORDER_TOKEN] = $publicToken;

        return [
            'public_token' => $publicToken,
            'order_number' => $orderNumber,
            'order_id' => $orderId,
        ];
    }

    private function nextDailySequence(mysqli $db, int $venueId, string $orderDateYmd): int
    {
        $upsert = <<<SQL
            INSERT INTO order_daily_sequences (venue_id, order_date, last_sequence)
            VALUES (?, ?, 1)
            ON DUPLICATE KEY UPDATE last_sequence = last_sequence + 1
            SQL;
        $stmt = $db->prepare($upsert);
        $stmt->bind_param('is', $venueId, $orderDateYmd);
        $stmt->execute();
        $stmt->close();

        $select = $db->prepare('SELECT last_sequence FROM order_daily_sequences WHERE venue_id = ? AND order_date = ? LIMIT 1');
        $select->bind_param('is', $venueId, $orderDateYmd);
        $select->execute();
        $row = $select->get_result()->fetch_assoc();
        $select->close();

        return max(1, (int) ($row['last_sequence'] ?? 1));
    }
}
