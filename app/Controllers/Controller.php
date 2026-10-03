<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\View;

/**
 * الفئة الأساسية للتحكم (Controllers)
 */
abstract class Controller
{
    protected function view(string $template, array $data = [], ?string $layout = 'layouts/front'): void
    {
        View::render($template, $data, $layout);
    }

    protected function adminView(string $template, array $data = []): void
    {
        View::render($template, $data, 'layouts/admin');
    }

    protected function guardCsrf(): void
    {
        Csrf::guard();
    }

    /** صفحات القائمة (من قاعدة البيانات) */
    protected function navPages(): array
    {
        return Database::rows(
            'SELECT p.slug, COALESCE(t.title, td.title) AS title
             FROM pages p
             LEFT JOIN page_translations t ON t.page_id = p.id AND t.lang = ?
             LEFT JOIN page_translations td ON td.page_id = p.id AND td.lang = ?
             WHERE p.active = 1 ORDER BY p.position, p.id',
            [\App\Core\Lang::code(), setting('default_lang', 'ar')]
        );
    }
}
