<?php
/**
 * صفحة المتجر — قائمة المنتجات مع الفلاتر
 */
?>
<div class="shop-layout">
  <aside class="filters card">
    <h3><?= e(t('filters')) ?></h3>
    <form method="get" action="<?= e(url('shop')) ?>">
      <?php if (!empty($filters['q'])): ?>
        <input type="hidden" name="q" value="<?= e($filters['q']) ?>">
      <?php endif; ?>

      <label><?= e(t('categories')) ?></label>
      <select name="category">
        <option value=""><?= e(t('all_categories')) ?></option>
        <?php foreach ($categories as $category): ?>
          <option value="<?= (int) $category['id'] ?>" <?= (int) ($filters['category'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>>
            <?= e($category['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label><?= e(t('price')) ?></label>
      <div class="price-range">
        <input type="number" name="min" placeholder="<?= e(t('price_from')) ?>" value="<?= e($filters['min'] ?? '') ?>">
        <input type="number" name="max" placeholder="<?= e(t('price_to')) ?>" value="<?= e($filters['max'] ?? '') ?>">
      </div>

      <label><?= e(t('sort')) ?></label>
      <select name="sort">
        <option value=""><?= e(t('sort_newest')) ?></option>
        <option value="price_asc" <?= ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>><?= e(t('sort_price_asc')) ?></option>
        <option value="price_desc" <?= ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>><?= e(t('sort_price_desc')) ?></option>
      </select>

      <button class="btn btn-primary btn-block" type="submit"><?= e(t('apply')) ?></button>
      <a class="btn btn-muted btn-block" href="<?= e(url('shop')) ?>"><?= e(t('reset')) ?></a>
    </form>
  </aside>

  <section class="shop-main">
    <div class="results-bar">
      <span><?= (int) $total ?> <?= e(t('results_found')) ?></span>
      <?php if (!empty($filters['q'])): ?>
        <span class="searched">🔍 <?= e($filters['q']) ?></span>
      <?php endif; ?>
    </div>

    <?php if (empty($products)): ?>
      <div class="empty-state">
        <p>😕</p>
        <p><?= e(t('no_results')) ?></p>
      </div>
    <?php else: ?>
      <div class="grid-products">
        <?php foreach ($products as $product): ?>
          <?= \App\Core\View::partial('front/_product_card', ['product' => $product]) ?>
        <?php endforeach; ?>
      </div>

      <?php if ($pages > 1): ?>
        <div class="pagination">
          <?php for ($i = 1; $i <= $pages; $i++): ?>
            <?php
              $query = $_GET;
              $query['page'] = $i;
              $link = url('shop?' . http_build_query($query));
            ?>
            <a class="<?= $i === $page ? 'active' : '' ?>" href="<?= e($link) ?>"><?= $i ?></a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </section>
</div>
