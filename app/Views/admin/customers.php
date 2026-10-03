<?php
/**
 * العملاء
 */
?>
<div class="toolbar">
  <form method="get" class="search-form">
    <input name="q" placeholder="بحث بالاسم أو الهاتف" value="<?= e($q) ?>">
    <button class="btn btn-muted" type="submit">بحث</button>
  </form>
</div>

<div class="card">
  <table class="table">
    <thead><tr><th>#</th><th>الاسم</th><th>الهاتف</th><th>الطلبات</th><th>مسجّل حساب</th><th>منذ</th><th></th></tr></thead>
    <tbody>
      <?php if (empty($customers)): ?>
        <tr><td colspan="7">لا يوجد عملاء</td></tr>
      <?php else: ?>
        <?php foreach ($customers as $customer): ?>
          <tr>
            <td><?= (int) $customer['id'] ?></td>
            <td><b><?= e($customer['name']) ?></b></td>
            <td dir="ltr"><?= e($customer['phone']) ?></td>
            <td><?= (int) $customer['orders_count'] ?></td>
            <td><?= $customer['password_hash'] !== null ? '✓' : '—' ?></td>
            <td><?= e($customer['created_at']) ?></td>
            <td><a class="btn btn-muted btn-sm" href="<?= e(url('admin/customers/' . $customer['id'])) ?>">عرض</a></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
