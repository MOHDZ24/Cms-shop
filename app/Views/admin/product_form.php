<?php
/**
 * نموذج المنتج (إضافة/تعديل) — 3 لغات
 * @var ?array $product
 */
use App\Core\Csrf;

$id = (int) ($product['id'] ?? 0);
?>
<form method="post" action="<?= e(url('admin/products/save')) ?>" enctype="multipart/form-data" class="form-stack">
  <?= Csrf::field() ?>
  <input type="hidden" name="id" value="<?= $id ?>">
  <?php if ($product !== null && !empty($product['image'])): ?>
    <input type="hidden" name="current_image" value="<?= e($product['image']) ?>">
  <?php endif; ?>

  <div class="card">
    <h2>البيانات الأساسية</h2>
    <div class="grid-2">
      <div>
        <label>السعر (<?= e(setting('currency', 'DZD')) ?>) *</label>
        <input type="number" step="0.01" name="price" required value="<?= e($product['price'] ?? '') ?>">
      </div>
      <div>
        <label>سعر التخفيض (اختياري)</label>
        <input type="number" step="0.01" name="sale_price" value="<?= e($product['sale_price'] ?? '') ?>">
      </div>
      <div>
        <label>المخزون (الكمية)</label>
        <input type="number" name="stock" value="<?= e($product['stock'] ?? '0') ?>">
      </div>
      <div>
        <label>SKU (رمز المنتج)</label>
        <input name="sku" value="<?= e($product['sku'] ?? '') ?>">
      </div>
      <div>
        <label>الفئة</label>
        <select name="category_id">
          <option value="">بدون فئة</option>
          <?php foreach ($categories as $category): ?>
            <option value="<?= (int) $category['id'] ?>" <?= (int) ($product['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>>
              <?= e($category['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label>الصورة الرئيسية</label>
        <input type="file" name="image" accept="image/*">
        <?php if ($product !== null && !empty($product['image'])): ?>
          <img class="thumb" src="<?= e(image_url($product['image'])) ?>" alt="">
        <?php endif; ?>
      </div>
    </div>
    <div class="checks">
      <label class="check"><input type="checkbox" name="active" <?= ($product === null || (int) ($product['active'] ?? 1) === 1) ? 'checked' : '' ?>> ظاهر في المتجر</label>
      <label class="check"><input type="checkbox" name="featured" <?= (int) ($product['featured'] ?? 0) === 1 ? 'checked' : '' ?>> منتج مميز (الرئيسية)</label>
    </div>
  </div>

  <?php foreach (['ar' => 'العربية', 'fr' => 'الفرنسية', 'en' => 'الإنجليزية'] as $lang => $label): ?>
    <div class="card">
      <h2>المحتوى — <?= e($label) ?></h2>
      <label>اسم المنتج <?= $lang === setting('default_lang', 'ar') ? '*' : '' ?></label>
      <input name="name_<?= $lang ?>" value="<?= e($translations[$lang]['name'] ?? '') ?>">
      <label>الوصف</label>
      <textarea name="description_<?= $lang ?>" rows="5"><?= e($translations[$lang]['description'] ?? '') ?></textarea>
    </div>
  <?php endforeach; ?>

  <button class="btn btn-primary btn-lg" type="submit">💾 حفظ المنتج</button>
</form>
