<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

final class MailService
{
    // TODO: Isi kredensial SMTP Anda di sini atau pindahkan ke file config
    private string $host = 'smtp.gmail.com'; 
    private int $port = 587;
    private string $username = 'grenlly21@gmail.com'; 
    private string $password = 'catblunlkkrwzuky'; 
    private string $fromEmail = 'grenlly21@gmail.com';
    private string $fromName = 'Scanteen';

    /**
     * Mengirim email rincian pembayaran.
     */
    public function sendReceipt(string $toEmail, string $customerName, array $order, array $items): bool
    {
        if ($toEmail === '' || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $this->host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->username;
            $mail->Password   = $this->password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $this->port;

            // Recipients
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($toEmail, $customerName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Rincian Pembayaran Scanteen - ' . $order['order_number'];
            
            // Build Body using tables and inline styles for email compatibility
            $groups = (new \App\Repositories\OrderRepository())->groupItemsByWarung((int)$order['id']);
            
            $tableNum = $order['table_number'] ?? '?';
            $dateStr = date('d M Y, H:i', strtotime($order['created_at']));
            $pm = $order['payment_method'] ?? '';
            $payLabel = match ($pm) {
                'qris' => 'QRIS',
                'midtrans' => 'Midtrans',
                default => 'Bayar di kasir',
            };
            $dineLabel = (($order['dining_type'] ?? '') === 'take_away') ? 'Take away' : 'Dine-in';

            $body = "
<table width='100%' cellpadding='0' cellspacing='0' style='background-color: #F9FAFB; padding: 20px; font-family: Arial, sans-serif;'>
    <tr>
        <td align='center'>
            <table width='380' cellpadding='0' cellspacing='0' style='background-color: #FFFFFF; border-radius: 12px; border: 1px solid #E5E7EB; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);'>
                <!-- Header -->
                <tr>
                    <td align='center' style='padding-bottom: 20px;'>
                        <img src='https://api.builder.io/api/v1/image/assets/TEMP/7c63d6d2f2848f53c163ee7f930ee5fd70edd4d6?width=80' width='60' style='display: block; margin-bottom: 10px;'>
                        <h1 style='color: #800000; font-size: 14px; font-weight: bold; margin: 0; text-transform: uppercase;'>Scanteen</h1>
                        <p style='color: #6B7280; font-size: 10px; margin: 5px 0 0 0; text-transform: uppercase;'>Kantin Demo</p>
                    </td>
                </tr>
                
                <tr><td style='border-top: 2px dashed #E5E7EB; padding-bottom: 20px;'></td></tr>
                
                <!-- Customer Info -->
                <tr>
                    <td style='padding-bottom: 20px;'>
                        <h2 style='color: #261817; font-size: 11px; font-weight: bold; text-transform: uppercase; margin: 0 0 10px 0;'>Informasi Pelanggan</h2>
                        <table width='100%' cellpadding='0' cellspacing='0' style='font-size: 12px;'>
                            <tr>
                                <td style='color: #6B7280; font-weight: bold;'>Nama</td>
                                <td align='right' style='color: #1A1C1A; font-weight: bold;'>".htmlspecialchars((string)$customerName)."</td>
                            </tr>
                            <tr>
                                <td style='color: #6B7280; font-weight: bold; padding-top: 5px;'>Email</td>
                                <td align='right' style='color: #1A1C1A; font-weight: bold; padding-top: 5px;'>".htmlspecialchars((string)$toEmail)."</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                
                <!-- Order Details -->
                <tr>
                    <td style='padding-bottom: 20px;'>
                        <h2 style='color: #261817; font-size: 11px; font-weight: bold; text-transform: uppercase; margin: 0 0 10px 0;'>Detail Pesanan</h2>
                        <table width='100%' cellpadding='0' cellspacing='0' style='font-size: 12px;'>
                            <tr>
                                <td style='color: #6B7280; font-weight: bold;'>ID Pesanan</td>
                                <td align='right' style='color: #1A1C1A; font-weight: bold;'>".htmlspecialchars((string)$order['order_number'])."</td>
                            </tr>
                            <tr>
                                <td style='color: #6B7280; font-weight: bold; padding-top: 5px;'>Tanggal</td>
                                <td align='right' style='color: #1A1C1A; padding-top: 5px;'>".$dateStr."</td>
                            </tr>
                            <tr>
                                <td style='color: #6B7280; font-weight: bold; padding-top: 5px;'>Lokasi</td>
                                <td align='right' style='color: #1A1C1A; padding-top: 5px;'>Meja ".$tableNum."</td>
                            </tr>
                            <tr>
                                <td style='color: #6B7280; font-weight: bold; padding-top: 5px;'>Tipe</td>
                                <td align='right' style='color: #1A1C1A; padding-top: 5px;'>".$dineLabel."</td>
                            </tr>
                            <tr>
                                <td style='color: #6B7280; font-weight: bold; padding-top: 5px;'>Metode</td>
                                <td align='right' style='color: #1A1C1A; font-weight: bold; padding-top: 5px;'>".$payLabel."</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                
                <tr><td style='border-top: 2px dashed #E5E7EB; padding-bottom: 20px;'></td></tr>
                
                <!-- Ordered Items -->
                <tr>
                    <td style='padding-bottom: 20px;'>
                        <h2 style='color: #261817; font-size: 11px; font-weight: bold; text-transform: uppercase; margin: 0 0 10px 0;'>Daftar Pesanan</h2>";
                        
            foreach ($groups as $g) {
                $body .= "<h3 style='color: #261817; font-size: 12px; font-weight: bold; text-transform: uppercase; margin: 10px 0 5px 0;'>".htmlspecialchars((string)$g['warung_name'])."</h3>";
                foreach ($g['items'] as $it) {
                    $lineTotal = number_format((float)$it['line_subtotal'], 0, ',', '.');
                    $unit = number_format((float)$it['unit_price'], 0, ',', '.');
                    $body .= "
                        <table width='100%' cellpadding='0' cellspacing='0' style='font-size: 12px; margin-bottom: 10px;'>
                            <tr>
                                <td style='color: #1A1C1A; font-weight: bold;'>".htmlspecialchars((string)$it['menu_name_snapshot'])."</td>
                                <td align='right' style='color: #1A1C1A; font-weight: bold;'>Rp ".$lineTotal."</td>
                            </tr>
                            <tr>
                                <td style='color: #6B7280; font-size: 11px;'>".(int)$it['quantity']."x Rp ".$unit."</td>
                                <td></td>
                            </tr>
                        </table>";
                }
            }
            
            $total = number_format((float)$order['total'], 0, ',', '.');
            $body .= "
                    </td>
                </tr>
                
                <tr><td style='border-top: 2px dashed #E5E7EB; padding-bottom: 20px;'></td></tr>
                
                <!-- Total -->
                <tr>
                    <td>
                        <table width='100%' cellpadding='0' cellspacing='0' style='font-size: 14px; font-weight: bold;'>
                            <tr>
                                <td style='color: #261817; text-transform: uppercase;'>Total Akhir</td>
                                <td align='right' style='color: #800000;'>Rp ".$total."</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                
                <tr><td align='center' style='padding-top: 30px; font-size: 11px; color: #6B7280;'>Terima kasih atas pesanan Anda!</td></tr>
            </table>
        </td>
    </tr>
</table>";

            $mail->Body = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            throw new \Exception("Mailer Error: " . $mail->ErrorInfo . " (" . $e->getMessage() . ")");
        }
    }
}
