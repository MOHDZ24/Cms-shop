<?php
declare(strict_types=1);

/**
 * معالج التثبيت — Cms-shop
 * يُشغَّل مرة واحدة فقط، ثم يقفل نفسه بملف install.lock
 */

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Csrf;

$lockFile = __DIR__ . '/install.lock';
$configFile = APP_ROOT . '/config/config.php';
$installed = is_file($lockFile) && is_file($configFile);

$step = get('step', 'check');
$error = '';
$done = false;

/* ---------- التحقق من المتطلبات ---------- */
$requirements = [
    'PHP 8.0+' => version_compare(PHP_VERSION, '8.0.0', '>='),
    'PDO' => extension_loaded('pdo'),
    'PDO MySQL' => extension_loaded('pdo_mysql'),
    'mbstring' => extension_loaded('mbstring'),
    'config/ قابل للكتابة' => is_writable(APP_ROOT . '/config'),
    'uploads/ قابل للكتابة' => is_writable(APP_ROOT . '/uploads'),
];
$requirementsOk = !in_array(false, $requirements, true);

/* ---------- تنفيذ التثبيت ---------- */
if ($installed === false && is_post() && ($step === 'install')) {
    Csrf::guard();

    $dbHost = (string) post('db_host', 'localhost');
    $dbPort = (string) post('db_port', '3306');
    $dbName = (string) post('db_name');
    $dbUser = (string) post('db_user');
    $dbPass = (string) post('db_pass', '');
    $siteName = (string) post('site_name');
    $adminName = (string) post('admin_name');
    $adminEmail = strtolower((string) post('admin_email'));
    $adminPass = (string) post('admin_pass');

    if ($dbName === '' || $dbUser === '' || $siteName === '' || $adminName === '' || $adminEmail === '' || strlen($adminPass) < 6) {
        $error = 'يرجى ملء جميع الحقول المطلوبة (كلمة سر المدير 6 أحرف على الأقل)';
        $step = 'form';
    } elseif (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        $error = 'البريد الإلكتروني للمدير غير صالح';
        $step = 'form';
    } else {
        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $dbHost, $dbPort, $dbName),
                $dbUser,
                $dbPass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            /* تشغيل الهيكل */
            $sql = file_get_contents(APP_ROOT . '/install/schema.sql');
            foreach (explode(';', $sql) as $statement) {
                $statement = trim(preg_replace('/^\s*--.*$/m', '', $statement));
                if ($statement !== '') {
                    $pdo->exec($statement);
                }
            }

            /* الإعدادات */
            $insertSetting = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)');
            $defaults = [
                'site_name' => $siteName,
                'default_lang' => 'ar',
                'currency' => 'DZD',
                'logo' => '',
                'primary_color' => '#1a73e8',
                'phone' => '',
                'address' => '',
                'delivery_enabled' => '1',
            ];
            foreach ($defaults as $key => $value) {
                $insertSetting->execute([$key, $value]);
            }

            /* حساب المالك */
            $pdo->prepare('INSERT INTO users (name, email, password_hash, role, active, created_at) VALUES (?, ?, ?, ?, 1, ?)')
                ->execute([$adminName, $adminEmail, password_hash($adminPass, PASSWORD_DEFAULT), 'owner', date('Y-m-d H:i:s')]);

            /* حفظ الإعدادات */
            $config = [
                'db' => [
                    'driver' => 'mysql',
                    'host' => $dbHost,
                    'port' => $dbPort,
                    'name' => $dbName,
                    'user' => $dbUser,
                    'pass' => $dbPass,
                    'charset' => 'utf8mb4',
                ],
                'app' => [
                    'url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
                        . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_URL,
                    'installed_at' => date('Y-m-d H:i:s'),
                ],
            ];
            file_put_contents($configFile, "<?php\nreturn " . var_export($config, true) . ";\n");
            @chmod($configFile, 0640);
            file_put_contents($lockFile, date('Y-m-d H:i:s'));

            log_activity(null, 'system', null, 'install', 'تم تثبيت المتجر: ' . $siteName);
            $done = true;
            $step = 'done';
        } catch (Throwable $e) {
            $error = 'خطأ أثناء التثبيت: ' . $e->getMessage();
            $step = 'form';
        }
    }
}

