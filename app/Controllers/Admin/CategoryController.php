<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\Category;

final class CategoryController extends Controller
{
    public function index(): void
    {
        Auth::requireArea('products');

        $id = (int) get('id', 0);
        $this->adminView('admin/categories', [
            'title' => 'الفئات',
            'categories' => Category::all(false),
            'editing' => $id > 0 ? Category::find($id) : null,
            'editingTranslations' => $id > 0 ? Category::translations($id) : [],
        ]);
    }

    public function save(): void
    {
        Auth::requireArea('products');
        Csrf::guard();

        $id = (int) post('id', 0) ?: null;
        $translations = [];
        foreach (['ar', 'fr', 'en'] as $lang) {
            $translations[$lang] = (string) post("name_$lang", '');
        }
        if ($translations[setting('default_lang', 'ar')] === '') {
            flash('error', 'اسم الفئة مطلوب (على الأقل باللغة الافتراضية)');
            redirect('admin/categories');
        }

        Category::save($translations, $id, [
            'parent_id' => (string) post('parent_id', ''),
            'image' => (string) post('current_image', ''),
            'position' => (int) post('position', 0),
            'active' => post('active') !== null ? 1 : 0,
        ]);

        log_activity(Auth::admin()['id'] ?? null, 'category', $id, $id ? 'update' : 'create', $translations['ar']);
        flash('success', 'تم حفظ الفئة');
        redirect('admin/categories');
    }

    public function delete(): void
    {
        Auth::requireArea('products');
        Csrf::guard();

        $id = (int) post('id', 0);
        if ($id > 0) {
            Category::delete($id);
            log_activity(Auth::admin()['id'] ?? null, 'category', $id, 'delete', '');
            flash('success', 'تم حذف الفئة');
        }
        redirect('admin/categories');
    }
}
