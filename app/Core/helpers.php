<?php
declare(strict_types=1);

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Lang;

/**
 * دوال مساعدة عامة
 */

/** تهريب مخرجات HTML */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** بناء رابط داخلي */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/** تحويل الصفحة */
function redirect(string $path): never
{
    header('Location: ' . (str_starts_with($path, 'http') ? $path : url($path)));
    exit;
}

/** نص مترجم */
function t(string $key, array $replace = []): string
{
    return Lang::get($key, $replace);
}

/** تنسيق المبلغ بالعملة */
function money(float|int|string|null $amount): string
{
    $currency = setting('currency', 'DZD');
    return number_format((float) $amount, 0, ',', ' ') . ' ' . $currency;
}

/** الآن بصيغة قاعدة البيانات */
function now(): string
{
    return date('Y-m-d H:i:s');
}

/** قيمة من إعدادات المتجر (مخزنة مؤقتًا) */
function setting(string $key, mixed $default = null): mixed
{
    static $settings = null;
    if ($settings === null) {
        try {
            $rows = Database::rows('SELECT setting_key, setting_value FROM settings');
            $settings = [];
            foreach ($rows as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Throwable) {
            $settings = [];
        }
    }
    return $settings[$key] ?? $default;
}

/** قيمة POST منظفة */
function post(string $key, mixed $default = null): mixed
{
    $value = $_POST[$key] ?? $default;
    return is_string($value) ? trim($value) : $value;
}

/** قيمة GET منظفة */
function get(string $key, mixed $default = null): mixed
{
    $value = $_GET[$key] ?? $default;
    return is_string($value) ? trim($value) : $value;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

/** رسائل مؤقتة (Flash) */
function flash(string $type, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$type] = $message;
        return null;
    }
    $value = $_SESSION['flash'][$type] ?? null;
    unset($_SESSION['flash'][$type]);
    return $value;
}

/** إيقاف بحالة خطأ */
function abort(int $code, string $message = ''): never
{
    http_response_code($code);
    echo $message !== '' ? e($message) : 'Error ' . $code;
    exit;
}

/** أداة مساعدة للقوالب: رابط صورة المنتج */
function image_url(?string $path): string
{
    if ($path === null || $path === '') {
        return url('assets/img/placeholder.svg');
    }
    return str_starts_with($path, 'http') ? $path : url('uploads/' . ltrim($path, '/'));
}

/** تسجيل نشاط */
function log_activity(?int $userId, string $entity, ?int $entityId, string $action, string $details = ''): void
    {
    try {
        Database::insert(
            'INSERT INTO activity_log (user_id, entity, entity_id, action, details, created_at) VALUES (?, ?, ?, ?, ?, ?)',
            [$userId, $entity, $entityId, $action, $details, now()]
        );
    } catch (Throwable) {
        // لا نعطّل الطلب بسبب سجل النشاط
    }
}
