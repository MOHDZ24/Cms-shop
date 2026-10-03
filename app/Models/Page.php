<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Lang;

/**
 * الصفحات المخصصة (تُدار من لوحة التحكم)
 */
final class Page
{
    private const SELECT = 'SELECT p.*, COALESCE(t.title, td.title) AS title,
            COALESCE(t.content, td.content) AS content
        FROM pages p
        LEFT JOIN page_translations t ON t.page_id = p.id AND t.lang = :lang
        LEFT JOIN page_translations td ON td.page_id = p.id AND td.lang = :dlang';

    public static function all(bool $onlyActive = false): array
    {
        $sql = self::SELECT . ($onlyActive ? ' WHERE p.active = 1' : '') . ' ORDER BY p.position, p.id';
        return Database::rows($sql, ['lang' => Lang::code(), 'dlang' => setting('default_lang', 'ar')]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::row(self::SELECT . ' WHERE p.slug = :slug AND p.active = 1', [
            'slug' => $slug,
            'lang' => Lang::code(),
            'dlang' => setting('default_lang', 'ar'),
        ]);
    }

    public static function find(int $id): ?array
    {
        return Database::row(self::SELECT . ' WHERE p.id = :id', [
            'id' => $id,
            'lang' => Lang::code(),
            'dlang' => setting('default_lang', 'ar'),
        ]);
    }

    public static function translations(int $id): array
    {
        $rows = Database::rows('SELECT lang, title, content FROM page_translations WHERE page_id = ?', [$id]);
        $out = [];
        foreach ($rows as $row) {
            $out[$row['lang']] = $row;
        }
        return $out;
    }

    public static function save(array $data, array $translations, ?int $id = null): int
    {
        $slug = trim((string) ($data['slug'] ?? ''));
        if ($slug === '') {
            $slug = 'page-' . substr(bin2hex(random_bytes(4)), 0, 6);
        }

        if ($id === null) {
            $id = (int) Database::insert(
                'INSERT INTO pages (slug, active, position) VALUES (?, ?, ?)',
                [$slug, (int) ($data['active'] ?? 1), (int) ($data['position'] ?? 0)]
            );
        } else {
            Database::exec('UPDATE pages SET slug = ?, active = ?, position = ? WHERE id = ?', [
                $slug,
                (int) ($data['active'] ?? 1),
                (int) ($data['position'] ?? 0),
                $id,
            ]);
        }

        foreach ($translations as $lang => $tr) {
            $updated = Database::exec(
                'UPDATE page_translations SET title = ?, content = ? WHERE page_id = ? AND lang = ?',
                [$tr['title'] ?? '', $tr['content'] ?? '', $id, $lang]
            );
            if ($updated === 0) {
                Database::insert(
                    'INSERT INTO page_translations (page_id, lang, title, content) VALUES (?, ?, ?, ?)',
                    [$id, $lang, $tr['title'] ?? '', $tr['content'] ?? '']
                );
            }
        }

        return $id;
    }

    public static function delete(int $id): void
    {
        Database::exec('DELETE FROM page_translations WHERE page_id = ?', [$id]);
        Database::exec('DELETE FROM pages WHERE id = ?', [$id]);
    }
}
