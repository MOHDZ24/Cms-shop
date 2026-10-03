<?php
/**
 * القالب العام للوحة التحكم
 * @var string $content
 */
use App\Core\Auth;

$admin = Auth::admin();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'لوحة التحكم') ?> — لوحة التحكم</title>
<link rel="stylesheet" href="<?= e(url('assets/css/admin.css')) ?>">
</head>
<body>
<div class="admin-wrap">
  <aside class="sidebar">
    <div class="sidebar-brand">🛍️ لوحة التحكم</div>
    <nav>
      <a href="<?= e(url('admin')) ?>">📊 الرئيسية</a>
      <?php if (Auth::check('orders')): ?>
        <a href="<?= e(url('admin/orders')) ?>">🧾 الطلبات</a>
        <a href="<?= e(url('admin/customers')) ?>">👥 العملاء</a>
      <?php endif; ?>
      <?php if (Auth::check('products')): ?>
        <a href="<?= e(url('admin/products')) ?>">📦 المنتجات</a>
        <a href="<?= e(url('admin/categories')) ?>">🗂️ الفئات</a>
        <a href="<?= e(url('admin/pages')) ?>">📄 الصفحات</a>
      <?php endif; ?>
      <?php if (Auth::check('wilayas')): ?>
        <a href="<?= e(url('admin/wilayas')) ?>">🚚 الولايات والتوصيل</a>
      <?php endif; ?>
      <?php if (Auth::check('settings')): ?>
        <a href="<?= e(url('admin/settings')) ?>">⚙️ الإعدادات</a>
      <?php endif; ?>
      <?php if (Auth::check('users')): ?>
        <a href="<?= e(url('admin/users')) ?>">🔑 المستخدمون</a>
      <?php endif; ?>
      <?php if (Auth::check('logs')): ?>
        <a href="<?= e(url('admin/logs')) ?>">🕘 سجل النشاط</a>
      <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
      <div class="admin-user"><?= e($admin['name'] ?? '') ?> <small>(<?= e($admin['role'] ?? '') ?>)</small></div>
      <a class="logout" href="<?= e(url('admin/logout')) ?>">تسجيل الخروج</a>
    </div>
  </aside>

  <main class="admin-main">
    <header class="admin-top">
      <h1><?= e($title ?? 'لوحة التحكم') ?></h1>
      <a class="view-site" href="<?= e(url('')) ?>" target="_blank" rel="noopener">عرض المتجر ↗</a>
    </header>

    <?php if (($msg = flash('success')) !== null): ?>
      <div class="alert alert-success"><?= e($msg) ?></div>
    <?php endif; ?>
    <?php if (($msg = flash('error')) !== null): ?>
      <div class="alert alert-error"><?= e($msg) ?></div>
    <?php endif; ?>

    <?= $content ?>
  </main>
</div>
</body>
</html>
