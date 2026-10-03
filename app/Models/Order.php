<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * الطلبات — الإنشاء (كضيف أو بحساب) والإدارة
 */
final class Order
{
    public const STATUSES = ['new', 'confirmed', 'preparing', 'shipped', 'delivered', 'cancelled', 'returned'];

    /** إنشاء طلب من السلة — الأسعار تُحسب من قاعدة البيانات فقط */
    public static function create(array $customer, array $cartItems, array $wilaya): string
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();

        try {
            $subtotal = 0.0;
            $items = [];

            foreach ($cartItems as $item) {
                $product = Database::row('SELECT * FROM products WHERE id = ? AND active = 1', [$item['product_id']]);
                if ($product === null || (int) $product['stock'] < $item['qty']) {
                    throw new \RuntimeException('stock');
                }
                $price = (float) ($product['sale_price'] ?? $product['price']);
                $lineTotal = $price * $item['qty'];
                $subtotal += $lineTotal;
                $items[] = [
                    'product_id' => (int) $product['id'],
                    'name' => $item['name'],
                    'price' => $price,
                    'qty' => (int) $item['qty'],
                    'total' => $lineTotal,
                ];
            }

            $deliveryPrice = (float) $wilaya['delivery_price'];
            $total = $subtotal + $deliveryPrice;
            $orderNumber = 'NS-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

            $orderId = (int) Database::insert(
                'INSERT INTO orders (order_number, customer_id, customer_name, phone, phone2, address,
                    wilaya_id, wilaya_name, delivery_price, subtotal, total, status, payment_method, notes, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $orderNumber,
                    $customer['customer_id'] ?? null,
                    $customer['name'],
                    $customer['phone'],
                    $customer['phone2'] ?? null,
                    $customer['address'],
                    (int) $wilaya['id'],
                    \App\Models\Wilaya::name($wilaya, \App\Core\Lang::code()),
                    $deliveryPrice,
                    $subtotal,
                    $total,
                    'new',
                    'cod',
                    $customer['notes'] ?? null,
                    now(),
                    now(),
                ]
            );

            foreach ($items as $item) {
                Database::exec(
                    'INSERT INTO order_items (order_id, product_id, product_name, price, qty, total) VALUES (?, ?, ?, ?, ?, ?)',
                    [$orderId, $item['product_id'], $item['name'], $item['price'], $item['qty'], $item['total']]
                );
                Database::exec('UPDATE products SET stock = stock - ? WHERE id = ?', [$item['qty'], $item['product_id']]);
            }

            Database::exec(
                'INSERT INTO order_events (order_id, user_id, action, details, created_at) VALUES (?, ?, ?, ?, ?)',
                [$orderId, null, 'created', 'طلب جديد', now()]
            );

            $pdo->commit();
            return $orderNumber;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function findByNumber(string $number): ?array
    {
        return Database::row('SELECT * FROM orders WHERE order_number = ?', [$number]);
    }

    public static function find(int $id): ?array
    {
        return Database::row('SELECT * FROM orders WHERE id = ?', [$id]);
    }

    public static function items(int $orderId): array
    {
        return Database::rows('SELECT * FROM order_items WHERE order_id = ?', [$orderId]);
    }

    public static function events(int $orderId): array
    {
        return Database::rows(
            'SELECT oe.*, u.name AS user_name FROM order_events oe
             LEFT JOIN users u ON u.id = oe.user_id
             WHERE oe.order_id = ? ORDER BY oe.id DESC',
            [$orderId]
        );
    }

    /** قائمة الطلبات مع فلاتر لوحة التحكم */
    public static function adminList(array $filters = []): array
    {
        $sql = 'SELECT * FROM orders WHERE 1 = 1';
        $params = [];
        if (!empty($filters['status'])) {
            $sql .= ' AND status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['q'])) {
            $sql .= ' AND (order_number LIKE :q OR customer_name LIKE :q OR phone LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['date'])) {
            $sql .= ' AND created_at LIKE :date';
            $params['date'] = $filters['date'] . '%';
        }
        $sql .= ' ORDER BY id DESC LIMIT 200';
        return Database::rows($sql, $params);
    }

    public static function forCustomer(int $customerId): array
    {
        return Database::rows('SELECT * FROM orders WHERE customer_id = ? ORDER BY id DESC', [$customerId]);
    }

    /** تغيير حالة الطلب مع تسجيل الحدث */
    public static function changeStatus(int $orderId, string $status, ?int $userId, string $note = ''): bool
    {
        if (!in_array($status, self::STATUSES, true)) {
            return false;
        }
        $order = self::find($orderId);
        if ($order === null) {
            return false;
        }

        $updated = Database::exec('UPDATE orders SET status = ?, updated_at = ? WHERE id = ?', [$status, now(), $orderId]);
        if ($updated > 0) {
            Database::exec(
                'INSERT INTO order_events (order_id, user_id, action, details, created_at) VALUES (?, ?, ?, ?, ?)',
                [$orderId, $userId, 'status:' . $status, ($note !== '' ? $note . ' — ' : '') . 'من: ' . $order['status'], now()]
            );
            // إعادة الكمية للمخزون عند الإلغاء أو الإرجاع
            if (in_array($status, ['cancelled', 'returned'], true) && !in_array($order['status'], ['cancelled', 'returned'], true)) {
                foreach (self::items($orderId) as $item) {
                    if ($item['product_id'] !== null) {
                        Database::exec('UPDATE products SET stock = stock + ? WHERE id = ?', [$item['qty'], $item['product_id']]);
                    }
                }
            }
        }
        return $updated > 0;
    }

    public static function delete(int $orderId): void
    {
        Database::exec('DELETE FROM order_items WHERE order_id = ?', [$orderId]);
        Database::exec('DELETE FROM order_events WHERE order_id = ?', [$orderId]);
        Database::exec('DELETE FROM orders WHERE id = ?', [$orderId]);
    }

    /** إحصائيات لوحة التحكم */
    public static function stats(): array
    {
        return [
            'orders_today' => (int) Database::scalar('SELECT COUNT(*) FROM orders WHERE created_at LIKE ?', [date('Y-m-d') . '%']),
            'orders_total' => (int) Database::scalar('SELECT COUNT(*) FROM orders'),
            'revenue_month' => (float) Database::scalar(
                "SELECT COALESCE(SUM(total), 0) FROM orders WHERE status NOT IN ('cancelled', 'returned') AND created_at LIKE ?",
                [date('Y-m') . '%']
            ),
            'new_orders' => (int) Database::scalar("SELECT COUNT(*) FROM orders WHERE status = 'new'"),
            'products' => (int) Database::scalar('SELECT COUNT(*) FROM products'),
            'customers' => (int) Database::scalar('SELECT COUNT(*) FROM customers'),
        ];
    }
}
