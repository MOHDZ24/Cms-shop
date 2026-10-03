<?php
/**
 * إتمام الطلب (كضيف أو بحساب)
 */
?>
<h1 class="page-title"><?= e(t('checkout_title')) ?></h1>

<div class="checkout-layout">
  <form method="post" action="<?= e(url('checkout')) ?>" class="card checkout-form">
    <?= \App\Core\Csrf::field() ?>

    <label><?= e(t('full_name')) ?> *</label>
    <input name="name" required value="<?= e($customer['name'] ?? '') ?>">

    <label><?= e(t('phone')) ?> *</label>
    <input name="phone" required dir="ltr" placeholder="0550 00 00 00" value="<?= e($customer['phone'] ?? '') ?>">

    <label><?= e(t('phone2')) ?></label>
    <input name="phone2" dir="ltr">

    <label><?= e(t('wilaya')) ?> *</label>
    <select name="wilaya_id" id="wilayaSelect" required>
      <option value=""><?= e(t('select_wilaya')) ?></option>
      <?php foreach ($wilayas as $wilaya): ?>
        <option value="<?= (int) $wilaya['id'] ?>" data-price="<?= e($wilaya['delivery_price']) ?>"
          <?= (int) ($customer['wilaya_id'] ?? 0) === (int) $wilaya['id'] ? 'selected' : '' ?>>
          <?= e(\App\Models\Wilaya::name($wilaya, \App\Core\Lang::code())) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label><?= e(t('address')) ?> *</label>
    <textarea name="address" required rows="3"><?= e($customer['address'] ?? '') ?></textarea>

    <label><?= e(t('notes')) ?></label>
    <textarea name="notes" rows="2"></textarea>

    <div class="payment-note">💵 <?= e(t('payment_cod')) ?> — <?= e(t('payment_note')) ?></div>

    <button class="btn btn-primary btn-lg btn-block" type="submit"><?= e(t('confirm_order')) ?></button>
    <p class="muted-note"><?= e(t('account_optional_note')) ?></p>
  </form>

  <aside class="card order-summary">
    <h3><?= e(t('order_summary')) ?></h3>
    <?php foreach ($items as $item): ?>
      <div class="summary-row">
        <span><?= e($item['name']) ?> × <?= $item['qty'] ?></span>
        <b><?= e(money($item['total'])) ?></b>
      </div>
    <?php endforeach; ?>
    <div class="summary-row">
      <span><?= e(t('subtotal')) ?></span>
      <b><?= e(money($subtotal)) ?></b>
    </div>
    <div class="summary-row">
      <span><?= e(t('delivery')) ?></span>
      <b id="deliveryPrice">—</b>
    </div>
    <div class="summary-row grand">
      <span><?= e(t('total')) ?></span>
      <b id="grandTotal"><?= e(money($subtotal)) ?></b>
    </div>
  </aside>
</div>

<script>
(function () {
  var select = document.getElementById('wilayaSelect');
  var base = <?= json_encode((float) $subtotal) ?>;
  var fmt = function (n) {
    return new Intl.NumberFormat('fr-DZ').format(Math.round(n)) + ' ' + <?= json_encode((string) setting('currency', 'DZD')) ?>;
  };
  function update() {
    var option = select.options[select.selectedIndex];
    var delivery = parseFloat(option && option.dataset.price ? option.dataset.price : 0) || 0;
    document.getElementById('deliveryPrice').textContent = option && option.value ? fmt(delivery) : '—';
    document.getElementById('grandTotal').textContent = fmt(base + delivery);
  }
  select.addEventListener('change', update);
  update();
})();
</script>
