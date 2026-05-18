<?php

declare(strict_types=1);

namespace App\Core;

use mysqli;
use mysqli_sql_exception;

final class Database
{
    private static ?mysqli $connection = null;

    public static function boot(): void
    {
        if (self::$connection instanceof mysqli) {
            return;
        }

        if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_PASS') || !defined('DB_NAME')) {
            throw new \RuntimeException('Database constants are not defined.');
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $mysqli->set_charset('utf8mb4');
        
        // Self-healing migration to support 'completed' status in order_warung_fulfillment
        try {
            $mysqli->query("ALTER TABLE order_warung_fulfillment MODIFY COLUMN status ENUM('new','preparing','ready','completed') NOT NULL DEFAULT 'new'");
        } catch (\Throwable $e) {
            // Ignore if column is already updated or other transient errors
        }

        // Self-healing migration to support 'phone' column in staff_users
        try {
            $mysqli->query("ALTER TABLE staff_users ADD COLUMN phone VARCHAR(24) DEFAULT NULL AFTER name");
        } catch (\Throwable $e) {
            // Ignore if column already exists or other transient errors
        }

        // Self-healing migration to support settings columns in venues
        $venueColumns = [
            'tax_percent' => "ADD COLUMN tax_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00",
            'service_fee_percent' => "ADD COLUMN service_fee_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00",
            'payment_expiry_minutes' => "ADD COLUMN payment_expiry_minutes INT NOT NULL DEFAULT 15",
            'maintenance_mode' => "ADD COLUMN maintenance_mode TINYINT(1) NOT NULL DEFAULT 0",
            'maintenance_message' => "ADD COLUMN maintenance_message TEXT DEFAULT NULL",
            'midtrans_client_key' => "ADD COLUMN midtrans_client_key VARCHAR(255) DEFAULT NULL",
            'midtrans_server_key' => "ADD COLUMN midtrans_server_key VARCHAR(255) DEFAULT NULL",
            'is_production' => "ADD COLUMN is_production TINYINT(1) NOT NULL DEFAULT 0",
            'allow_qris' => "ADD COLUMN allow_qris TINYINT(1) NOT NULL DEFAULT 1",
            'allow_cash' => "ADD COLUMN allow_cash TINYINT(1) NOT NULL DEFAULT 1",
            'allow_debit' => "ADD COLUMN allow_debit TINYINT(1) NOT NULL DEFAULT 1",
            'operating_hours' => "ADD COLUMN operating_hours TEXT DEFAULT NULL",
        ];

        foreach ($venueColumns as $col => $sql) {
            try {
                $mysqli->query("ALTER TABLE venues $sql");
            } catch (\Throwable $e) {
                // Ignore if column already exists or other transient errors
            }
        }

        // Self-healing migration to clean up empty or duplicate slugs in warungs
        try {
            $res = $mysqli->query("SELECT id, name, slug FROM warungs");
            $seenSlugs = [];
            while ($row = $res->fetch_assoc()) {
                $id = (int) $row['id'];
                $name = trim((string) $row['name']);
                $slug = trim((string) $row['slug']);
                
                $slugify = function(string $str) {
                    $s = strtolower(trim($str));
                    $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? 'warung';
                    $s = trim($s, '-');
                    return $s !== '' ? $s : 'warung';
                };
                
                $needsUpdate = false;
                if ($slug === '' || in_array($slug, $seenSlugs, true) || preg_match('/[^a-z0-9\-]/', $slug)) {
                    $needsUpdate = true;
                }
                
                if ($needsUpdate) {
                    $base = $slugify($name !== '' ? $name : 'warung');
                    $newSlug = $base . '-' . substr(bin2hex(random_bytes(3)), 0, 6);
                    $stmt = $mysqli->prepare("UPDATE warungs SET slug = ? WHERE id = ?");
                    $stmt->bind_param('si', $newSlug, $id);
                    $stmt->execute();
                    $stmt->close();
                    $slug = $newSlug;
                }
                
                $seenSlugs[] = $slug;
            }
        } catch (\Throwable $e) {
            // Ignore any issues
        }

        self::$connection = $mysqli;
    }

    public static function mysqli(): mysqli
    {
        self::boot();

        return self::$connection;
    }

    /**
     * @template T
     * @param callable(mysqli): T $callback
     * @return T
     */
    public static function transaction(callable $callback)
    {
        $db = self::mysqli();
        try {
            $db->begin_transaction();
            $result = $callback($db);
            $db->commit();

            return $result;
        } catch (mysqli_sql_exception $e) {
            $db->rollback();
            throw $e;
        }
    }
}
