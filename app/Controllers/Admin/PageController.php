<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\Page;

final class PageController extends Controller
{
    public function index(): void
    {
        Auth::requireArea('pages');

        $id = (int) get('id', 0);
        $this->adminView('admin/pages', [
            'title' => 'الصفحات المخصصة',
            'pages' => Page::all(),
            'editing' => $id > 0 ? Page::find($id) : null,
            'editingTranslations' => $id > 0 ? Page::translations($id) : [],
        ]);
    }

    public function save(): void
    {
        Auth::requireArea('pages');
        Csrf::guard();

        $id = (int) post('id', 0) ?: null;
        $translations = [];
        foreach (['ar', 'fr', 'en'] as $lang) {
            $translations[$lang] = [
                'title' => (string) post("title_$lang", ''),
                'content' => (string) post("content_$lang", ''),
            ];
        }

        Page::save([
            'slug' => (string) post('slug', ''),
            'active' => post('active') !== null ? 1 : 0,
            'position' => (int) post('position', 0),
        ], $translations, $id);

        log_activity(Auth::admin()['id'] ?? null, 'page', $id, $id ? 'update' : 'create', (string) post('slug', ''));
        flash('success', 'تم حفظ الصفحة');
        redirect('admin/pages');
    }

    public function delete(): void
    {
        Auth::requireArea('pages');
        Csrf::guard();

        $id = (int) post('id', 0);
        if ($id > 0) {
            Page::delete($id);
            log_activity(Auth::admin()['id'] ?? null, 'page', $id, 'delete', '');
            flash('success', 'تم حذف الصفحة');
        }
        redirect('admin/pages');
    }
}
