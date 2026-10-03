<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Upload;
use App\Models\Category;
use App\Models\Product;

final class ProductController extends Controller
{
    public function index(): void
    {
        Auth::requireArea('products');

        $this->adminView('admin/products', [
            'title' => 'المنتجات',
            'products' => Product::adminList((string) get('q', '')),
            'q' => (string) get('q', ''),
        ]);
    }

    public function create(): void
    {
        Auth::requireArea('products');
        $this->adminView('admin/product_form', [
            'title' => 'منتج جديد',
            'product' => null,
            'translations' => [],
            'images' => [],
            'categories' => Category::all(false),
        ]);
    }

    public function edit(string $id): void
    {
        Auth::requireArea('products');
        $product = Product::find((int) $id);
        if ($product === null) {
            abort(404);
        }

        $this->adminView('admin/product_form', [
            'title' => 'تعديل منتج',
            'product' => $product,
            'translations' => Product::translations((int) $id),
            'images' => Product::images((int) $id),
            'categories' => Category::all(false),
        ]);
    }

    public function save(): void
    {
        Auth::requireArea('products');
        Csrf::guard();

        $id = (int) post('id', 0) ?: null;

        $data = [
            'sku' => (string) post('sku', ''),
            'price' => (string) post('price', '0'),
            'sale_price' => (string) post('sale_price', ''),
            'stock' => (int) post('stock', 0),
            'category_id' => (string) post('category_id', ''),
            'active' => post('active') !== null ? 1 : 0,
            'featured' => post('featured') !== null ? 1 : 0,
            'image' => (string) post('current_image', ''),
        ];

        $uploaded = Upload::image('image');
        if ($uploaded !== null) {
            $data['image'] = $uploaded;
        }
        if ($data['image'] === '') {
            $data['image'] = null;
        }

        $translations = [];
        foreach (['ar', 'fr', 'en'] as $lang) {
            $translations[$lang] = [
                'name' => (string) post("name_$lang", ''),
                'description' => (string) post("description_$lang", ''),
            ];
        }
        if ($translations[setting('default_lang', 'ar')]['name'] === '') {
            flash('error', 'اسم المنتج مطلوب (على الأقل باللغة الافتراضية)');
            redirect($id ? 'admin/products/' . $id : 'admin/products/new');
        }

        $productId = Product::save($data, $translations, $id);
        log_activity(\App\Core\Auth::admin()['id'] ?? null, 'product', $productId, $id ? 'update' : 'create', $translations['ar']['name']);

        flash('success', 'تم حفظ المنتج بنجاح');
        redirect('admin/products/' . $productId);
    }

    public function delete(): void
    {
        Auth::requireArea('products');
        Csrf::guard();

        $id = (int) post('id', 0);
        if ($id > 0) {
            Product::delete($id);
            log_activity(\App\Core\Auth::admin()['id'] ?? null, 'product', $id, 'delete', '');
            flash('success', 'تم حذف المنتج');
        }
        redirect('admin/products');
    }
}
