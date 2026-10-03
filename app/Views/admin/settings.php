<?php
/**
 * إعدادات المتجر
 */
use App\Core\Csrf;
?>
<form method="post" action="<?= e(url('admin/settings')) ?>" enctype="multipart/form-data" class="form-stack">
  <?= Csrf::field() ?>

  <div class="card">
    <h2>هوية المتجر</h2>
    <div class="grid-2">
      <div>
        <label>اسم المتجر</label>
        <input name="site_name" value="<?= e(setting('site_name', '')) ?>">
      </div>
      <div>
        <label>الشعار (صورة)</label>
        <input type="file" name="logo" accept="image/*">
        <?php if (setting('logo', '') !== ''): ?>
          <img class="thumb" src="<?= e(image_url(setting('logo'))) ?>" alt="logo">
        <?php endif; ?>
      </div>
      <div>
        <label>اللون الرئيسي</label>
        <input type="color" name="primary_color" value="<?= e(setting('primary_color', '#1a73e8')) ?>">
      </div>
      <div>
        <label>العملة</label>
        <input name="currency" value="<?= e(setting('currency', 'DZD')) ?>">
      </div>
      <div>
        <label>اللغة الافتراضية</label>
        <select name="default_lang">
          <?php foreach (['ar' => 'العربية', 'fr' => 'الفرنسية', 'en' => 'الإنجليزية'] as $code => $label): ?>
            <option value="<?= $code ?>" <?= setting('default_lang', 'ar') === $code ? 'selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label>رقم الهاتف</label>
        <input name="phone" dir="ltr" value="<?= e(setting('phone', '')) ?>">
      </div>
    </div>
    <label>العنوان</label>
    <input name="address" value="<?= e(setting('address', '')) ?>">
  </div>

  <div class="card">
    <h2>التواصل الاجتماعي</h2>
    <div class="grid-2">
      <div>
        <label>Facebook (رابط)</label>
        <input name="facebook" dir="ltr" value="<?= e(setting('facebook', '')) ?>">
      </div>
      <div>
        <label>Instagram (رابط)</label>
        <input name="instagram" dir="ltr" value="<?= e(setting('instagram', '')) ?>">
      </div>
      <div>
        <label>WhatsApp (رقم)</label>
        <input name="whatsapp" dir="ltr" value="<?= e(setting('whatsapp', '')) ?>">
      </div>
    </div>
  </div>

  <button class="btn btn-primary btn-lg" type="submit">💾 حفظ الإعدادات</button>
</form>
