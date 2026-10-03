<?php
/**
 * سجل النشاط
 */
?>
<div class="card">
  <h2>آخر التغييرات (من غيّر ماذا ومتى)</h2>
  <table class="table">
    <thead><tr><th>#</th><th>المستخدم</th><th>العنصر</th><th>الإجراء</th><th>التفاصيل</th><th>التاريخ</th></tr></thead>
    <tbody>
      <?php if (empty($logs)): ?>
        <tr><td colspan="6">لا يوجد نشاط مسجل</td></tr>
      <?php else: ?>
        <?php foreach ($logs as $log): ?>
          <tr>
            <td><?= (int) $log['id'] ?></td>
            <td><?= e($log['user_name'] ?? 'النظام') ?></td>
            <td><?= e($log['entity']) ?> <?= $log['entity_id'] !== null ? '#' . (int) $log['entity_id'] : '' ?></td>
            <td><code><?= e($log['action']) ?></code></td>
            <td><?= e($log['details'] ?? '') ?></td>
            <td><?= e($log['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
