<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Wilaya;

final class CatalogController extends Controller
{
    private const PER_PAGE = 12;

    public function index(): void
    {
        $filters = [
            'category' => (int) get('category', 0) ?: null,
            'q' => (string) get('q', ''),
            'min' => get('min', ''),
            'max' => get('max', ''),
            'sort' => (string) get('sort', ''),
        ];
        $filters = array_filter($filters, static fn ($v) => $v !== '' && $v !== null);

        $page = max(1, (int) get('page', 1));
        $total = Product::count($filters);
        $pages = max(1, (int) ceil($total / self::PER_PAGE));

        $this->view('front/shop', [
            'title' => t('shop'),
            'products' => Product::list($filters, self::PER_PAGE, ($page - 1) * self::PER_PAGE),
            'categories' => Category::all(),
            'filters' => $filters,
            'total' => $total,
            'page' => $page,
            'pages' => $pages,
            'navPages' => $this->navPages(),
        ]);
    }

    public function product(string $id): void
    {
        $product = Product::find((int) $id);
        if ($product === null) {
            abort(404);
        }

        $this->view('front/product', [
            'title' => (string) $product['name'],
            'product' => $product,
            'images' => Product::images((int) $id),
            'related' => $product['category_id'] ? Product::related((int) $id, (int) $product['category_id']) : [],
            'navPages' => $this->navPages(),
        ]);
    }
}
