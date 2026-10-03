<?php
/**
 * دخول لوحة التحكم
 */
use App\Core\Csrf;
?>
<div class="login-wrap">
  <form method="post" action="<?= e(url('admin/login')) ?>" class="card login-card">
    <h1>🛍️ لوحة التحكم</h1>
    <p class="muted-note">سجّل الدخول لإدارة المتجر</p>
    <?= Csrf::field() ?>
    <label>البريد الإلكتروني</label>
    <input type="email" name="email" required>
    <label>كلمة السر</label>
    <input type="password" name="password" required>
    <button class="btn btn-primary btn-block" type="submit">تسجيل الدخول</button>
  </form>
</div>
