<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;

final class AdminController extends Controller
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Simple auth check (using existing logic)
        require_once SCANTEEN_ROOT . '/app/Staff/staff_portal_guard.php';
        if (function_exists('scanteen_staff_require_portal')) {
            scanteen_staff_require_portal('admin');
        }
    }

    public function dashboard(): void
    {
        $this->render('admin.dashboard', [
            'pageTitle' => 'Dashboard',
            'activePage' => 'dashboard'
        ]);
    }

    public function orders(): void
    {
        $this->render('admin.orders', [
            'pageTitle' => 'Order Management',
            'activePage' => 'orders'
        ]);
    }

    public function tenants(): void
    {
        $this->render('admin.tenants', [
            'pageTitle' => 'Tenant Management',
            'activePage' => 'tenants'
        ]);
    }

    public function menus(): void
    {
        $this->render('admin.menus', [
            'pageTitle' => 'Menu Management',
            'activePage' => 'menus'
        ]);
    }

    public function users(): void
    {
        $this->render('admin.users', [
            'pageTitle' => 'User Management',
            'activePage' => 'users'
        ]);
    }
}
