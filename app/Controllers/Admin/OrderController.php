<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\Order;

final class OrderController extends Controller
{
    public function index(): void
    {
        Auth::requireArea('orders');

        $this->adminView('admin/orders', [
            'title' => 'الطلبات',
            'orders' => Order::adminList([
                'status' => (string) get('status', ''),
                'q' => (string) get('q', ''),
                'date' => (string) get('date', ''),
            ]),
            'filters' => [
                'status' => (string) get('status', ''),
                'q' => (string) get('q', ''),
                'date' => (string) get('date', ''),
            ],
        ]);
    }

    public function show(string $id): void
    {
        Auth::requireArea('orders');

        $order = Order::find((int) $id);
        if ($order === null) {
            abort(404);
        }

        $this->adminView('admin/order', [
            'title' => 'طلب ' . $order['order_number'],
            'order' => $order,
            'items' => Order::items((int) $id),
            'events' => Order::events((int) $id),
            'statuses' => Order::STATUSES,
        ]);
    }

    public function status(): void
    {
        Auth::requireArea('orders');
        Csrf::guard();

        $id = (int) post('id', 0);
        $status = (string) post('status', '');
        $note = (string) post('note', '');

        if (Order::changeStatus($id, $status, Auth::admin()['id'] ?? null, $note)) {
            log_activity(Auth::admin()['id'] ?? null, 'order', $id, 'status:' . $status, $note);
            flash('success', 'تم تحديث حالة الطلب');
        } else {
            flash('error', 'تعذر تحديث الحالة');
        }
        redirect('admin/orders/' . $id);
    }

    public function delete(): void
    {
        Auth::requireArea('orders');
        Csrf::guard();

        $id = (int) post('id', 0);
        if ($id > 0) {
            Order::delete($id);
            log_activity(Auth::admin()['id'] ?? null, 'order', $id, 'delete', 'حذف طلب');
            flash('success', 'تم حذف الطلب');
        }
        redirect('admin/orders');
    }

    /** فاتورة قابلة للطباعة / حفظ PDF */
    public function invoice(string $id): void
    {
        Auth::requireArea('orders');

        $order = Order::find((int) $id);
        if ($order === null) {
            abort(404);
        }

        \App\Core\View::render('admin/invoice', [
            'title' => 'فاتورة ' . $order['order_number'],
            'order' => $order,
            'items' => Order::items((int) $id),
        ], 'layouts/print');
    }
}
