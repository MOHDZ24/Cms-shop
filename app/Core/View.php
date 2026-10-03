<?php
declare(strict_types=1);

namespace App\Core;

/**
 * عارض القوالب (Views) — app/Views
 */
final class View
{
    private static array $shared = [];

    /** بيانات مشتركة لكل القوالب (مثل إعدادات المتجر) */
    public static function share(string $key, mixed $value): void
    {
        self::$shared[$key] = $value;
    }

    public static function render(string $template, array $data = [], ?string $layout = 'layouts/front'): void
    {
        echo self::renderToString($template, $data, $layout);
    }

    public static function renderToString(string $template, array $data = [], ?string $layout = 'layouts/front'): string
    {
        $content = self::partial($template, $data);
        if ($layout === null) {
            return $content;
        }
        return self::partial($layout, array_merge($data, ['content' => $content]));
    }

    public static function partial(string $template, array $data = []): string
    {
        $file = APP_ROOT . '/app/Views/' . $template . '.php';
        if (!is_file($file)) {
            return '<!-- View not found: ' . e($template) . ' -->';
        }
        extract(self::$shared, EXTR_SKIP);
        extract($data, EXTR_SKIP);
        ob_start();
        include $file;
        return (string) ob_get_clean();
    }
}
