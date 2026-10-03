<?php
/**
 * نجاح الطلب
 */
?>
<div class="empty-state success-state">
  <p>✅</p>
  <h1><?= e(t('order_success')) ?></h1>
  <p><?= e(t('order_success_text')) ?></p>
  <p class="order-number"><?= e(t('order_number')) ?>: <b><?= e($order['order_number']) ?></b></p>

  <div class="card summary-card">
    <div class="summary-row"><span><?= e(t('full_name')) ?></span><b><?= e($order['customer_name']) ?></b></div>
    <div class="summary-row"><span><?= e(t('phone')) ?></span><b dir="ltr"><?= e($order['phone']) ?></b></div>
    <div class="summary-row"><span><?= e(t('wilaya')) ?></span><b><?= e($order['wilaya_name']) ?></b></div>
    <div class="summary-row"><span><?= e(t('address')) ?></span><b><?= e($order['address']) ?></b></div>
    <div class="summary-row"><span><?= e(t('payment_cod')) ?></span><b><?= e(money($order['total'])) ?></b></div>
  </div>

  <a class="btn btn-primary" href="<?= e(url('')) ?>"><?= e(t('back_home')) ?></a>
</div>
