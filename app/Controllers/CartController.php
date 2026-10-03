<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Cart;

final class CartController extends Controller
{
    public function index(): void
    {
        $this->view('front/cart', [
            'title' => t('cart'),
            'items' => Cart::details(),
            'subtotal' => Cart::subtotal(),
            'navPages' => $this->navPages(),
        ]);
    }

    public function add(): void
    {
        $this->guardCsrf();
        $productId = (int) post('product_id', 0);
        $qty = max(1, (int) post('qty', 1));

        if ($productId > 0) {
            Cart::add($productId, $qty);
        }

        $redirect = (string) post('redirect', '');
        redirect($redirect !== '' ? $redirect : 'cart');
    }

    public function update(): void
    {
        $this->guardCsrf();
        $quantities = $_POST['qty'] ?? [];
        if (is_array($quantities)) {
            foreach ($quantities as $productId => $qty) {
                Cart::update((int) $productId, (int) $qty);
            }
        }
        flash('success', 'تم تحديث السلة');
        redirect('cart');
    }

    public function remove(): void
    {
        $this->guardCsrf();
        Cart::update((int) post('product_id', 0), 0);
        redirect('cart');
    }
}
