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
