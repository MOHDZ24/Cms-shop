<?php
/**
 * الصفحات المخصصة
 */
use App\Core\Csrf;

$editingId = (int) ($editing['id'] ?? 0);
?>
<div class="two-col">
  <div class="card">
    <h2>الصفحات</h2>
    <table class="table">
      <thead><tr><th>#</th><th>الرابط</th><th>العنوان</th><th>الحالة</th><th></th></tr></thead>
      <tbody>
        <?php if (empty($pages)): ?>
          <tr><td colspan="5">لا توجد صفحات</td></tr>
        <?php else: ?>
          <?php foreach ($pages as $page): ?>
            <tr>
              <td><?= (int) $page['id'] ?></td>
              <td><code>/page/<?= e($page['slug']) ?></code></td>
              <td><b><?= e($page['title'] ?? '—') ?></b></td>
              <td><?= (int) $page['active'] === 1 ? '✓' : '✗' ?></td>
              <td class="row-actions">
                <a class="btn btn-muted btn-sm" href="<?= e(url('admin/pages?id=' . $page['id'])) ?>">تعديل</a>
                <form method="post" action="<?= e(url('admin/pages/delete')) ?>" class="inline-form" onsubmit="return confirm('حذف الصفحة؟')">
                  <?= Csrf::field() ?>
                  <input type="hidden" name="id" value="<?= (int) $page['id'] ?>">
                  <button class="btn btn-danger btn-sm" type="submit">حذف</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <form method="post" action="<?= e(url('admin/pages/save')) ?>" class="card form-stack">
    <h2><?= $editingId > 0 ? 'تعديل صفحة' : 'صفحة جديدة' ?></h2>
    <?= Csrf::field() ?>
    <input type="hidden" name="id" value="<?= $editingId ?>">

    <label>الرابط (بالإنجليزية، مثال: about)</label>
    <input name="slug" value="<?= e($editing['slug'] ?? '') ?>" placeholder="about">
    <label>الترتيب في القائمة</label>
    <input type="number" name="position" value="<?= e($editing['position'] ?? '0') ?>">
    <label class="check"><input type="checkbox" name="active" <?= $editingId === 0 || (int) ($editing['active'] ?? 1) === 1 ? 'checked' : '' ?>> ظاهرة في القائمة</label>

    <?php foreach (['ar' => 'العربية', 'fr' => 'الفرنسية', 'en' => 'الإنجليزية'] as $lang => $label): ?>
      <label>العنوان (<?= e($label) ?>)</label>
      <input name="title_<?= $lang ?>" value="<?= e($editingTranslations[$lang]['title'] ?? '') ?>">
      <label>المحتوى (<?= e($label) ?>)</label>
      <textarea name="content_<?= $lang ?>" rows="5"><?= e($editingTranslations[$lang]['content'] ?? '') ?></textarea>
    <?php endforeach; ?>

    <button class="btn btn-primary" type="submit">💾 حفظ الصفحة</button>
  </form>
</div>
