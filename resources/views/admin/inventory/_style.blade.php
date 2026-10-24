<style>
.inv-panel{background:#fff;border-radius:12px;padding:28px;box-shadow:0 10px 30px rgba(0,0,0,.05);border-top:4px solid #D4AF37;margin-bottom:20px}
.inv-head{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:18px}
.inv-title{font-family:'Playfair Display',serif;font-weight:700;font-size:24px;color:#1A2228;margin:0}
.inv-sub{color:#718096;font-size:13px;margin:4px 0 0}
.inv-filter{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:16px}
.inv-filter input,.inv-filter select{padding:9px 12px;border:1px solid #E2E8F0;border-radius:8px;font-family:inherit;font-size:14px;background:#fff}
.inv-filter input[type=text]{min-width:220px}
.inv-wrap{overflow-x:auto}
.inv-table{width:100%;border-collapse:collapse;font-size:14px}
.inv-table th{text-align:left;background:#FCFBF8;color:#4A5568;font-size:12px;font-weight:700;padding:12px 10px;border-bottom:2px solid #EAEAEA;white-space:nowrap}
.inv-table td{padding:12px 10px;border-bottom:1px solid #F1F5F9;vertical-align:middle}
.inv-table .r{text-align:right}
.inv-table tr.is-low td{background:#FFFBEB}
.inv-table tr.is-out td{background:#FEF2F2}
.inv-table tr.is-off td{opacity:.55}
.inv-code{font-family:monospace;font-size:13px;color:#4A5568}
.inv-muted{color:#718096;font-size:13px}
.inv-badge{display:inline-block;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:600;white-space:nowrap}
.inv-badge.low{background:#FEF3C7;color:#92400E}
.inv-badge.out{background:#FEE2E2;color:#991B1B}
.inv-badge.ok{background:#ECFDF5;color:#065F46}
.inv-badge.off{background:#E2E8F0;color:#475569}
.inv-actions{display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap}
.inv-actions form{margin:0}
.inv-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px}
.inv-stat{border:1px solid #E2E8F0;border-radius:10px;padding:12px 16px;background:#fff}
.inv-stat p{margin:0;font-size:12px;color:#718096}
.inv-stat b{font-size:22px;color:#1A2228}
.inv-stat.warn b{color:#B45309}.inv-stat.bad b{color:#DC2626}
.inv-form{max-width:640px}
.inv-form label{display:block;font-size:13px;font-weight:600;margin:16px 0 6px;color:#1A2228}
.inv-form input[type=text],.inv-form input[type=number],.inv-form input[type=email],.inv-form select,.inv-form textarea{width:100%;padding:10px 12px;border:1px solid #E2E8F0;border-radius:8px;font-family:inherit;font-size:14px;box-sizing:border-box;background:#fff}
.inv-form input:focus,.inv-form select:focus,.inv-form textarea:focus{outline:none;border-color:#D4AF37;box-shadow:0 0 0 3px rgba(212,175,55,.15)}
.inv-form .row2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.inv-hint{font-size:12px;color:#718096;margin-top:6px}
.inv-err{color:#DC2626;font-size:13px;margin-top:5px}
.inv-check{display:flex;align-items:center;gap:8px;margin-top:18px;font-size:14px}
.inv-check input{width:auto}
.inv-form-actions{display:flex;gap:10px;margin-top:24px}
.inv-readonly{background:#F8FAFC;border:1px dashed #CBD5E1;border-radius:8px;padding:10px 12px;font-size:14px}
.inv-dl{display:grid;grid-template-columns:180px 1fr;gap:10px 16px;font-size:14px}
.inv-dl dt{color:#718096}.inv-dl dd{margin:0;color:#1A2228}
.inv-pager{margin-top:16px;display:flex;justify-content:center}
.inv-empty{text-align:center;padding:32px 10px;color:#718096}
@media(max-width:768px){.inv-stats{grid-template-columns:1fr}.inv-form .row2{grid-template-columns:1fr}.inv-dl{grid-template-columns:1fr}.inv-filter input[type=text]{min-width:0;width:100%}}
/* ===== Phân trang ===== */
.inv-pager{margin-top:20px;display:flex;justify-content:center}
.inv-pager nav{display:flex;flex-direction:column;align-items:center;gap:10px}
.inv-pager nav>div:first-child{display:none} /* ẩn bản mobile nếu có */
.inv-pager ul,
.inv-pager .pagination{
  list-style:none;margin:0;padding:0;
  display:flex;align-items:center;justify-content:center;
  gap:6px;flex-wrap:wrap
}
.inv-pager li{list-style:none;margin:0;padding:0}
.inv-pager li::marker{content:''}

.inv-pager li a,
.inv-pager li span{
  display:inline-flex;align-items:center;justify-content:center;
  min-width:38px;height:38px;padding:0 12px;box-sizing:border-box;
  border:1px solid #E2E8F0;border-radius:8px;background:#fff;
  color:#4A5568;font-size:14px;font-weight:600;text-decoration:none;
  transition:all .15s ease
}
.inv-pager li a:hover{
  border-color:#D4AF37;color:#1A2228;background:#FCFBF8
}
.inv-pager li.active span,
.inv-pager li.active a,
.inv-pager li[aria-current="page"] span{
  background:#D4AF37;border-color:#D4AF37;color:#1A2228;
  box-shadow:0 4px 10px rgba(212,175,55,.3)
}
.inv-pager li.disabled span,
.inv-pager li[aria-disabled="true"] span{
  background:#F8FAFC;color:#CBD5E1;cursor:not-allowed
}
.inv-pager li:first-child a,
.inv-pager li:last-child a,
.inv-pager li:first-child span,
.inv-pager li:last-child span{font-size:16px}

@media(max-width:480px){
  .inv-pager li a,.inv-pager li span{min-width:34px;height:34px;padding:0 8px;font-size:13px}
}
</style>
