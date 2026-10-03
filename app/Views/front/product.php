<?php
/**
 * صفحة المنتج
 * @var array $product
 */
use App\Models\Product;

$price = Product::price($product);
$hasSale = $product['sale_price'] !== null && (float) $product['sale_price'] < (float) $product['price'];
$inStock = (int) $product['stock'] > 0;
?>
<nav class="breadcrumb">
  <a href="<?= e(url('')) ?>"><?= e(t('home')) ?></a> /
  <a href="<?= e(url('shop')) ?>"><?= e(t('shop')) ?></a> /
  <span><?= e($product['name']) ?></span>
</nav>

<div class="product-page">
  <div class="product-gallery">
    <?php $mainImage = !empty($images) ? $images[0]['path'] : $product['image']; ?>
    <img id="mainImage" src="<?= e(image_url($mainImage)) ?>" alt="<?= e($product['name']) ?>">
    <?php if (count($images) > 1): ?>
      <div class="thumbs">
        <?php foreach ($images as $image): ?>
          <img src="<?= e(image_url($image['path'])) ?>" alt="" onclick="document.getElementById('mainImage').src=this.src">
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="product-info">
    <h1><?= e($product['name']) ?></h1>

    <div class="product-price big">
      <b><?= e(money($price)) ?></b>
      <?php if ($hasSale): ?><s><?= e(money($product['price'])) ?></s><?php endif; ?>
    </div>

    <p class="stock <?= $inStock ? 'in' : 'out' ?>">
      <?= $inStock ? '✓ ' . e(t('in_stock')) : '✗ ' . e(t('out_of_stock')) ?>
    </p>

    <?php if ($inStock): ?>
      <form method="post" action="<?= e(url('cart/add')) ?>" class="buy-form">
        <?= \App\Core\Csrf::field() ?>
        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
        <input type="number" name="qty" value="1" min="1" max="<?= (int) $product['stock'] ?>">
        <button class="btn btn-primary btn-lg" type="submit"><?= e(t('add_to_cart')) ?> 🛒</button>
      </form>
    <?php endif; ?>

    <div class="payment-note">💵 <?= e(t('payment_cod')) ?> — <?= e(t('payment_note')) ?></div>

    <?php if (!empty($product['description'])): ?>
      <div class="description">
        <h2><?= e(t('description')) ?></h2>
        <div class="rich"><?= nl2br(e($product['description'])) ?></div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php if (!empty($related)): ?>
<section class="section">
  <h2 class="section-title"><?= e(t('related_products')) ?></h2>
  <div class="grid-products">
    <?php foreach ($related as $product): ?>
      <?= \App\Core\View::partial('front/_product_card', ['product' => $product]) ?>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>
