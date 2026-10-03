<?php
/**
 * الدخول / إنشاء حساب (اختياري)
 */
?>
<div class="auth-layout">
  <form method="post" action="<?= e(url('account/login')) ?>" class="card auth-card">
    <h2><?= e(t('account_title')) ?></h2>
    <?= \App\Core\Csrf::field() ?>

    <label><?= e(t('phone')) ?></label>
    <input name="phone" required dir="ltr">

    <label><?= e(t('password')) ?></label>
    <input type="password" name="password" required>

    <button class="btn btn-primary btn-block" type="submit"><?= e(t('login')) ?></button>
  </form>

  <form method="post" action="<?= e(url('account/register')) ?>" class="card auth-card">
    <h2><?= e(t('register_title')) ?></h2>
    <?= \App\Core\Csrf::field() ?>

    <label><?= e(t('full_name')) ?></label>
    <input name="name" required>

    <label><?= e(t('phone')) ?></label>
    <input name="phone" required dir="ltr">

    <label><?= e(t('password')) ?></label>
    <input type="password" name="password" required minlength="6">

    <label><?= e(t('password_confirm')) ?></label>
    <input type="password" name="password_confirm" required>

    <button class="btn btn-primary btn-block" type="submit"><?= e(t('register')) ?></button>
    <p class="muted-note"><?= e(t('account_optional_note')) ?></p>
  </form>
</div>
