<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Support\Money;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private function getMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);
        // Konfigurasi SMTP (Silakan ganti dengan konfigurasi asli Anda nantinya)
        // Untuk saat ini, Anda dapat menggunakan Mailtrap atau membiarkan menggunakan SMTP default server lokal jika ada
        
        // $mail->isSMTP();
        // $mail->Host       = 'smtp.mailtrap.io';
        // $mail->SMTPAuth   = true;
        // $mail->Username   = 'your_mailtrap_username';
        // $mail->Password   = 'your_mailtrap_password';
        // $mail->Port       = 2525;

        // Sementara menggunakan konfigurasi lokal atau mail() dasar
        $mail->setFrom('noreply@scanteen.local', 'SCanteen');
        return $mail;
    }

    public function sendReceipt(int $orderId): bool
    {
        try {
            $db = Database::mysqli();
            
            // Ambil data pesanan
            $sql = "SELECT order_number, customer_name, customer_email, total, created_at, dining_table_id 
                    FROM orders WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('i', $orderId);
            $stmt->execute();
            $order = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$order || empty($order['customer_email'])) {
                return false; // Tidak ada email, tidak perlu dikirim
            }

            // Ambil nama meja
            $tableSql = "SELECT table_number FROM dining_tables WHERE id = ?";
            $tableStmt = $db->prepare($tableSql);
            $tableStmt->bind_param('i', $order['dining_table_id']);
            $tableStmt->execute();
            $table = $tableStmt->get_result()->fetch_assoc();
            $tableStmt->close();
            $tableNum = $table ? $table['table_number'] : '-';

            // Ambil item pesanan
            $itemSql = "SELECT menu_name_snapshot, quantity, unit_price, line_subtotal 
                        FROM order_items WHERE order_id = ?";
            $itemStmt = $db->prepare($itemSql);
            $itemStmt->bind_param('i', $orderId);
            $itemStmt->execute();
            $items = $itemStmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $itemStmt->close();

            $mail = $this->getMailer();
            $mail->addAddress($order['customer_email'], $order['customer_name'] ?? 'Pelanggan');
            $mail->isHTML(true);
            $mail->Subject = 'Struk Pesanan SCanteen - ' . $order['order_number'];

            $html = $this->generateHtmlReceipt($order, $items, $tableNum);
            $mail->Body = $html;
            $mail->AltBody = "Terima kasih atas pesanan Anda di SCanteen. Order ID: {$order['order_number']}, Total: " . Money::formatIdr((float)$order['total']);

            return $mail->send();
        } catch (\Throwable $e) {
            // Log error if needed
            error_log('Gagal mengirim struk email: ' . $e->getMessage());
            return false;
        }
    }

    private function generateHtmlReceipt(array $order, array $items, string $tableNum): string
    {
        $date = date('d M Y H:i', strtotime($order['created_at']));
        $totalFmt = Money::formatIdr((float)$order['total']);
        $name = htmlspecialchars($order['customer_name'] ?? 'Pelanggan');
        $orderNum = htmlspecialchars($order['order_number']);

        $itemsHtml = '';
        foreach ($items as $item) {
            $itemName = htmlspecialchars($item['menu_name_snapshot']);
            $qty = (int)$item['quantity'];
            $subtotal = Money::formatIdr((float)$item['line_subtotal']);
            
            $itemsHtml .= "
                <tr>
                    <td style='padding: 8px; border-bottom: 1px solid #eee;'>{$itemName} x{$qty}</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee; text-align: right;'>{$subtotal}</td>
                </tr>
            ";
        }

        return "
        <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;'>
            <div style='background-color: #7B0009; color: white; padding: 20px; text-align: center;'>
                <h1 style='margin: 0; font-size: 24px;'>SCanteen</h1>
                <p style='margin: 5px 0 0; opacity: 0.8;'>Struk Pesanan Anda</p>
            </div>
            <div style='padding: 20px;'>
                <p>Halo <strong>{$name}</strong>,</p>
                <p>Terima kasih telah memesan di SCanteen. Berikut adalah rincian pesanan Anda:</p>
                
                <table style='width: 100%; margin-bottom: 20px; font-size: 14px;'>
                    <tr>
                        <td style='color: #666;'>Order ID</td>
                        <td style='text-align: right; font-weight: bold;'>{$orderNum}</td>
                    </tr>
                    <tr>
                        <td style='color: #666;'>Tanggal</td>
                        <td style='text-align: right;'>{$date}</td>
                    </tr>
                    <tr>
                        <td style='color: #666;'>Meja</td>
                        <td style='text-align: right; font-weight: bold;'>{$tableNum}</td>
                    </tr>
                </table>

                <h3 style='margin-bottom: 10px; border-bottom: 2px solid #7B0009; padding-bottom: 5px; color: #7B0009;'>Rincian Item</h3>
                <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>
                    {$itemsHtml}
                    <tr>
                        <td style='padding: 12px 8px; font-weight: bold; font-size: 16px;'>TOTAL</td>
                        <td style='padding: 12px 8px; font-weight: bold; font-size: 16px; text-align: right; color: #7B0009;'>{$totalFmt}</td>
                    </tr>
                </table>
                
                <p style='text-align: center; color: #666; font-size: 12px; margin-top: 30px;'>
                    Pesanan ini telah lunas dibayar.<br>
                    Harap simpan email ini sebagai bukti pembayaran yang sah.
                </p>
            </div>
        </div>
        ";
    }
}
