<?php
/**
 * سلة التسوق
 */
?>
<h1 class="page-title"><?= e(t('cart')) ?></h1>

<?php if (empty($items)): ?>
  <div class="empty-state">
    <p>🛒</p>
    <p><b><?= e(t('cart_empty')) ?></b></p>
    <p><?= e(t('cart_empty_text')) ?></p>
    <a class="btn btn-primary" href="<?= e(url('shop')) ?>"><?= e(t('continue_shopping')) ?></a>
  </div>
<?php else: ?>
  <form method="post" action="<?= e(url('cart/update')) ?>">
    <?= \App\Core\Csrf::field() ?>
    <table class="table cart-table">
      <thead>
        <tr>
          <th><?= e(t('product')) ?></th>
          <th><?= e(t('price')) ?></th>
          <th><?= e(t('quantity')) ?></th>
          <th><?= e(t('total')) ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item): ?>
          <tr>
            <td class="cart-product">
              <img src="<?= e(image_url($item['image'])) ?>" alt="">
              <a href="<?= e(url('product/' . $item['product_id'])) ?>"><?= e($item['name']) ?></a>
            </td>
            <td><?= e(money($item['price'])) ?></td>
            <td>
              <input type="number" name="qty[<?= $item['product_id'] ?>]" value="<?= $item['qty'] ?>" min="0" max="<?= max(1, $item['stock']) ?>">
            </td>
            <td><b><?= e(money($item['total'])) ?></b></td>
            <td>
              <button class="btn-remove" formaction="<?= e(url('cart/remove')) ?>" name="product_id" value="<?= $item['product_id'] ?>">✕</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="cart-actions">
      <button class="btn btn-muted" type="submit"><?= e(t('update_cart')) ?></button>
      <div class="cart-total">
        <span><?= e(t('subtotal')) ?>: <b><?= e(money($subtotal)) ?></b></span>
        <a class="btn btn-primary btn-lg" href="<?= e(url('checkout')) ?>"><?= e(t('proceed_checkout')) ?> ←</a>
      </div>
    </div>
  </form>
<?php endif; ?>
