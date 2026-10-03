<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\Wilaya;

final class WilayaController extends Controller
{
    public function index(): void
    {
        Auth::requireArea('wilayas');

        $this->adminView('admin/wilayas', [
            'title' => 'الولايات والتوصيل',
            'wilayas' => Wilaya::all(false),
        ]);
    }

    public function save(): void
    {
        Auth::requireArea('wilayas');
        Csrf::guard();

        $prices = $_POST['price'] ?? [];
        $actives = $_POST['active'] ?? [];
        if (is_array($prices)) {
            foreach ($prices as $id => $price) {
                $id = (int) $id;
                if ($id > 0) {
                    Wilaya::save($id, [
                        'delivery_price' => (float) $price,
                        'active' => isset($actives[$id]) ? 1 : 0,
                    ]);
                }
            }
            log_activity(Auth::admin()['id'] ?? null, 'wilaya', null, 'update', 'تحديث أسعار التوصيل');
            flash('success', 'تم حفظ إعدادات الولايات');
        }
        redirect('admin/wilayas');
    }
}
