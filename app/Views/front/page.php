<?php
/**
 * صفحة مخصصة
 */
?>
<article class="page-content card">
  <h1><?= e($page['title']) ?></h1>
  <div class="rich">
    <?= /* المحتوى يُدار من لوحة التحكم (مالك فقط) */ $page['content'] ?>
  </div>
</article>
