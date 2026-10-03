<?php
declare(strict_types=1);

namespace App\Core;

/**
 * المصادقة والصلاحيات — لوحة التحكم + الزبائن
 */
final class Auth
{
    /** صلاحيات كل دور (التحقق يتم على الخادم دائمًا) */
    public const PERMISSIONS = [
        'owner' => ['*'],
        'orders' => ['dashboard', 'orders', 'customers'],
        'products' => ['dashboard', 'products', 'categories', 'pages'],
    ];

    /* ---------- لوحة التحكم ---------- */

    public static function login(string $email, string $password): bool
    {
        $user = Database::row(
            'SELECT * FROM users WHERE email = ? AND active = 1 LIMIT 1',
            [strtolower(trim($email))]
        );
        if ($user === null || !password_verify($password, (string) $user['password_hash'])) {
            return false;
        }
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $user['id'];
        Database::exec('UPDATE users SET last_login_at = ? WHERE id = ?', [now(), $user['id']]);
        return true;
    }

    public static function logout(): void
    {
        unset($_SESSION['admin_id']);
        session_regenerate_id(true);
    }

    public static function admin(): ?array
    {
        static $cache = false;
        if ($cache !== false) {
            return $cache;
        }
        $id = $_SESSION['admin_id'] ?? null;
        if ($id === null) {
            return $cache = null;
        }
        return $cache = Database::row('SELECT id, name, email, role FROM users WHERE id = ? AND active = 1', [$id]);
    }

    public static function check(string $area): bool
    {
        $admin = self::admin();
        if ($admin === null) {
            return false;
        }
        $perms = self::PERMISSIONS[$admin['role']] ?? [];
        return in_array('*', $perms, true) || in_array($area, $perms, true);
    }

    /** تأكد من الصلاحية وإلا 403 */
    public static function requireArea(string $area): void
    {
        if (self::admin() === null) {
            redirect('admin/login');
        }
        if (!self::check($area)) {
            http_response_code(403);
            echo View::renderToString('errors/403', ['title' => '403'], 'layouts/admin');
            exit;
        }
    }

    /* ---------- الزبائن ---------- */

    public static function loginCustomer(int $customerId): void
    {
        session_regenerate_id(true);
        $_SESSION['customer_id'] = $customerId;
    }

    public static function logoutCustomer(): void
    {
        unset($_SESSION['customer_id']);
    }

    public static function customer(): ?array
    {
        $id = $_SESSION['customer_id'] ?? null;
        if ($id === null) {
            return null;
        }
        return Database::row('SELECT id, name, phone, address, wilaya_id FROM customers WHERE id = ?', [$id]);
    }
}
