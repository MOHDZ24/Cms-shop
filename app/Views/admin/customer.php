<?php
/**
 * عميل — تفاصيل وطلباته
 */
$statusLabels = [
    'new' => 'جديد', 'confirmed' => 'مؤكد', 'preparing' => 'قيد التجهيز',
    'shipped' => 'في الطريق', 'delivered' => 'تم التسليم', 'cancelled' => 'ملغى', 'returned' => 'مرتجع',
];
?>
<div class="toolbar">
  <a class="btn btn-muted" href="<?= e(url('admin/customers')) ?>">→ كل العملاء</a>
</div>

<div class="two-col">
  <div class="card">
    <h2>بيانات العميل</h2>
    <div class="info-list">
      <div><span>الاسم</span><b><?= e($customer['name']) ?></b></div>
      <div><span>الهاتف</span><b dir="ltr"><?= e($customer['phone']) ?></b></div>
      <div><span>العنوان</span><b><?= e($customer['address'] ?? '—') ?></b></div>
      <div><span>حساب مفعّل</span><b><?= $customer['password_hash'] !== null ? 'نعم' : 'لا (طلب كضيف)' ?></b></div>
      <div><span>منذ</span><b><?= e($customer['created_at']) ?></b></div>
    </div>
  </div>

  <div class="card">
    <h2>الطلبات (<?= count($orders) ?>)</h2>
    <table class="table">
      <thead><tr><th>رقم الطلب</th><th>المجموع</th><th>الحالة</th><th>التاريخ</th><th></th></tr></thead>
      <tbody>
        <?php if (empty($orders)): ?>
          <tr><td colspan="5">لا توجد طلبات</td></tr>
        <?php else: ?>
          <?php foreach ($orders as $order): ?>
            <tr>
              <td><b><?= e($order['order_number']) ?></b></td>
              <td><?= e(money($order['total'])) ?></td>
              <td><span class="status status-<?= e($order['status']) ?>"><?= e($statusLabels[$order['status']] ?? $order['status']) ?></span></td>
              <td><?= e($order['created_at']) ?></td>
              <td><a class="btn btn-muted btn-sm" href="<?= e(url('admin/orders/' . $order['id'])) ?>">عرض</a></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
