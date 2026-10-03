<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Lang;

/**
 * الفئات
 */
final class Category
{
    private const SELECT = 'SELECT c.*, COALESCE(t.name, td.name) AS name
        FROM categories c
        LEFT JOIN category_translations t ON t.category_id = c.id AND t.lang = :lang
        LEFT JOIN category_translations td ON td.category_id = c.id AND td.lang = :dlang';

    public static function all(bool $onlyActive = true): array
    {
        $sql = self::SELECT . ($onlyActive ? ' WHERE c.active = 1' : '') . ' ORDER BY c.position, c.id';
        return Database::rows($sql, ['lang' => Lang::code(), 'dlang' => setting('default_lang', 'ar')]);
    }

    public static function find(int $id): ?array
    {
        return Database::row(self::SELECT . ' WHERE c.id = :id', [
            'id' => $id,
            'lang' => Lang::code(),
            'dlang' => setting('default_lang', 'ar'),
        ]);
    }

    public static function translations(int $id): array
    {
        $rows = Database::rows('SELECT lang, name FROM category_translations WHERE category_id = ?', [$id]);
        $out = [];
        foreach ($rows as $row) {
            $out[$row['lang']] = $row['name'];
        }
        return $out;
    }

    public static function save(array $translations, ?int $id = null, array $data = []): int
    {
        if ($id === null) {
            $id = (int) Database::insert(
                'INSERT INTO categories (parent_id, image, position, active) VALUES (?, ?, ?, ?)',
                [
                    $data['parent_id'] !== '' ? (int) $data['parent_id'] : null,
                    $data['image'] ?? null,
                    (int) ($data['position'] ?? 0),
                    (int) ($data['active'] ?? 1),
                ]
            );
        } else {
            Database::exec(
                'UPDATE categories SET parent_id = ?, image = ?, position = ?, active = ? WHERE id = ?',
                [
                    $data['parent_id'] !== '' ? (int) $data['parent_id'] : null,
                    $data['image'] ?? null,
                    (int) ($data['position'] ?? 0),
                    (int) ($data['active'] ?? 1),
                    $id,
                ]
            );
        }

        foreach ($translations as $lang => $name) {
            $updated = Database::exec(
                'UPDATE category_translations SET name = ? WHERE category_id = ? AND lang = ?',
                [$name, $id, $lang]
            );
            if ($updated === 0) {
                Database::insert(
                    'INSERT INTO category_translations (category_id, lang, name) VALUES (?, ?, ?)',
                    [$id, $lang, $name]
                );
            }
        }

        return $id;
    }

    public static function delete(int $id): void
    {
        Database::exec('UPDATE products SET category_id = NULL WHERE category_id = ?', [$id]);
        Database::exec('DELETE FROM category_translations WHERE category_id = ?', [$id]);
        Database::exec('DELETE FROM categories WHERE id = ?', [$id]);
    }
}
