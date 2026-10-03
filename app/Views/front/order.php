<?php
/**
 * تفاصيل الطلب (حساب الزبون)
 */
?>
<nav class="breadcrumb">
  <a href="<?= e(url('account/orders')) ?>"><?= e(t('my_orders')) ?></a> /
  <span><?= e($order['order_number']) ?></span>
</nav>

<h1 class="page-title"><?= e(t('order_number')) ?>: <?= e($order['order_number']) ?></h1>

<div class="checkout-layout">
  <div class="card">
    <table class="table">
      <thead>
        <tr>
          <th><?= e(t('product')) ?></th>
          <th><?= e(t('qty')) ?></th>
          <th><?= e(t('unit_price')) ?></th>
          <th><?= e(t('total')) ?></th>
        </tr>
      </thead>
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
    </table>
  </div>

  <aside class="card order-summary">
    <h3><?= e(t('order_summary')) ?></h3>
    <div class="summary-row"><span><?= e(t('order_status')) ?></span>
      <b><span class="status status-<?= e($order['status']) ?>"><?= e(t('status_' . $order['status'])) ?></span></b>
    </div>
    <div class="summary-row"><span><?= e(t('order_date')) ?></span><b><?= e($order['created_at']) ?></b></div>
    <div class="summary-row"><span><?= e(t('wilaya')) ?></span><b><?= e($order['wilaya_name']) ?></b></div>
    <div class="summary-row"><span><?= e(t('delivery')) ?></span><b><?= e(money($order['delivery_price'])) ?></b></div>
    <div class="summary-row grand"><span><?= e(t('total')) ?></span><b><?= e(money($order['total'])) ?></b></div>
  </aside>
</div>
