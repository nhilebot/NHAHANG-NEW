/* Bọc mọi <table> trong khung cuộn ngang; điều khiển sidebar admin trên mobile */
(function () {
  var mq = window.matchMedia('(max-width: 991px)');
  function wrapTables() {
    if (!mq.matches) return;            // desktop: không đụng vào bảng
    document.querySelectorAll('table').forEach(function (t) {
      if (t.classList.contains('table-order') || t.closest('.table-scroll') || t.closest('.r-table') || t.closest('.receipt')) return;
      var w = document.createElement('div');
      w.className = 'table-scroll';
      t.parentNode.insertBefore(w, t);
      w.appendChild(t);
    });
  }
  function initSidebar() {
    var btn = document.querySelector('.menu-btn');
    var sb = document.querySelector('.sidebar');
    if (!btn || !sb) return;
    var bd = document.createElement('div');
    bd.className = 'sidebar-backdrop';
    document.body.appendChild(bd);
    function mobile() { return window.matchMedia('(max-width: 991px)').matches; }
    btn.addEventListener('click', function () {
      document.body.classList.toggle(mobile() ? 'sidebar-open' : 'sidebar-collapsed');
    });
    bd.addEventListener('click', function () { document.body.classList.remove('sidebar-open'); });
    sb.querySelectorAll('a[href]:not([href="#"])').forEach(function (a) {
      a.addEventListener('click', function () { document.body.classList.remove('sidebar-open'); });
    });
  }
  document.addEventListener('DOMContentLoaded', function () { wrapTables(); initSidebar(); });
  window.addEventListener('resize', wrapTables);
  if (mq.addEventListener) mq.addEventListener('change', wrapTables);
})();
