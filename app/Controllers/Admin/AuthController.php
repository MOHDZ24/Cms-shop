<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Auth;
use App\Core\Csrf;

final class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (Auth::admin() !== null) {
            redirect('admin');
        }
        $this->adminView('admin/login', ['title' => 'تسجيل الدخول']);
    }

    public function login(): void
    {
        Csrf::guard();

        $email = (string) post('email', '');
        $password = (string) post('password', '');

        if (!Auth::login($email, $password)) {
            sleep(1);
            flash('error', 'البريد الإلكتروني أو كلمة السر غير صحيحة');
            redirect('admin/login');
        }

        $admin = Auth::admin();
        log_activity($admin['id'] ?? null, 'user', $admin['id'] ?? null, 'login', 'دخول إلى لوحة التحكم');
        redirect('admin');
    }

    public function logout(): void
    {
        $admin = Auth::admin();
        log_activity($admin['id'] ?? null, 'user', $admin['id'] ?? null, 'logout', 'خروج من لوحة التحكم');
        Auth::logout();
        redirect('admin/login');
    }
}
