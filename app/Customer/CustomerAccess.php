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
        if (!isset($_GET['t'])) {
            return false;
        }
        $token = trim((string) $_GET['t']);
        if ($token === '') {
            return false;
        }

        $repo = new DiningTableRepository();
        $row = $repo->findActiveByToken($token);
        if ($row === null) {
            return false;
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
