<?php
/**
 * الفئات — قائمة + نموذج
 */
use App\Core\Csrf;

$editingId = (int) ($editing['id'] ?? 0);
?>
<div class="two-col">
  <div class="card">
    <h2>الفئات الحالية</h2>
    <table class="table">
      <thead><tr><th>#</th><th>الاسم</th><th>الترتيب</th><th>الحالة</th><th></th></tr></thead>
      <tbody>
        <?php if (empty($categories)): ?>
          <tr><td colspan="5">لا توجد فئات</td></tr>
        <?php else: ?>
          <?php foreach ($categories as $category): ?>
            <tr>
              <td><?= (int) $category['id'] ?></td>
              <td><b><?= e($category['name']) ?></b></td>
              <td><?= (int) $category['position'] ?></td>
              <td><?= (int) $category['active'] === 1 ? '✓' : '✗' ?></td>
              <td class="row-actions">
                <a class="btn btn-muted btn-sm" href="<?= e(url('admin/categories?id=' . $category['id'])) ?>">تعديل</a>
                <form method="post" action="<?= e(url('admin/categories/delete')) ?>" class="inline-form" onsubmit="return confirm('حذف الفئة؟')">
                  <?= Csrf::field() ?>
                  <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                  <button class="btn btn-danger btn-sm" type="submit">حذف</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <form method="post" action="<?= e(url('admin/categories/save')) ?>" class="card form-stack">
    <h2><?= $editingId > 0 ? 'تعديل فئة' : 'فئة جديدة' ?></h2>
    <?= Csrf::field() ?>
    <input type="hidden" name="id" value="<?= $editingId ?>">

    <label>اسم الفئة (العربية) *</label>
    <input name="name_ar" value="<?= e($editingTranslations['ar'] ?? '') ?>">
    <label>اسم الفئة (الفرنسية)</label>
    <input name="name_fr" value="<?= e($editingTranslations['fr'] ?? '') ?>">
    <label>اسم الفئة (الإنجليزية)</label>
    <input name="name_en" value="<?= e($editingTranslations['en'] ?? '') ?>">
    <label>الترتيب (الأصغر أولًا)</label>
    <input type="number" name="position" value="<?= e($editing['position'] ?? '0') ?>">
    <label class="check"><input type="checkbox" name="active" <?= $editingId === 0 || (int) ($editing['active'] ?? 1) === 1 ? 'checked' : '' ?>> ظاهرة</label>

    <button class="btn btn-primary" type="submit">💾 حفظ</button>
  </form>
</div>