/* ---------- عرض الصفحة ---------- */
$title = 'تثبيت Cms-shop';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($title) ?></title>
<style>
  body{font-family:Tahoma,Arial,sans-serif;background:#f1f5f9;margin:0;padding:24px;color:#1e293b}
  .box{max-width:720px;margin:0 auto;background:#fff;border-radius:14px;padding:28px;box-shadow:0 8px 30px rgba(15,23,42,.08)}
  h1{margin:0 0 6px;font-size:22px;color:#1a73e8}
  h2{font-size:17px;margin:22px 0 10px}
  ul{list-style:none;padding:0;margin:0}
  li{padding:8px 12px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between}
  .ok{color:#16a34a;font-weight:bold}.bad{color:#dc2626;font-weight:bold}
  label{display:block;margin:12px 0 4px;font-size:14px;font-weight:bold}
  input{width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;box-sizing:border-box;font-size:14px}
  .grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
  button{margin-top:18px;background:#1a73e8;color:#fff;border:0;padding:12px 22px;border-radius:8px;font-size:15px;cursor:pointer}
  button:disabled{background:#94a3b8;cursor:not-allowed}
  .error{background:#fef2f2;color:#b91c1c;padding:12px;border-radius:8px;margin:14px 0}
  .success{background:#f0fdf4;color:#15803d;padding:14px;border-radius:8px;margin:14px 0}
  .note{background:#eff6ff;color:#1d4ed8;padding:12px;border-radius:8px;font-size:13px;margin-top:16px}
  a.btn{display:inline-block;background:#1a73e8;color:#fff;text-decoration:none;padding:11px 20px;border-radius:8px;margin:6px 6px 0 0}
</style>
</head>
<body>
<div class="box">
  <h1>🛍️ تثبيت Cms-shop</h1>
  <p>معالج تثبيت المتجر — املأ البيانات وابدأ التثبيت.</p>

  <?php if ($error !== ''): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($installed): ?>
    <div class="success">المتجر مثبَّت مسبقًا. إذا كنت تريد إعادة التثبيت، احذف الملفين <code>install/install.lock</code> و<code>config/config.php</code>.</div>
  <?php elseif ($done || $step === 'done'): ?>
    <div class="success">
      ✅ تم التثبيت بنجاح!<br>
      يمكنك الآن الدخول إلى لوحة التحكم وبدء إضافة المنتجات.
    </div>
    <a class="btn" href="<?= htmlspecialchars(url('admin')) ?>">لوحة التحكم</a>
    <a class="btn" href="<?= htmlspecialchars(url('')) ?>">زيارة المتجر</a>
    <div class="note">⚠️ لأسباب أمنية: احذف مجلد <code>install</code> من الاستضافة بعد التأكد من عمل المتجر.</div>
  <?php else: ?>

    <h2>1) فحص المتطلبات</h2>
    <ul>
      <?php foreach ($requirements as $label => $ok): ?>
        <li><span><?= htmlspecialchars($label) ?></span><span class="<?= $ok ? 'ok' : 'bad' ?>"><?= $ok ? '✓ جاهز' : '✗ غير متوفر' ?></span></li>
      <?php endforeach; ?>
    </ul>

    <?php if ($requirementsOk): ?>
      <h2>2) بيانات التثبيت</h2>
      <form method="post" action="?step=install">
        <?= Csrf::field() ?>
        <div class="grid">
          <div>
            <label>اسم قاعدة البيانات *</label>
            <input name="db_name" required value="<?= htmlspecialchars(post('db_name', '')) ?>">
          </div>
          <div>
            <label>مستخدم قاعدة البيانات *</label>
            <input name="db_user" required value="<?= htmlspecialchars(post('db_user', '')) ?>">
          </div>
          <div>
            <label>كلمة سر قاعدة البيانات</label>
            <input name="db_pass" type="password">
          </div>
          <div>
            <label>مضيف قاعدة البيانات</label>
            <input name="db_host" value="<?= htmlspecialchars(post('db_host', 'localhost')) ?>">
          </div>
          <div>
            <label>المنفذ (Port)</label>
            <input name="db_port" value="<?= htmlspecialchars(post('db_port', '3306')) ?>">
          </div>
          <div>
            <label>اسم المتجر *</label>
            <input name="site_name" required value="<?= htmlspecialchars(post('site_name', 'Nasser Shop')) ?>">
          </div>
          <div>
            <label>اسم المدير *</label>
            <input name="admin_name" required value="<?= htmlspecialchars(post('admin_name', '')) ?>">
          </div>
          <div>
            <label>بريد المدير (للدخول) *</label>
            <input name="admin_email" type="email" required value="<?= htmlspecialchars(post('admin_email', '')) ?>">
          </div>
          <div>
            <label>كلمة سر المدير * (6 أحرف+)</label>
            <input name="admin_pass" type="password" required minlength="6">
          </div>
        </div>
        <button type="submit">تثبيت الآن 🚀</button>
      </form>
    <?php else: ?>
      <div class="error">يرجى حل مشاكل المتطلبات أعلاه ثم تحديث الصفحة.</div>
    <?php endif; ?>
  <?php endif; ?>
</div>
</body>
</html>
