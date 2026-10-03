<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Lang;

final class LangController extends Controller
{
    public function switch(string $code): void
    {
        if (in_array($code, Lang::CODES, true)) {
            Lang::set($code);
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $path = parse_url($referer, PHP_URL_PATH);
        if (is_string($path) && $path !== '') {
            header('Location: ' . $path);
            exit;
        }
        redirect('');
    }
}
