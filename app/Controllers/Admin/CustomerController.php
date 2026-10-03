<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Auth;
use App\Models\Customer;
use App\Models\Order;

final class CustomerController extends Controller
{
    public function index(): void
    {
        Auth::requireArea('orders');

        $this->adminView('admin/customers', [
            'title' => 'العملاء',
            'customers' => Customer::all((string) get('q', '')),
            'q' => (string) get('q', ''),
        ]);
    }

    public function show(string $id): void
    {
        Auth::requireArea('orders');

        $customer = Customer::find((int) $id);
        if ($customer === null) {
            abort(404);
        }

        $this->adminView('admin/customer', [
            'title' => 'عميل: ' . $customer['name'],
            'customer' => $customer,
            'orders' => Customer::orders((int) $id),
        ]);
    }
}
