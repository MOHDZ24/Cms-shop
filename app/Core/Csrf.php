<?php
declare(strict_types=1);

namespace App\Core;

/**
 * حماية CSRF — كل نموذج POST يجب أن يحمل الرمز
 */
final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::token()) . '">';
    }

    public static function verify(): bool
    {
        $sent = $_POST['_csrf'] ?? '';
        return is_string($sent) && $sent !== '' && hash_equals(self::token(), $sent);
    }

    /** أوقف التنفيذ إذا كان الطلب غير صالح */
    public static function guard(): void
    {
        if (!self::verify()) {
            http_response_code(419);
            exit('انتهت صلاحية النموذج. حدّث الصفحة وحاول مجددًا.');
        }
    }
}
