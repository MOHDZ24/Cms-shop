<?php
declare(strict_types=1);

/**
 * التهيئة العامة — Cms-shop
 */

define('APP_ROOT', dirname(__DIR__));

/* إعدادات العرض والوقت */
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);
date_default_timezone_set('Africa/Algiers');

/* رابط الأساس (يدعم التثبيت في مجلد فرعي وكل أنواع الاستضافات) */
$appRoot = str_replace('\\', '/', dirname(__DIR__));
$docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
if ($docRoot !== '' && str_starts_with($appRoot, $docRoot)) {
    $baseUrl = substr($appRoot, strlen($docRoot));
} else {
    $baseUrl = dirname(str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/')));
}
define('BASE_URL', $baseUrl === '/' || $baseUrl === '\\' ? '' : rtrim($baseUrl, '/'));

/* التحميل التلقائي للملفات */
spl_autoload_register(static function (string $class): void {
    if (str_starts_with($class, 'App\\')) {
        $path = __DIR__ . '/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (is_file($path)) {
            require $path;
        }
    }
});

require __DIR__ . '/Core/helpers.php';

/* الجلسة (مع حل بديل إن كانت مجلدات الجلسات غير قابلة للكتابة على الاستضافة) */
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => BASE_URL === '' ? '/' : BASE_URL . '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('cmsshop');
    if (!@session_start()) {
        $fallback = sys_get_temp_dir() . '/cmsshop-sessions';
        if (is_dir($fallback) || @mkdir($fallback, 0700, true)) {
            @session_save_path($fallback);
        }
        @session_start();
    }
}

/* الإعدادات */
$configFile = APP_ROOT . '/config/config.php';
$isInstaller = str_contains($_SERVER['REQUEST_URI'] ?? '', '/install');

if (is_file($configFile)) {
    \App\Core\Config::load(require $configFile);
} elseif (!$isInstaller) {
    header('Location: ' . url('install/'));
    exit;
}

\App\Core\Lang::init();
