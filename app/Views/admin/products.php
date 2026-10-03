<?php
/**
 * قائمة المنتجات
 */
use App\Core\Csrf;
?>
<div class="toolbar">
  <form method="get" class="search-form">
    <input name="q" placeholder="بحث عن منتج..." value="<?= e($q) ?>">
    <button class="btn btn-muted" type="submit">بحث</button>
  </form>
  <a class="btn btn-primary" href="<?= e(url('admin/products/new')) ?>">+ منتج جديد</a>
</div>

<div class="card">
  <table class="table">
    <thead>
      <tr><th>#</th><th>الصورة</th><th>الاسم</th><th>SKU</th><th>السعر</th><th>المخزون</th><th>الحالة</th><th></th></tr>
    </thead>
    <tbody>
      <?php if (empty($products)): ?>
        <tr><td colspan="8">لا توجد منتجات — ابدأ بإضافة منتج جديد</td></tr>
      <?php else: ?>
        <?php foreach ($products as $product): ?>
          <tr>
            <td><?= (int) $product['id'] ?></td>
            <td><img class="thumb" src="<?= e(image_url($product['image'])) ?>" alt=""></td>
            <td><a href="<?= e(url('admin/products/' . $product['id'])) ?>"><b><?= e($product['name'] ?? '—') ?></b></a></td>
            <td><?= e($product['sku'] ?? '—') ?></td>
            <td>
              <?= e(money($product['sale_price'] ?? $product['price'])) ?>
              <?php if ($product['sale_price'] !== null): ?><br><s><?= e(money($product['price'])) ?></s><?php endif; ?>
            </td>
            <td><?= (int) $product['stock'] ?></td>
            <td>
              <?= (int) $product['active'] === 1 ? '✓ ظاهر' : '✗ مخفي' ?>
              <?= (int) $product['featured'] === 1 ? ' ⭐' : '' ?>
            </td>
            <td class="row-actions">
              <a class="btn btn-muted btn-sm" href="<?= e(url('admin/products/' . $product['id'])) ?>">تعديل</a>
              <form method="post" action="<?= e(url('admin/products/delete')) ?>" class="inline-form" onsubmit="return confirm('حذف المنتج نهائيًا؟')">
                <?= Csrf::field() ?>
                <button class="btn btn-danger btn-sm" type="submit">حذف</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
