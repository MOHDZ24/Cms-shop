<?php
/**
 * الفاتورة (طباعة / حفظ PDF)
 */
$siteName = setting('site_name', 'Cms-shop');
?>
<div class="invoice-head">
  <div>
    <?php if (setting('logo', '') !== ''): ?>
      <img src="<?= e(image_url(setting('logo'))) ?>" alt="<?= e($siteName) ?>" style="max-height:64px">
    <?php endif; ?>
    <h1><?= e($siteName) ?></h1>
    <?php if (setting('address', '') !== ''): ?><div><?= e(setting('address')) ?></div><?php endif; ?>
    <?php if (setting('phone', '') !== ''): ?><div dir="ltr"><?= e(setting('phone')) ?></div><?php endif; ?>
  </div>
  <div class="invoice-meta">
    <h2><?= e(t('invoice')) ?></h2>
    <div><?= e(t('invoice_no')) ?>: <b><?= e($order['order_number']) ?></b></div>
    <div><?= e(t('order_date')) ?>: <?= e($order['created_at']) ?></div>
    <div><?= e(t('order_status')) ?>: <?= e($order['status']) ?></div>
  </div>
</div>

<div class="invoice-customer">
  <h3><?= e(t('bill_to')) ?></h3>
  <div><b><?= e($order['customer_name']) ?></b></div>
  <div dir="ltr"><?= e($order['phone']) ?></div>
  <div><?= e($order['address']) ?> — <?= e($order['wilaya_name']) ?></div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th><?= e(t('product')) ?></th>
      <th><?= e(t('qty')) ?></th>
      <th><?= e(t('unit_price')) ?></th>
      <th><?= e(t('total')) ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($items as $i => $item): ?>
      <tr>
        <td><?= $i + 1 ?></td>
        <td><?= e($item['product_name']) ?></td>
        <td><?= (int) $item['qty'] ?></td>
        <td><?= e(money($item['price'])) ?></td>
        <td><?= e(money($item['total'])) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<table class="totals">
  <tr><td><?= e(t('subtotal')) ?></td><td><?= e(money($order['subtotal'])) ?></td></tr>
  <tr><td><?= e(t('delivery')) ?> (<?= e($order['wilaya_name']) ?>)</td><td><?= e(money($order['delivery_price'])) ?></td></tr>
  <tr class="grand"><td><?= e(t('total')) ?></td><td><?= e(money($order['total'])) ?></td></tr>
</table>

<div class="invoice-footer">
  <p>💵 <?= e(t('payment_cod')) ?></p>
  <p><?= e(t('thank_you')) ?> 🌹</p>
</div>
