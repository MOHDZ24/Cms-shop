<?php
/**
 * الولايات — أسعار التوصيل
 */
use App\Core\Csrf;
?>
<form method="post" action="<?= e(url('admin/wilayas/save')) ?>">
  <?= Csrf::field() ?>
  <div class="card">
    <h2>أسعار التوصيل لكل ولاية (<?= count($wilayas) ?> ولاية)</h2>
    <p class="muted-note">السعر بالدينار الجزائري — عدّله حسب شركة التوصيل. ألغِ "مفعّلة" لإخفاء ولاية من نموذج الطلب.</p>
    <table class="table">
      <thead><tr><th>#</th><th>الولاية</th><th>سعر التوصيل (<?= e(setting('currency', 'DZD')) ?>)</th><th>مفعّلة</th></tr></thead>
      <tbody>
        <?php foreach ($wilayas as $wilaya): ?>
          <tr>
            <td><?= e($wilaya['code']) ?></td>
            <td><b><?= e($wilaya['name_ar']) ?></b> <small>(<?= e($wilaya['name_fr']) ?>)</small></td>
            <td><input type="number" name="price[<?= (int) $wilaya['id'] ?>]" value="<?= e($wilaya['delivery_price']) ?>" style="max-width:120px"></td>
            <td><input type="checkbox" name="active[<?= (int) $wilaya['id'] ?>]" <?= (int) $wilaya['active'] === 1 ? 'checked' : '' ?>></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <button class="btn btn-primary btn-lg" type="submit">💾 حفظ الإعدادات</button>
  </div>
</form>
