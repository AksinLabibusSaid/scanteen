<?php

declare(strict_types=1);

namespace App\Services;

use App\Customer\CustomerSessionKeys;

/**
 * Keranjang disimpan di session PHP (tanpa login customer).
 *
 * @phpstan-type CartLine array{menu_id:int, qty:int, note:string}
 */
final class CartService
{
    /**
     * @return list<CartLine>
     */
    public function lines(): array
    {
        if (!isset($_SESSION[CustomerSessionKeys::CART]) || !is_array($_SESSION[CustomerSessionKeys::CART])) {
            return [];
        }

        $out = [];
        foreach ($_SESSION[CustomerSessionKeys::CART] as $row) {
            if (!is_array($row)) {
                continue;
            }
            $mid = isset($row['menu_id']) ? (int) $row['menu_id'] : 0;
            $qty = isset($row['qty']) ? (int) $row['qty'] : 0;
            if ($mid <= 0 || $qty <= 0) {
                continue;
            }
            $note = isset($row['note']) ? trim((string) $row['note']) : '';
            $out[] = ['menu_id' => $mid, 'qty' => $qty, 'note' => $note];
        }

        return $out;
    }

    public function clear(): void
    {
        $_SESSION[CustomerSessionKeys::CART] = [];
    }

    /**
     * @param CartLine $line
     */
    public function add(int $menuId, int $qty = 1, string $note = ''): void
    {
        if ($menuId <= 0 || $qty <= 0) {
            return;
        }
        $cart = $_SESSION[CustomerSessionKeys::CART] ?? [];
        if (!is_array($cart)) {
            $cart = [];
        }
        $found = false;
        foreach ($cart as &$row) {
            if (!is_array($row)) {
                continue;
            }
            if ((int) ($row['menu_id'] ?? 0) === $menuId) {
                $row['qty'] = (int) ($row['qty'] ?? 0) + $qty;
                $row['note'] = trim((string) ($row['note'] ?? '')) !== '' ? trim((string) $row['note']) : trim($note);
                $found = true;
                break;
            }
        }
        unset($row);
        if (!$found) {
            $cart[] = ['menu_id' => $menuId, 'qty' => $qty, 'note' => trim($note)];
        }
        $_SESSION[CustomerSessionKeys::CART] = $cart;
    }

    public function setQty(int $menuId, int $qty, string $note = ''): void
    {
        if ($menuId <= 0) {
            return;
        }
        $cart = $_SESSION[CustomerSessionKeys::CART] ?? [];
        if (!is_array($cart)) {
            $cart = [];
        }
        $next = [];
        foreach ($cart as $row) {
            if (!is_array($row)) {
                continue;
            }
            if ((int) ($row['menu_id'] ?? 0) !== $menuId) {
                $next[] = $row;
            }
        }
        if ($qty > 0) {
            $next[] = ['menu_id' => $menuId, 'qty' => $qty, 'note' => trim($note)];
        }
        $_SESSION[CustomerSessionKeys::CART] = $next;
    }

    public function remove(int $menuId): void
    {
        $this->setQty($menuId, 0);
    }
}
