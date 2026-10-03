<?php
/**
 * طلباتي (حساب الزبون)
 */
?>
<h1 class="page-title"><?= e(t('welcome')) ?>, <?= e($customer['name']) ?> — <?= e(t('my_orders')) ?></h1>

<?php if (empty($orders)): ?>
  <div class="empty-state">
    <p>📦</p>
    <p><?= e(t('no_orders')) ?></p>
    <a class="btn btn-primary" href="<?= e(url('shop')) ?>"><?= e(t('continue_shopping')) ?></a>
  </div>
<?php else: ?>
  <table class="table">
    <thead>
      <tr>
        <th><?= e(t('order_number')) ?></th>
        <th><?= e(t('order_date')) ?></th>
        <th><?= e(t('total')) ?></th>
        <th><?= e(t('order_status')) ?></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($orders as $order): ?>
        <tr>
          <td><b><?= e($order['order_number']) ?></b></td>
          <td><?= e($order['created_at']) ?></td>
          <td><?= e(money($order['total'])) ?></td>
          <td><span class="status status-<?= e($order['status']) ?>"><?= e(t('status_' . $order['status'])) ?></span></td>
          <td><a class="btn btn-muted btn-sm" href="<?= e(url('account/order/' . $order['id'])) ?>"><?= e(t('view_order')) ?></a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
