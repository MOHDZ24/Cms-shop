<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Database;

final class UserController extends Controller
{
    public function index(): void
    {
        Auth::requireArea('users');

        $this->adminView('admin/users', [
            'title' => 'المستخدمون والصلاحيات',
            'users' => Database::rows('SELECT id, name, email, role, active, created_at, last_login_at FROM users ORDER BY id'),
            'editing' => (int) get('edit', 0) > 0
                ? Database::row('SELECT id, name, email, role, active FROM users WHERE id = ?', [(int) get('edit', 0)])
                : null,
        ]);
    }

    public function save(): void
    {
        Auth::requireArea('users');
        Csrf::guard();

        $id = (int) post('id', 0);
        $name = (string) post('name', '');
        $email = strtolower((string) post('email', ''));
        $role = (string) post('role', 'products');
        $password = (string) post('password', '');
        $active = post('active') !== null ? 1 : 0;

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($role, ['owner', 'orders', 'products'], true)) {
            flash('error', 'يرجى إدخال بيانات صحيحة (الدور: owner / orders / products)');
            redirect('admin/users');
        }

        if ($id > 0) {
            Database::exec('UPDATE users SET name = ?, email = ?, role = ?, active = ? WHERE id = ?', [$name, $email, $role, $active, $id]);
            if ($password !== '') {
                if (strlen($password) < 6) {
                    flash('error', 'كلمة السر يجب أن تكون 6 أحرف على الأقل');
                    redirect('admin/users');
                }
                Database::exec('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $id]);
            }
        } else {
            if (strlen($password) < 6) {
                flash('error', 'كلمة السر يجب أن تكون 6 أحرف على الأقل');
                redirect('admin/users');
            }
            $id = (int) Database::insert(
                'INSERT INTO users (name, email, password_hash, role, active, created_at) VALUES (?, ?, ?, ?, ?, ?)',
                [$name, $email, password_hash($password, PASSWORD_DEFAULT), $role, $active, now()]
            );
        }

        log_activity(Auth::admin()['id'] ?? null, 'user', $id, 'save', $email . ' (' . $role . ')');
        flash('success', 'تم حفظ المستخدم');
        redirect('admin/users');
    }

    public function delete(): void
    {
        Auth::requireArea('users');
        Csrf::guard();

        $id = (int) post('id', 0);
        $me = Auth::admin();

        if ($id > 0 && (int) ($me['id'] ?? 0) !== $id) {
            Database::exec('DELETE FROM users WHERE id = ?', [$id]);
            log_activity($me['id'] ?? null, 'user', $id, 'delete', '');
            flash('success', 'تم حذف المستخدم');
        } else {
            flash('error', 'لا يمكنك حذف حسابك الحالي');
        }
        redirect('admin/users');
    }
}
