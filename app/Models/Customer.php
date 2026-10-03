<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * الزبائن (الحسابات الاختيارية)
 */
final class Customer
{
    public static function findByPhone(string $phone): ?array
    {
        return Database::row('SELECT * FROM customers WHERE phone = ?', [$phone]);
    }

    public static function find(int $id): ?array
    {
        return Database::row('SELECT * FROM customers WHERE id = ?', [$id]);
    }

    public static function create(string $name, string $phone, ?string $password = null, ?string $address = null): int
    {
        return (int) Database::insert(
            'INSERT INTO customers (name, phone, password_hash, address, created_at) VALUES (?, ?, ?, ?, ?)',
            [$name, $phone, $password !== null ? password_hash($password, PASSWORD_DEFAULT) : null, $address, now()]
        );
    }

    /** إنشاء/تحديث حساب من بيانات الطلب (كضيف) */
    public static function upsertFromOrder(string $name, string $phone, string $address): int
    {
        $existing = self::findByPhone($phone);
        if ($existing !== null) {
            Database::exec('UPDATE customers SET name = ?, address = ? WHERE id = ?', [$name, $address, $existing['id']]);
            return (int) $existing['id'];
        }
        return self::create($name, $phone, null, $address);
    }

    public static function all(string $q = ''): array
    {
        $sql = 'SELECT c.*, (SELECT COUNT(*) FROM orders o WHERE o.customer_id = c.id) AS orders_count
                FROM customers c';
        $params = [];
        if ($q !== '') {
            $sql .= ' WHERE c.name LIKE :q OR c.phone LIKE :q';
            $params['q'] = '%' . $q . '%';
        }
        $sql .= ' ORDER BY c.id DESC LIMIT 300';
        return Database::rows($sql, $params);
    }

    public static function orders(int $customerId): array
    {
        return Database::rows('SELECT * FROM orders WHERE customer_id = ? ORDER BY id DESC', [$customerId]);
    }
}
