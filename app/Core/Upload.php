<?php
declare(strict_types=1);

namespace App\Core;

/**
 * رفع الصور (التحقق الصارم: صور فقط، بلا تنفيذ PHP)
 */
final class Upload
{
    private const ALLOWED = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    /** يُرجع المسار النسبي في uploads/ أو null */
    public static function image(string $field): ?string
    {
        $file = $_FILES[$field] ?? null;
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 5 * 1024 * 1024) {
            return null;
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset(self::ALLOWED[$mime])) {
            return null;
        }

        $name = date('Ymd') . '-' . bin2hex(random_bytes(8)) . '.' . self::ALLOWED[$mime];
        $dest = APP_ROOT . '/uploads/' . $name;

        if (!is_dir(dirname($dest))) {
            mkdir(dirname($dest), 0755, true);
        }
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return null;
        }
        @chmod($dest, 0644);
        return $name;
    }
}
