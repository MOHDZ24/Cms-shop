<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Cart;
use App\Models\Order;
use App\Models\Wilaya;

final class CheckoutController extends Controller
{
    public function index(): void
    {
        $items = Cart::details();
        if ($items === []) {
            redirect('cart');
        }

        $customer = Auth::customer();
        $this->view('front/checkout', [
            'title' => t('checkout'),
            'items' => $items,
            'subtotal' => Cart::subtotal(),
            'wilayas' => Wilaya::all(),
            'customer' => $customer,
            'navPages' => $this->navPages(),
        ]);
    }

    public function place(): void
    {
        $this->guardCsrf();

        $items = Cart::details();
        if ($items === []) {
            flash('error', t('err_cart_empty'));
            redirect('cart');
        }

        $name = (string) post('name', '');
        $phone = (string) post('phone', '');
        $phone2 = (string) post('phone2', '');
        $address = (string) post('address', '');
        $wilayaId = (int) post('wilaya_id', 0);
        $notes = (string) post('notes', '');

        /* التحقق من المدخلات */
        if ($name === '' || $phone === '' || $address === '') {
            flash('error', t('err_required'));
            redirect('checkout');
        }
        if (!preg_match('/^(0|\+213)[0-9\s\-]{8,12}$/', $phone)) {
            flash('error', t('err_phone'));
            redirect('checkout');
        }
        $wilaya = Wilaya::find($wilayaId);
        if ($wilaya === null || (int) $wilaya['active'] !== 1) {
            flash('error', t('err_wilaya'));
            redirect('checkout');
        }

        /* ربط الطلب بحساب الزبون إن كان مسجّلًا (وإلا يُنشأ/يُحدَّث حساب صامت برقم الهاتف) */
        $customerId = Auth::customer()['id'] ?? null;
        if ($customerId === null) {
            try {
                $customerId = \App\Models\Customer::upsertFromOrder($name, $phone, $address);
            } catch (\Throwable) {
                $customerId = null;
            }
        }

        try {
            $orderNumber = Order::create(
                [
                    'customer_id' => $customerId,
                    'name' => $name,
                    'phone' => $phone,
                    'phone2' => $phone2 !== '' ? $phone2 : null,
                    'address' => $address,
                    'notes' => $notes !== '' ? $notes : null,
                ],
                $items,
                $wilaya
            );
        } catch (\RuntimeException) {
            flash('error', t('err_stock'));
            redirect('cart');
        }

        Cart::clear();
        redirect('order/success/' . $orderNumber);
    }

    public function success(string $number): void
    {
        $order = Order::findByNumber($number);
        if ($order === null) {
            abort(404);
        }

        $this->view('front/success', [
            'title' => t('order_success'),
            'order' => $order,
            'navPages' => $this->navPages(),
        ]);
    }
}
