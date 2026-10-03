<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Models\Customer;
use App\Models\Order;

final class AccountController extends Controller
{
    public function index(): void
    {
        $customer = Auth::customer();
        if ($customer !== null) {
            redirect('account/orders');
        }

        $this->view('front/account', [
            'title' => t('account'),
            'navPages' => $this->navPages(),
        ]);
    }

    public function login(): void
    {
        $this->guardCsrf();

        $phone = (string) post('phone', '');
        $password = (string) post('password', '');
        $customer = Customer::findByPhone($phone);

        if ($customer === null || $customer['password_hash'] === null || !password_verify($password, (string) $customer['password_hash'])) {
            flash('error', t('err_invalid_credentials'));
            redirect('account');
        }

        Auth::loginCustomer((int) $customer['id']);
        redirect('account/orders');
    }

    public function register(): void
    {
        $this->guardCsrf();

        $name = (string) post('name', '');
        $phone = (string) post('phone', '');
        $password = (string) post('password', '');
        $confirm = (string) post('password_confirm', '');

        if ($name === '' || $phone === '' || $password === '') {
            flash('error', t('err_required'));
            redirect('account');
        }
        if (strlen($password) < 6) {
            flash('error', t('err_password_short'));
            redirect('account');
        }
        if ($password !== $confirm) {
            flash('error', t('err_password_match'));
            redirect('account');
        }

        $existing = Customer::findByPhone($phone);
        if ($existing !== null) {
            if ($existing['password_hash'] !== null) {
                flash('error', t('err_phone_exists'));
                redirect('account');
            }
            /* زبون سجّل طلبًا سابقًا كضيف — تفعيل الحساب */
            \App\Core\Database::exec(
                'UPDATE customers SET password_hash = ?, name = ? WHERE id = ?',
                [password_hash($password, PASSWORD_DEFAULT), $name, $existing['id']]
            );
            Auth::loginCustomer((int) $existing['id']);
            redirect('account/orders');
        }

        $id = Customer::create($name, $phone, $password);
        Auth::loginCustomer($id);
        redirect('account/orders');
    }

    public function logout(): void
    {
        Auth::logoutCustomer();
        redirect('');
    }

    public function orders(): void
    {
        $customer = Auth::customer();
        if ($customer === null) {
            redirect('account');
        }

        $this->view('front/orders', [
            'title' => t('my_orders'),
            'customer' => $customer,
            'orders' => Customer::orders((int) $customer['id']),
            'navPages' => $this->navPages(),
        ]);
    }

    public function order(string $id): void
    {
        $customer = Auth::customer();
        if ($customer === null) {
            redirect('account');
        }

        $order = Order::find((int) $id);
        if ($order === null || (int) $order['customer_id'] !== (int) $customer['id']) {
            abort(404);
        }

        $this->view('front/order', [
            'title' => t('order_number') . ' ' . $order['order_number'],
            'order' => $order,
            'items' => Order::items((int) $order['id']),
            'navPages' => $this->navPages(),
        ]);
    }
}
