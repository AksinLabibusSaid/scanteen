<?php

declare(strict_types=1);

namespace App\Customer;

use App\Repositories\DiningTableRepository;

/**
 * Mengikat session customer ke meja lewat query ?t=TOKEN_BARCODE.
 */
final class CustomerAccess
{
    public static function syncTableTokenFromRequest(): bool
    {
        file_put_contents(dirname(__DIR__, 2) . '/pages/Customer/log.txt', "Full GET: " . json_encode($_GET) . "\n", FILE_APPEND);
        if (!isset($_GET['t'])) {
            return false;
        }
        $token = trim((string) $_GET['t']);
        file_put_contents(dirname(__DIR__, 2) . '/pages/Customer/log.txt', "Received token: " . $token . "\n", FILE_APPEND);
        if ($token === '') {
            return false;
        }

        $repo = new DiningTableRepository();
        $row = $repo->findActiveByToken($token);
        if ($row === null) {
            self::clear(); // Clear session to prevent falling back to old table
            return false;
        }

        // Check if table is occupied (active orders or scanning state)
        $isOccupied = false;
        try {
            $mysqli = \App\Core\Database::mysqli();
            
            // A. Check active orders
            $stmtOrder = $mysqli->prepare("SELECT COUNT(*) AS c FROM orders WHERE dining_table_id = ? AND status IN ('accepted', 'processing', 'ready', 'paid')");
            $stmtOrder->bind_param('i', $row['dining_table_id']);
            $stmtOrder->execute();
            $resOrder = $stmtOrder->get_result()->fetch_assoc();
            $stmtOrder->close();
            
            if ((int)($resOrder['c'] ?? 0) > 0) {
                $isOccupied = true;
            } else {
                // B. Check scan state
                $stmtScanTime = $mysqli->prepare("SELECT last_scanned_at, last_cleared_at FROM dining_tables WHERE id = ?");
                $stmtScanTime->bind_param('i', $row['dining_table_id']);
                $stmtScanTime->execute();
                $resScanTime = $stmtScanTime->get_result()->fetch_assoc();
                $stmtScanTime->close();
                
                $lastScan = $resScanTime['last_scanned_at'] ?? null;
                $lastClear = $resScanTime['last_cleared_at'] ?? null;
                
                if ($lastScan !== null) {
                    if ($lastClear === null || strtotime($lastScan) > strtotime($lastClear)) {
                        $isOccupied = true;
                    }
                }
            }
        } catch (\Throwable $e) {}

        // If occupied, check if same user session
        if ($isOccupied) {
            $currentSessionTableId = $_SESSION[CustomerSessionKeys::TABLE_ID] ?? null;
            if ($currentSessionTableId !== (int) $row['dining_table_id']) {
                // Not the same session/device! Block and redirect.
                $_SESSION['scan_error_type'] = 'occupied';
                $_SESSION['scan_error_table'] = (string) $row['table_number'];
                $_SESSION['scan_error'] = "Meja " . htmlspecialchars($row['table_number']) . " sedang digunakan oleh pelanggan lain.";
                header('Location: ./index.php?page=need-scan');
                exit;
            }
        }

        // Update last_scanned_at when barcode is scanned
        try {
            $mysqli = \App\Core\Database::mysqli();
            $resScan = $mysqli->query("SHOW COLUMNS FROM dining_tables LIKE 'last_scanned_at'");
            if ($resScan->num_rows === 0) {
                $mysqli->query("ALTER TABLE dining_tables ADD COLUMN last_scanned_at DATETIME NULL");
            }
            $stmtScan = $mysqli->prepare("UPDATE dining_tables SET last_scanned_at = NOW() WHERE id = ?");
            $stmtScan->bind_param('i', $row['dining_table_id']);
            $stmtScan->execute();
            $stmtScan->close();
        } catch (\Throwable $e) {}

        if (isset($_SESSION[CustomerSessionKeys::TABLE_ID]) && (int) $_SESSION[CustomerSessionKeys::TABLE_ID] !== (int) $row['dining_table_id']) {
            self::clear();
        }

        $_SESSION[CustomerSessionKeys::TABLE_ID] = (int) $row['dining_table_id'];
        $_SESSION[CustomerSessionKeys::TABLE_NUMBER] = (string) $row['table_number'];
        $_SESSION[CustomerSessionKeys::VENUE_ID] = (int) $row['venue_id'];
        $_SESSION[CustomerSessionKeys::VENUE_NAME] = (string) $row['venue_name'];
        $_SESSION[CustomerSessionKeys::BARCODE_TOKEN] = (string) $row['barcode_token'];

        return true;
    }

    public static function contextFromSession(): ?CustomerContext
    {
        if (!isset($_SESSION[CustomerSessionKeys::TABLE_ID])) {
            return null;
        }
        $tableId = (int) $_SESSION[CustomerSessionKeys::TABLE_ID];
        if ($tableId <= 0) {
            return null;
        }

        $repo = new DiningTableRepository();
        $row = $repo->findById($tableId);
        if ($row === null) {
            self::clear();

            return null;
        }

        return CustomerContext::fromRow($row);
    }

    public static function clear(): void
    {
        unset(
            $_SESSION[CustomerSessionKeys::TABLE_ID],
            $_SESSION[CustomerSessionKeys::TABLE_NUMBER],
            $_SESSION[CustomerSessionKeys::VENUE_ID],
            $_SESSION[CustomerSessionKeys::VENUE_NAME],
            $_SESSION[CustomerSessionKeys::BARCODE_TOKEN],
            $_SESSION[CustomerSessionKeys::CART],
            $_SESSION[CustomerSessionKeys::CHECKOUT_DRAFT],
            $_SESSION[CustomerSessionKeys::LAST_ORDER_TOKEN],
        );
    }
}
