<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Auth;
use App\Core\Database;

final class LogController extends Controller
{
    public function index(): void
    {
        Auth::requireArea('logs');

        $this->adminView('admin/logs', [
            'title' => 'سجل النشاط',
            'logs' => Database::rows(
                'SELECT al.*, u.name AS user_name FROM activity_log al
                 LEFT JOIN users u ON u.id = al.user_id
                 ORDER BY al.id DESC LIMIT 300'
            ),
        ]);
    }
}
