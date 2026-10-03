/* Cms-shop — سلوكات بسيطة للواجهة */
document.addEventListener('DOMContentLoaded', function () {
  // إخفاء رسائل التنبيه تلقائيًا بعد 6 ثوانٍ
  document.querySelectorAll('.alert').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity .5s';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 500);
    }, 6000);
  });

  // تأكيد قبل الإجراءات الحساسة
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('submit', function (e) {
      if (!window.confirm(el.dataset.confirm)) e.preventDefault();
    });
  });
});
