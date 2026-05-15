<?php

declare(strict_types=1);

namespace App\Staff;

use App\Repositories\StaffUserRepository;

final class StaffAuth
{
    public static function attemptLogin(string $email, string $password): bool
    {
        $email = trim($email);
        if ($email === '' || $password === '') {
            return false;
        }

        $repo = new StaffUserRepository();
        $user = $repo->findActiveByEmail($email);
        if ($user === null) {
            return false;
        }

        $hash = (string) $user['password_hash'];
        if (!password_verify($password, $hash)) {
            return false;
        }

        $_SESSION[StaffSessionKeys::USER_ID] = (int) $user['id'];
        $_SESSION[StaffSessionKeys::VENUE_ID] = (int) $user['venue_id'];
        $_SESSION[StaffSessionKeys::EMAIL] = (string) $user['email'];
        $_SESSION[StaffSessionKeys::NAME] = (string) $user['name'];
        $_SESSION[StaffSessionKeys::ROLE] = (string) $user['role'];
        $wid = $user['warung_id'];
        $_SESSION[StaffSessionKeys::WARUNG_ID] = $wid !== null ? (int) $wid : null;

        return true;
    }

    public static function logout(): void
    {
        foreach ([
            StaffSessionKeys::USER_ID,
            StaffSessionKeys::VENUE_ID,
            StaffSessionKeys::EMAIL,
            StaffSessionKeys::NAME,
            StaffSessionKeys::ROLE,
            StaffSessionKeys::WARUNG_ID,
        ] as $k) {
            unset($_SESSION[$k]);
        }
    }

    public static function check(): bool
    {
        return isset($_SESSION[StaffSessionKeys::USER_ID], $_SESSION[StaffSessionKeys::ROLE]);
    }

    public static function role(): ?string
    {
        if (!self::check()) {
            return null;
        }

        return (string) $_SESSION[StaffSessionKeys::ROLE];
    }

    public static function venueId(): ?int
    {
        if (!self::check()) {
            return null;
        }

        return (int) $_SESSION[StaffSessionKeys::VENUE_ID];
    }

    public static function warungId(): ?int
    {
        if (!self::check()) {
            return null;
        }

        $v = $_SESSION[StaffSessionKeys::WARUNG_ID] ?? null;

        return $v === null ? null : (int) $v;
    }

    public static function userId(): ?int
    {
        if (!self::check()) {
            return null;
        }

        return (int) $_SESSION[StaffSessionKeys::USER_ID];
    }

    public static function userName(): string
    {
        return self::check() ? (string) ($_SESSION[StaffSessionKeys::NAME] ?? '') : '';
    }

    public static function userEmail(): string
    {
        return self::check() ? (string) ($_SESSION[StaffSessionKeys::EMAIL] ?? '') : '';
    }
}
