<?php
/**
 * قائمة الطلبات
 */
$statuses = ['new', 'confirmed', 'preparing', 'shipped', 'delivered', 'cancelled', 'returned'];
$statusLabels = [
    'new' => 'جديد', 'confirmed' => 'مؤكد', 'preparing' => 'قيد التجهيز',
    'shipped' => 'في الطريق', 'delivered' => 'تم التسليم', 'cancelled' => 'ملغى', 'returned' => 'مرتجع',
];
?>
<div class="toolbar">
  <form method="get" class="search-form">
    <input name="q" placeholder="رقم الطلب / العميل / الهاتف" value="<?= e($filters['q']) ?>">
    <input type="date" name="date" value="<?= e($filters['date']) ?>">
    <select name="status">
      <option value="">كل الحالات</option>
      <?php foreach ($statuses as $status): ?>
        <option value="<?= $status ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= e($statusLabels[$status]) ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-muted" type="submit">تصفية</button>
  </form>
</div>

<div class="card">
  <table class="table">
    <thead>
      <tr><th>رقم الطلب</th><th>العميل</th><th>الهاتف</th><th>الولاية</th><th>المجموع</th><th>الحالة</th><th>التاريخ</th><th></th></tr>
    </thead>
    <tbody>
      <?php if (empty($orders)): ?>
        <tr><td colspan="8">لا توجد طلبات مطابقة</td></tr>
      <?php else: ?>
        <?php foreach ($orders as $order): ?>
          <tr>
            <td><a href="<?= e(url('admin/orders/' . $order['id'])) ?>"><b><?= e($order['order_number']) ?></b></a></td>
            <td><?= e($order['customer_name']) ?></td>
            <td dir="ltr"><?= e($order['phone']) ?></td>
            <td><?= e($order['wilaya_name']) ?></td>
            <td><b><?= e(money($order['total'])) ?></b></td>
            <td><span class="status status-<?= e($order['status']) ?>"><?= e($statusLabels[$order['status']] ?? $order['status']) ?></span></td>
            <td><?= e($order['created_at']) ?></td>
            <td class="row-actions">
              <a class="btn btn-muted btn-sm" href="<?= e(url('admin/orders/' . $order['id'])) ?>">عرض</a>
              <a class="btn btn-muted btn-sm" target="_blank" href="<?= e(url('admin/orders/' . $order['id'] . '/invoice')) ?>">فاتورة</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
