<?php
declare(strict_types=1);

namespace App\Core;

/**
 * سلة التسوق (محفوظة في الجلسة) — الأسعار تُقرأ من قاعدة البيانات دائمًا
 */
final class Cart
{
    /** @return array<int,int> product_id => qty */
    public static function items(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    public static function count(): int
    {
        return array_sum(self::items());
    }

    public static function add(int $productId, int $qty = 1): void
    {
        $cart = self::items();
        $cart[$productId] = ($cart[$productId] ?? 0) + max(1, $qty);
        $_SESSION['cart'] = $cart;
    }

    public static function update(int $productId, int $qty): void
    {
        $cart = self::items();
        if ($qty <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $qty;
        }
        $_SESSION['cart'] = $cart;
    }

    public static function clear(): void
    {
        unset($_SESSION['cart']);
    }

    /** تفاصيل السلة الحالية من قاعدة البيانات */
    public static function details(): array
    {
        $rows = [];
        foreach (self::items() as $productId => $qty) {
            $product = Database::row(
                'SELECT p.*, COALESCE(t.name, td.name) AS name
                 FROM products p
                 LEFT JOIN product_translations t ON t.product_id = p.id AND t.lang = ?
                 LEFT JOIN product_translations td ON td.product_id = p.id AND td.lang = ?
                 WHERE p.id = ? AND p.active = 1',
                [Lang::code(), setting('default_lang', 'ar'), $productId]
            );
            if ($product === null) {
                continue;
            }
            $price = (float) ($product['sale_price'] ?? $product['price']);
            $rows[] = [
                'product_id' => (int) $productId,
                'name' => (string) $product['name'],
                'image' => $product['image'],
                'price' => $price,
                'qty' => (int) $qty,
                'total' => $price * $qty,
                'stock' => (int) $product['stock'],
            ];
        }
        return $rows;
    }

    public static function subtotal(): float
    {
        $sum = 0.0;
        foreach (self::details() as $row) {
            $sum += $row['total'];
        }
        return $sum;
    }
}
