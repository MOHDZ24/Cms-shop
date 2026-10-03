<?php
/**
 * القالب العام للواجهة الأمامية
 * @var string $content
 */
use App\Core\Auth;
use App\Core\Cart;
use App\Core\Lang;

$siteName = setting('site_name', 'Cms-shop');
$logo = setting('logo', '');
$primary = setting('primary_color', '#1a73e8');
$dir = Lang::dir();
?>
<!DOCTYPE html>
<html lang="<?= e(Lang::code()) ?>" dir="<?= e($dir) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? $siteName) ?> — <?= e($siteName) ?></title>
<meta name="description" content="<?= e(setting('address', $siteName)) ?>">
<link rel="stylesheet" href="<?= e(url('assets/css/app.css')) ?>">
<style>:root{--primary:<?= e($primary) ?>}</style>
</head>
<body>
<header class="topbar">
  <div class="container topbar-inner">
    <a class="brand" href="<?= e(url('')) ?>">
      <?php if ($logo !== ''): ?>
        <img src="<?= e(image_url($logo)) ?>" alt="<?= e($siteName) ?>">
      <?php else: ?>
        <span class="brand-icon">🛍️</span>
      <?php endif; ?>
      <span class="brand-name"><?= e($siteName) ?></span>
    </a>

    <form class="search" action="<?= e(url('shop')) ?>" method="get">
      <input type="search" name="q" placeholder="<?= e(t('search_placeholder')) ?>" value="<?= e($_GET['q'] ?? '') ?>">
      <button type="submit" aria-label="<?= e(t('search')) ?>">🔍</button>
    </form>

    <div class="top-actions">
      <div class="lang-switch">
        <?php foreach (['ar', 'fr', 'en'] as $code): ?>
          <a class="<?= Lang::code() === $code ? 'active' : '' ?>" href="<?= e(url('lang/' . $code)) ?>"><?= strtoupper($code) ?></a>
        <?php endforeach; ?>
      </div>
      <a class="icon-link" href="<?= e(url('account')) ?>">👤<span><?= e(t('account')) ?></span></a>
      <a class="icon-link cart-link" href="<?= e(url('cart')) ?>">🛒<span><?= e(t('cart')) ?></span>
        <b class="badge"><?= Cart::count() ?></b>
      </a>
    </div>
  </div>

  <nav class="mainnav">
    <div class="container mainnav-inner">
      <a href="<?= e(url('')) ?>"><?= e(t('home')) ?></a>
      <a href="<?= e(url('shop')) ?>"><?= e(t('shop')) ?></a>
      <?php foreach ($navPages ?? [] as $navPage): ?>
        <a href="<?= e(url('page/' . $navPage['slug'])) ?>"><?= e($navPage['title']) ?></a>
      <?php endforeach; ?>
      <?php if (setting('phone', '') !== ''): ?>
        <a class="phone" href="tel:+213<?= e(preg_replace('/\D/', '', (string) setting('phone'))) ?>">📞 <?= e(setting('phone')) ?></a>
      <?php endif; ?>
    </div>
  </nav>
</header>

<main class="container main">
  <?php if (($msg = flash('success')) !== null): ?>
    <div class="alert alert-success"><?= e($msg) ?></div>
  <?php endif; ?>
  <?php if (($msg = flash('error')) !== null): ?>
    <div class="alert alert-error"><?= e($msg) ?></div>
  <?php endif; ?>

  <?= $content ?>
</main>

<footer class="footer">
  <div class="container footer-grid">
    <div>
      <h3><?= e($siteName) ?></h3>
      <p><?= e(t('payment_cod')) ?> — <?= e(t('payment_note')) ?></p>
      <?php if (setting('address', '') !== ''): ?><p>📍 <?= e(setting('address')) ?></p><?php endif; ?>
      <?php if (setting('phone', '') !== ''): ?><p>📞 <?= e(setting('phone')) ?></p><?php endif; ?>
    </div>
    <div>
      <h3><?= e(t('categories')) ?></h3>
      <a href="<?= e(url('shop')) ?>"><?= e(t('all_categories')) ?></a>
    </div>
    <div>
      <h3><?= e(t('about')) ?></h3>
      <?php foreach ($navPages ?? [] as $navPage): ?>
        <a href="<?= e(url('page/' . $navPage['slug'])) ?>"><?= e($navPage['title']) ?></a>
      <?php endforeach; ?>
    </div>
    <div>
      <h3><?= e(t('follow_us')) ?></h3>
      <?php if (setting('facebook', '') !== ''): ?><a href="<?= e(setting('facebook')) ?>" target="_blank" rel="noopener">Facebook</a><?php endif; ?>
      <?php if (setting('instagram', '') !== ''): ?><a href="<?= e(setting('instagram')) ?>" target="_blank" rel="noopener">Instagram</a><?php endif; ?>
    </div>
  </div>
  <div class="copyright">© <?= date('Y') ?> <?= e($siteName) ?> — <?= e(t('footer_rights')) ?></div>
</footer>
<script src="<?= e(url('assets/js/app.js')) ?>"></script>
</body>
</html>
