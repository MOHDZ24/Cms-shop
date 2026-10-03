<?php
/**
 * الصفحة الرئيسية
 */
?>
<section class="hero">
  <div class="hero-text">
    <h1><?= e(t('hero_title')) ?></h1>
    <p><?= e(t('hero_text')) ?></p>
    <a class="btn btn-primary btn-lg" href="<?= e(url('shop')) ?>"><?= e(t('shop_now')) ?></a>
  </div>
</section>

<?php if (!empty($categories)): ?>
<section class="section">
  <h2 class="section-title"><?= e(t('categories_title')) ?></h2>
  <div class="cat-strip">
    <?php foreach ($categories as $category): ?>
      <a class="cat-chip" href="<?= e(url('shop?category=' . $category['id'])) ?>">
        <?php if (!empty($category['image'])): ?>
          <img src="<?= e(image_url($category['image'])) ?>" alt="<?= e($category['name']) ?>">
        <?php else: ?>
          <span class="cat-emoji">🏷️</span>
        <?php endif; ?>
        <?= e($category['name']) ?>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($featured)): ?>
<section class="section">
  <h2 class="section-title"><?= e(t('featured_products')) ?></h2>
  <div class="grid-products">
    <?php foreach ($featured as $product): ?>
      <?= \App\Core\View::partial('front/_product_card', ['product' => $product]) ?>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($latest)): ?>
<section class="section">
  <h2 class="section-title"><?= e(t('new_products')) ?></h2>
  <div class="grid-products">
    <?php foreach ($latest as $product): ?>
      <?= \App\Core\View::partial('front/_product_card', ['product' => $product]) ?>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>
