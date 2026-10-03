<?php
declare(strict_types=1);

namespace App\Core;

/**
 * اللغات: ar (افتراضي) / fr / en — مع دعم RTL
 */
final class Lang
{
    public const CODES = ['ar', 'fr', 'en'];

    private static string $code = 'ar';
    private static array $strings = [];

    public static function init(): void
    {
        $default = Config::get('app.default_lang', 'ar');
        $code = $default;

        $cookie = $_COOKIE['lang'] ?? null;
        if (in_array($cookie, self::CODES, true)) {
            $code = $cookie;
        }

        self::set($code);
    }

    public static function set(string $code): void
    {
        if (!in_array($code, self::CODES, true)) {
            $code = 'ar';
        }
        self::$code = $code;
        $file = APP_ROOT . '/lang/' . $code . '.php';
        self::$strings = is_file($file) ? (array) require $file : [];

        setcookie('lang', $code, [
            'expires' => time() + 86400 * 365,
            'path' => BASE_URL === '' ? '/' : BASE_URL . '/',
            'samesite' => 'Lax',
        ]);
    }

    public static function code(): string
    {
        return self::$code;
    }

    public static function dir(): string
    {
        return self::$code === 'ar' ? 'rtl' : 'ltr';
    }

    public static function get(string $key, array $replace = []): string
    {
        $text = self::$strings[$key] ?? Config::get('app.fallback_strings', [])[$key] ?? $key;
        foreach ($replace as $k => $v) {
            $text = str_replace(':' . $k, (string) $v, $text);
        }
        return $text;
    }
}
