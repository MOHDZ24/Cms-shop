<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Upload;

final class SettingsController extends Controller
{
    public function index(): void
    {
        Auth::requireArea('settings');

        $this->adminView('admin/settings', [
            'title' => 'إعدادات المتجر',
        ]);
    }

    public function save(): void
    {
        Auth::requireArea('settings');
        Csrf::guard();

        $fields = [
            'site_name' => (string) post('site_name', ''),
            'currency' => (string) post('currency', 'DZD'),
            'default_lang' => in_array(post('default_lang'), ['ar', 'fr', 'en'], true) ? (string) post('default_lang') : 'ar',
            'primary_color' => (string) post('primary_color', '#1a73e8'),
            'phone' => (string) post('phone', ''),
            'address' => (string) post('address', ''),
            'facebook' => (string) post('facebook', ''),
            'instagram' => (string) post('instagram', ''),
            'whatsapp' => (string) post('whatsapp', ''),
        ];

        $logo = Upload::image('logo');
        if ($logo !== null) {
            $fields['logo'] = $logo;
        }

        foreach ($fields as $key => $value) {
            $updated = Database::exec('UPDATE settings SET setting_value = ? WHERE setting_key = ?', [$value, $key]);
            if ($updated === 0) {
                Database::insert('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)', [$key, $value]);
            }
        }

        log_activity(Auth::admin()['id'] ?? null, 'settings', null, 'update', 'تحديث إعدادات المتجر');
        flash('success', 'تم حفظ الإعدادات');
        redirect('admin/settings');
    }
}
