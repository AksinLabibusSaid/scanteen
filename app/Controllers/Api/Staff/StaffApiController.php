<?php

declare(strict_types=1);

namespace App\Controllers\Api\Staff;

use App\Core\Controller;
use App\Staff\StaffAuth;

abstract class StaffApiController extends Controller
{
    protected function requireAuth(): void
    {
        if (!StaffAuth::check()) {
            $this->json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }
    }

    /** @param list<string> $roles */
    protected function requireRoles(array $roles): void
    {
        $this->requireAuth();
        $role = StaffAuth::role();
        if ($role === null || !in_array($role, $roles, true)) {
            $this->json(['ok' => false, 'error' => 'Forbidden'], 403);
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireRoles(['admin']);
    }

    protected function getJsonData(): array
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['ok' => false, 'error' => 'Method not allowed'], 405);
        }

        $raw = file_get_contents('php://input');
        $data = is_string($raw) ? json_decode($raw, true) : null;
        if (!is_array($data)) {
            $this->json(['ok' => false, 'error' => 'JSON tidak valid'], 400);
        }
        return $data;
    }

    protected function json(mixed $data, int $code = 200): void
    {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    abstract public function handle(): void;
}
