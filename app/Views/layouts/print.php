<?php
/**
 * قالب الطباعة (الفواتير)
 * @var string $content
 */
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<title><?= e($title ?? 'فاتورة') ?></title>
<link rel="stylesheet" href="<?= e(url('assets/css/app.css')) ?>">
<style>
  body{background:#fff;font-family:Tahoma,Arial,sans-serif;color:#111}
  .print-wrap{max-width:800px;margin:24px auto;padding:28px;border:1px solid #e2e8f0;border-radius:10px}
  .no-print{max-width:800px;margin:16px auto;display:flex;gap:10px}
  .no-print button{background:#1a73e8;color:#fff;border:0;padding:10px 18px;border-radius:8px;cursor:pointer;font-size:14px}
  table{width:100%;border-collapse:collapse;margin:16px 0}
  th,td{border:1px solid #cbd5e1;padding:10px;text-align:right;font-size:14px}
  th{background:#f1f5f9}
  .totals td{border:0;padding:6px 10px;font-size:15px}
  .totals .grand td{font-size:18px;font-weight:bold;border-top:2px solid #111}
  @media print{.no-print{display:none}.print-wrap{border:0;margin:0}}
</style>
</head>
<body>
<div class="no-print">
  <button onclick="window.print()"><?= e(t('print')) ?> 🖨️</button>
  <button onclick="history.back()">رجوع</button>
</div>
<div class="print-wrap">
  <?= $content ?>
</div>
</body>
</html>
