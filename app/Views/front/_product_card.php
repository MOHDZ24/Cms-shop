<?php
/**
 * بطاقة منتج — تُستخدم في الرئيسية وصفحة المتجر
 * @var array $product
 */
use App\Models\Product;

$price = Product::price($product);
$hasSale = $product['sale_price'] !== null && (float) $product['sale_price'] < (float) $product['price'];
?>
<div class="card product-card">
  <a class="product-img" href="<?= e(url('product/' . $product['id'])) ?>">
    <?php if ($hasSale): ?><span class="tag-sale"><?= e(t('sale')) ?></span><?php endif; ?>
    <img src="<?= e(image_url($product['image'])) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
  </a>
  <div class="product-body">
    <a class="product-name" href="<?= e(url('product/' . $product['id'])) ?>"><?= e($product['name']) ?></a>
    <div class="product-price">
      <b><?= e(money($price)) ?></b>
      <?php if ($hasSale): ?><s><?= e(money($product['price'])) ?></s><?php endif; ?>
    </div>
    <?php if ((int) $product['stock'] > 0): ?>
      <form method="post" action="<?= e(url('cart/add')) ?>">
        <?= \App\Core\Csrf::field() ?>
        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
        <button class="btn btn-primary btn-block" type="submit"><?= e(t('add_to_cart')) ?></button>
      </form>
    <?php else: ?>
      <button class="btn btn-muted btn-block" disabled><?= e(t('out_of_stock')) ?></button>
    <?php endif; ?>
  </div>
</div>
