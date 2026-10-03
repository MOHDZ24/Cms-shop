<?php
/**
 * المستخدمون والصلاحيات (المالك فقط)
 */
use App\Core\Csrf;

$roleLabels = ['owner' => 'المالك (كل الصلاحيات)', 'orders' => 'موظف الطلبات', 'products' => 'موظف المنتجات'];
?>
<div class="two-col">
  <div class="card">
    <h2>المستخدمون</h2>
    <table class="table">
      <thead><tr><th>#</th><th>الاسم</th><th>البريد</th><th>الدور</th><th>نشط</th><th>آخر دخول</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($users as $user): ?>
          <tr>
            <td><?= (int) $user['id'] ?></td>
            <td><b><?= e($user['name']) ?></b></td>
            <td><?= e($user['email']) ?></td>
            <td><?= e($roleLabels[$user['role']] ?? $user['role']) ?></td>
            <td><?= (int) $user['active'] === 1 ? '✓' : '✗' ?></td>
            <td><?= e($user['last_login_at'] ?? '—') ?></td>
            <td class="row-actions">
              <a class="btn btn-muted btn-sm" href="<?= e(url('admin/users?edit=' . $user['id'])) ?>">تعديل</a>
              <form method="post" action="<?= e(url('admin/users/delete')) ?>" class="inline-form" onsubmit="return confirm('حذف المستخدم؟')">
                <?= Csrf::field() ?>
                <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                <button class="btn btn-danger btn-sm" type="submit">حذف</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <h2>إضافة/تعديل مستخدم</h2>
    <form method="post" action="<?= e(url('admin/users/save')) ?>" class="form-stack">
      <?= Csrf::field() ?>
      <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">

      <label>الاسم</label>
      <input name="name" required value="<?= e($editing['name'] ?? '') ?>">
      <label>البريد الإلكتروني (للدخول)</label>
      <input type="email" name="email" required value="<?= e($editing['email'] ?? '') ?>">
      <label>كلمة السر (6 أحرف على الأقل — اتركها فارغة للإبقاء عليها)</label>
      <input type="password" name="password">
      <label>الدور</label>
      <select name="role">
        <?php foreach (['products' => 'موظف المنتجات — يدير المنتجات والفئات والصفحات', 'orders' => 'موظف الطلبات — يدير الطلبات والعملاء والفواتير', 'owner' => 'المالك — كل الصلاحيات'] as $role => $label): ?>
          <option value="<?= $role ?>" <?= ($editing['role'] ?? '') === $role ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
      <label class="check"><input type="checkbox" name="active" <?= ($editing === null || (int) ($editing['active'] ?? 1) === 1) ? 'checked' : '' ?>> حساب نشط</label>
      <button class="btn btn-primary" type="submit">💾 حفظ المستخدم</button>
    </form>
    <p class="muted-note">الصلاحيات تُفرض دائمًا على الخادع (Server) — موظف الطلبات لا يستطيع تعديل الأسعار أو الإعدادات.</p>
  </div>
</div>
