<?php

declare(strict_types=1);

namespace App\Services;

use App\Customer\CustomerSessionKeys;

final class CheckoutDraftService
{
    /**
     * @return array{name:?string, email:?string, dining_type:string}|null
     */
    public function get(): ?array
    {
        $raw = $_SESSION[CustomerSessionKeys::CHECKOUT_DRAFT] ?? null;
        if (!is_array($raw)) {
            return null;
        }

        return [
            'name' => isset($raw['name']) ? trim((string) $raw['name']) : null,
            'email' => isset($raw['email']) ? trim((string) $raw['email']) : null,
            'dining_type' => ($raw['dining_type'] ?? 'dine_in') === 'take_away' ? 'take_away' : 'dine_in',
        ];
    }

    /**
     * @param array{name?:string, email?:string, dining_type?:string} $data
     */
    public function save(array $data): void
    {
        $name = isset($data['name']) ? trim((string) $data['name']) : '';
        $email = isset($data['email']) ? trim((string) $data['email']) : '';
        $dining = ($data['dining_type'] ?? 'dine_in') === 'take_away' ? 'take_away' : 'dine_in';

        $_SESSION[CustomerSessionKeys::CHECKOUT_DRAFT] = [
            'name' => $name === '' ? null : $name,
            'email' => $email === '' ? null : $email,
            'dining_type' => $dining,
        ];
    }

    public function clear(): void
    {
        unset($_SESSION[CustomerSessionKeys::CHECKOUT_DRAFT]);
    }
}
