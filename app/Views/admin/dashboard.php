<?php
/**
 * لوحة التحكم — الرئيسية
 */
?>
<div class="stat-grid">
  <div class="stat"><span>طلبات اليوم</span><b><?= (int) $stats['orders_today'] ?></b></div>
  <div class="stat"><span>طلبات جديدة</span><b><?= (int) $stats['new_orders'] ?></b></div>
  <div class="stat"><span>مبيعات الشهر</span><b><?= e(money($stats['revenue_month'])) ?></b></div>
  <div class="stat"><span>إجمالي الطلبات</span><b><?= (int) $stats['orders_total'] ?></b></div>
  <div class="stat"><span>المنتجات</span><b><?= (int) $stats['products'] ?></b></div>
  <div class="stat"><span>العملاء</span><b><?= (int) $stats['customers'] ?></b></div>
</div>

<div class="card">
  <h2>آخر الطلبات</h2>
  <table class="table">
    <thead>
      <tr><th>رقم الطلب</th><th>العميل</th><th>الولاية</th><th>المجموع</th><th>الحالة</th><th>التاريخ</th></tr>
    </thead>
    <tbody>
      <?php if (empty($latestOrders)): ?>
        <tr><td colspan="6">لا توجد طلبات بعد</td></tr>
      <?php else: ?>
        <?php foreach (array_slice($latestOrders, 0, 8) as $order): ?>
          <tr>
            <td><a href="<?= e(url('admin/orders/' . $order['id'])) ?>"><b><?= e($order['order_number']) ?></b></a></td>
            <td><?= e($order['customer_name']) ?></td>
            <td><?= e($order['wilaya_name']) ?></td>
            <td><?= e(money($order['total'])) ?></td>
            <td><span class="status status-<?= e($order['status']) ?>"><?= e($order['status']) ?></span></td>
            <td><?= e($order['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
