<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Auth;
use App\Models\Order;

final class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireArea('dashboard');

        $this->adminView('admin/dashboard', [
            'title' => 'لوحة التحكم',
            'stats' => Order::stats(),
            'latestOrders' => Order::adminList(['status' => '']) ?: [],
        ]);
    }
}
