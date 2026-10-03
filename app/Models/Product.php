<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Lang;

/**
 * المنتجات — الاستعلامات مع الترجمات (ar/fr/en)
 */
final class Product
{
    private const SELECT = 'SELECT p.*, COALESCE(t.name, td.name) AS name,
            COALESCE(t.description, td.description) AS description,
            c.id AS category_id_ref
        FROM products p
        LEFT JOIN product_translations t ON t.product_id = p.id AND t.lang = :lang
        LEFT JOIN product_translations td ON td.product_id = p.id AND td.lang = :dlang
        LEFT JOIN categories c ON c.id = p.category_id';

    /** قائمة المنتجات مع الفلاتر والبحث */
    public static function list(array $filters = [], int $limit = 12, int $offset = 0): array
    {
        $sql = self::SELECT . ' WHERE p.active = 1';
        $params = ['lang' => Lang::code(), 'dlang' => setting('default_lang', 'ar')];

        if (!empty($filters['category'])) {
            $sql .= ' AND p.category_id = :cat';
            $params['cat'] = (int) $filters['category'];
        }
        if (!empty($filters['q'])) {
            $sql .= ' AND (COALESCE(t.name, td.name) LIKE :q OR COALESCE(t.description, td.description) LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }
        if (isset($filters['min']) && $filters['min'] !== '') {
            $sql .= ' AND COALESCE(p.sale_price, p.price) >= :min';
            $params['min'] = (float) $filters['min'];
        }
        if (isset($filters['max']) && $filters['max'] !== '') {
            $sql .= ' AND COALESCE(p.sale_price, p.price) <= :max';
            $params['max'] = (float) $filters['max'];
        }
        if (!empty($filters['featured'])) {
            $sql .= ' AND p.featured = 1';
        }

        $order = match ($filters['sort'] ?? '') {
            'price_asc' => 'COALESCE(p.sale_price, p.price) ASC',
            'price_desc' => 'COALESCE(p.sale_price, p.price) DESC',
            default => 'p.created_at DESC, p.id DESC',
        };
        $sql .= " ORDER BY $order LIMIT $limit OFFSET $offset";

        return Database::rows($sql, $params);
    }

    public static function count(array $filters = []): int
    {
        $sql = 'SELECT COUNT(*) FROM products p
            LEFT JOIN product_translations t ON t.product_id = p.id AND t.lang = :lang
            LEFT JOIN product_translations td ON td.product_id = p.id AND td.lang = :dlang
            WHERE p.active = 1';
        $params = ['lang' => Lang::code(), 'dlang' => setting('default_lang', 'ar')];
        if (!empty($filters['category'])) {
            $sql .= ' AND p.category_id = :cat';
            $params['cat'] = (int) $filters['category'];
        }
        if (!empty($filters['q'])) {
            $sql .= ' AND (COALESCE(t.name, td.name) LIKE :q OR COALESCE(t.description, td.description) LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }
        if (isset($filters['min']) && $filters['min'] !== '') {
            $sql .= ' AND COALESCE(p.sale_price, p.price) >= :min';
            $params['min'] = (float) $filters['min'];
        }
        if (isset($filters['max']) && $filters['max'] !== '') {
            $sql .= ' AND COALESCE(p.sale_price, p.price) <= :max';
            $params['max'] = (float) $filters['max'];
        }
        return (int) Database::scalar($sql, $params);
    }

    /** منتج واحد للواجهة */
    public static function find(int $id): ?array
    {
        return Database::row(self::SELECT . ' WHERE p.id = :id AND p.active = 1', [
            'id' => $id,
            'lang' => Lang::code(),
            'dlang' => setting('default_lang', 'ar'),
        ]);
    }

    /** كل ترجمات منتج (للتعديل في لوحة التحكم) */
    public static function translations(int $id): array
    {
        $rows = Database::rows('SELECT lang, name, description FROM product_translations WHERE product_id = ?', [$id]);
        $out = [];
        foreach ($rows as $row) {
            $out[$row['lang']] = $row;
        }
        return $out;
    }

    public static function images(int $id): array
    {
        return Database::rows('SELECT * FROM product_images WHERE product_id = ? ORDER BY position, id', [$id]);
    }

    public static function related(int $id, int $categoryId, int $limit = 4): array
    {
        return Database::rows(
            self::SELECT . ' WHERE p.active = 1 AND p.id != :id AND p.category_id = :cat ORDER BY p.created_at DESC LIMIT ' . $limit,
            ['id' => $id, 'cat' => $categoryId, 'lang' => Lang::code(), 'dlang' => setting('default_lang', 'ar')]
        );
    }

    /** حفظ منتج + ترجماته (لوحة التحكم) */
    public static function save(array $data, array $translations, ?int $id = null): int
    {
        $params = [
            'sku' => $data['sku'] ?: null,
            'price' => (float) $data['price'],
            'sale_price' => $data['sale_price'] !== '' ? (float) $data['sale_price'] : null,
            'stock' => (int) $data['stock'],
            'category_id' => $data['category_id'] !== '' ? (int) $data['category_id'] : null,
            'active' => (int) ($data['active'] ?? 1),
            'featured' => (int) ($data['featured'] ?? 0),
            'image' => $data['image'] ?? null,
        ];

        if ($id === null) {
            $id = (int) Database::insert(
                'INSERT INTO products (sku, price, sale_price, stock, category_id, active, featured, image, created_at, updated_at)
                 VALUES (:sku, :price, :sale_price, :stock, :category_id, :active, :featured, :image, :created_at, :updated_at)',
                $params + ['created_at' => now(), 'updated_at' => now()]
            );
        } else {
            Database::exec(
                'UPDATE products SET sku = :sku, price = :price, sale_price = :sale_price, stock = :stock,
                 category_id = :category_id, active = :active, featured = :featured, image = :image, updated_at = :updated_at
                 WHERE id = :id',
                $params + ['updated_at' => now(), 'id' => $id]
            );
        }

        foreach ($translations as $lang => $tr) {
            $updated = Database::exec(
                'UPDATE product_translations SET name = ?, description = ? WHERE product_id = ? AND lang = ?',
                [$tr['name'] ?? '', $tr['description'] ?? '', $id, $lang]
            );
            if ($updated === 0) {
                Database::insert(
                    'INSERT INTO product_translations (product_id, lang, name, description) VALUES (?, ?, ?, ?)',
                    [$id, $lang, $tr['name'] ?? '', $tr['description'] ?? '']
                );
            }
        }

        return $id;
    }

    public static function delete(int $id): void
    {
        Database::exec('DELETE FROM product_translations WHERE product_id = ?', [$id]);
        Database::exec('DELETE FROM product_images WHERE product_id = ?', [$id]);
        Database::exec('DELETE FROM products WHERE id = ?', [$id]);
    }

    /** السعر الفعلي (مع الخصم) */
    public static function price(array $product): float
    {
        return (float) ($product['sale_price'] ?? $product['price']);
    }

    /** كل المنتجات للوحة التحكم */
    public static function adminList(string $q = ''): array
    {
        $sql = 'SELECT p.*, COALESCE(t.name, td.name) AS name FROM products p
            LEFT JOIN product_translations t ON t.product_id = p.id AND t.lang = :lang
            LEFT JOIN product_translations td ON td.product_id = p.id AND td.lang = :dlang';
        $params = ['lang' => Lang::code(), 'dlang' => setting('default_lang', 'ar')];
        if ($q !== '') {
            $sql .= ' WHERE COALESCE(t.name, td.name) LIKE :q OR p.sku LIKE :q';
            $params['q'] = '%' . $q . '%';
        }
        $sql .= ' ORDER BY p.id DESC';
        return Database::rows($sql, $params);
    }
}
