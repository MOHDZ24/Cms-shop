<?php
/**
 * تفاصيل الطلب (لوحة التحكم)
 */
use App\Core\Csrf;

$statusLabels = [
    'new' => 'جديد', 'confirmed' => 'مؤكد', 'preparing' => 'قيد التجهيز',
    'shipped' => 'في الطريق', 'delivered' => 'تم التسليم', 'cancelled' => 'ملغى', 'returned' => 'مرتجع',
];
?>
<div class="toolbar">
  <a class="btn btn-muted" href="<?= e(url('admin/orders')) ?>">→ كل الطلبات</a>
  <a class="btn btn-primary" target="_blank" href="<?= e(url('admin/orders/' . $order['id'] . '/invoice')) ?>">🧾 طباعة الفاتورة</a>
</div>

<div class="two-col">
  <div class="card">
    <h2>منتجات الطلب</h2>
    <table class="table">
      <thead><tr><th>المنتج</th><th>الكمية</th><th>السعر</th><th>المجموع</th></tr></thead>
      <tbody>
        <?php foreach ($items as $item): ?>
          <tr>
            <td><?= e($item['product_name']) ?></td>
            <td><?= (int) $item['qty'] ?></td>
            <td><?= e(money($item['price'])) ?></td>
            <td><b><?= e(money($item['total'])) ?></b></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr><td colspan="3">المجموع الفرعي</td><td><?= e(money($order['subtotal'])) ?></td></tr>
        <tr><td colspan="3">التوصيل (<?= e($order['wilaya_name']) ?>)</td><td><?= e(money($order['delivery_price'])) ?></td></tr>
        <tr class="grand"><td colspan="3">الإجمالي</td><td><?= e(money($order['total'])) ?></td></tr>
      </tfoot>
    </table>

    <h2>معلومات العميل</h2>
    <div class="info-list">
      <div><span>الاسم</span><b><?= e($order['customer_name']) ?></b></div>
      <div><span>الهاتف</span><b dir="ltr"><?= e($order['phone']) ?></b></div>
      <?php if (!empty($order['phone2'])): ?><div><span>هاتف إضافي</span><b dir="ltr"><?= e($order['phone2']) ?></b></div><?php endif; ?>
      <div><span>العنوان</span><b><?= e($order['address']) ?></b></div>
      <div><span>الولاية</span><b><?= e($order['wilaya_name']) ?></b></div>
      <div><span>الدفع</span><b>الدفع عند الاستلام</b></div>
      <?php if (!empty($order['notes'])): ?><div><span>ملاحظات</span><b><?= e($order['notes']) ?></b></div><?php endif; ?>
      <div><span>تاريخ الطلب</span><b><?= e($order['created_at']) ?></b></div>
    </div>
  </div>

  <div>
    <div class="card">
      <h2>تغيير الحالة</h2>
      <form method="post" action="<?= e(url('admin/orders/status')) ?>">
        <?= Csrf::field() ?>
        <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
        <label>الحالة الحالية: <span class="status status-<?= e($order['status']) ?>"><?= e($statusLabels[$order['status']] ?? $order['status']) ?></span></label>
        <select name="status">
          <?php foreach ($statuses as $status): ?>
            <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= e($statusLabels[$status]) ?></option>
          <?php endforeach; ?>
        </select>
        <label>ملاحظة (اختياري)</label>
        <input name="note" placeholder="مثال: تأكيد هاتفي مع العميل">
        <button class="btn btn-primary btn-block" type="submit">تحديث الحالة</button>
      </form>
      <p class="muted-note">عند الإلغاء أو الإرجاع تُعاد الكميات إلى المخزون تلقائيًا.</p>
    </div>

    <div class="card">
      <h2>سجل الطلب</h2>
      <div class="timeline">
        <?php foreach ($events as $event): ?>
          <div class="timeline-item">
            <b><?= e($event['action']) ?></b>
            <small><?= e($event['created_at']) ?> — <?= e($event['user_name'] ?? 'النظام') ?></small>
            <?php if (!empty($event['details'])): ?><p><?= e($event['details']) ?></p><?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="card">
      <form method="post" action="<?= e(url('admin/orders/delete')) ?>" onsubmit="return confirm('حذف الطلب نهائيًا؟')">
        <?= Csrf::field() ?>
        <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
        <button class="btn btn-danger btn-block" type="submit">حذف الطلب</button>
      </form>
    </div>
  </div>
</div>
