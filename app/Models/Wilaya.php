<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * الولايات الـ69 + أسعار التوصيل
 */
final class Wilaya
{
    public static function all(bool $onlyActive = true): array
    {
        $sql = 'SELECT * FROM wilayas' . ($onlyActive ? ' WHERE active = 1' : '') . ' ORDER BY id';
        return Database::rows($sql);
    }

    public static function find(int $id): ?array
    {
        return Database::row('SELECT * FROM wilayas WHERE id = ?', [$id]);
    }

    /** الاسم حسب لغة الواجهة */
    public static function name(array $wilaya, string $lang): string
    {
        return $wilaya['name_' . $lang] ?? $wilaya['name_ar'] ?? '';
    }

    public static function save(int $id, array $data): void
    {
        Database::exec(
            'UPDATE wilayas SET delivery_price = ?, active = ? WHERE id = ?',
            [(float) $data['delivery_price'], (int) ($data['active'] ?? 0), $id]
        );
    }
}
