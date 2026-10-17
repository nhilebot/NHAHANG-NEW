
<?php $__env->startSection('title', 'Thanh toán'); ?>
<?php $__env->startSection('page_title', 'Thanh toán'); ?>
<?php $__env->startSection('topbar_title', 'Thanh toán'); ?>

<?php $__env->startSection('admin_content'); ?>
<?php
    $deposit   = min((int) ($res->deposit_amount ?? 0), (int) $res->total_price);
    $bankId    = config('services.bank.id');
    $bankAcc   = config('services.bank.account');
    $bankName  = config('services.bank.name');
    $transfer  = 'HD' . str_pad($res->id, 5, '0', STR_PAD_LEFT);
    $qrReady   = !empty($bankAcc) && !empty($bankId);
?>

<div class="co-wrap">

    
    <div class="co-head">
        <div>
            <h2>💳 Thanh toán</h2>
            <p><?php echo e($res->table->name ?? '---'); ?> · <?php echo e($res->full_name); ?> · #<?php echo e(str_pad($res->id, 5, '0', STR_PAD_LEFT)); ?></p>
        </div>
        <a href="<?php echo e(route('staff.reservations.index')); ?>" class="co-back">← Quay lại</a>
    </div>

    <div class="co-grid">

        
        <div class="co-card">
            <h3>🧾 Tóm tắt hóa đơn</h3>

            <?php $items = is_array($res->cart_data) ? $res->cart_data : []; ?>
            <?php if(count($items)): ?>
                <div class="co-items">
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="co-item">
                            <span><?php echo e($it['name'] ?? 'Món'); ?> <small>× <?php echo e($it['quantity']); ?></small></span>
                            <b><?php echo e(number_format($it['price'] * $it['quantity'], 0, ',', '.')); ?>đ</b>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <div class="co-row"><span>Tổng hóa đơn</span><b><?php echo e(number_format($res->total_price, 0, ',', '.')); ?>đ</b></div>
            <div class="co-row green"><span>Đã cọc</span><b>− <?php echo e(number_format($deposit, 0, ',', '.')); ?>đ</b></div>

            <div class="co-due">
                <p>Khách cần thanh toán</p>
                <h1><?php echo e(number_format($due, 0, ',', '.')); ?>đ</h1>
            </div>
        </div>

        
        <div class="co-card">
            <h3>Chọn phương thức thanh toán</h3>

            <form method="POST" action="<?php echo e(route('staff.reservations.pay', $res->id)); ?>" id="payForm">
                <?php echo csrf_field(); ?>

                <div class="co-methods">
                    <label class="co-method" data-m="cash">
                        <input type="radio" name="payment_method" value="cash" required>
                        <span class="ico">💰</span>
                        <span><b>Tiền mặt</b><small>Thu trực tiếp tại quầy</small></span>
                    </label>

                    <label class="co-method" data-m="transfer">
                        <input type="radio" name="payment_method" value="transfer">
                        <span class="ico">📱</span>
                        <span><b>Chuyển khoản</b><small>Khách quét mã QR</small></span>
                    </label>
                </div>

                
                <div class="co-panel" id="panel-cash">
                    <p>Nhận đủ số tiền <b><?php echo e(number_format($due, 0, ',', '.')); ?>đ</b> từ khách rồi bấm xác nhận.</p>
                </div>

                
                <div class="co-panel" id="panel-transfer">
                    <?php if($qrReady): ?>
                        <div class="co-qr">
                            <img src="<?php echo e($qrUrl); ?>" alt="QR chuyển khoản"
                                 onerror="this.style.display='none';document.getElementById('qrErr').style.display='block'">
                            <div id="qrErr" class="co-qr-err">Không tải được ảnh QR. Vui lòng cho khách chuyển khoản theo thông tin bên dưới.</div>
                        </div>
                        <div class="co-bank">
                            <div><span>Ngân hàng</span><b><?php echo e($bankId); ?></b></div>
                            <div><span>Số tài khoản</span><b><?php echo e($bankAcc); ?></b></div>
                            <div><span>Chủ tài khoản</span><b><?php echo e($bankName); ?></b></div>
                            <div><span>Số tiền</span><b><?php echo e(number_format($due, 0, ',', '.')); ?>đ</b></div>
                            <div><span>Nội dung</span><b><?php echo e($transfer); ?></b></div>
                        </div>
                        <p class="co-note">Chỉ bấm xác nhận khi đã thấy tiền về tài khoản.</p>
                    <?php else: ?>
                        <div class="co-qr-err" style="display:block">
                            ⚠️ Chưa cấu hình tài khoản ngân hàng. Thêm <code>BANK_ID</code>, <code>BANK_ACCOUNT</code>,
                            <code>BANK_ACCOUNT_NAME</code> vào file .env rồi chạy <code>php artisan config:clear</code>.
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" id="confirmBtn" class="co-confirm" disabled>Chọn phương thức thanh toán</button>
            </form>
        </div>
    </div>
</div>

<style>
.co-wrap{max-width:1000px;margin:auto}
.co-head{display:flex;justify-content:space-between;align-items:center;background:#fff;border-radius:20px;padding:24px 30px;margin-bottom:24px;box-shadow:0 10px 30px rgba(0,0,0,.05)}
.co-head h2{margin:0;font-size:26px;color:#1e293b}
.co-head p{margin:6px 0 0;color:#64748b}
.co-back{background:#e2e8f0;color:#1e293b;padding:10px 18px;border-radius:12px;font-weight:700;text-decoration:none}
.co-back:hover{background:#cbd5e1}

.co-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}
.co-card{background:#fff;border-radius:20px;padding:26px;box-shadow:0 10px 30px rgba(0,0,0,.05)}
.co-card h3{margin:0 0 18px;color:#1e293b;font-size:18px}

.co-items{max-height:200px;overflow:auto;margin-bottom:12px;border-bottom:1px solid #f1f5f9}
.co-item{display:flex;justify-content:space-between;padding:8px 0;font-size:14px;color:#334155}
.co-item small{color:#94a3b8}
.co-row{display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid #f1f5f9;color:#475569}
.co-row.green b{color:#10b981}
.co-due{margin-top:18px;background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff;border-radius:16px;padding:20px;text-align:center}
.co-due p{margin:0;opacity:.8;font-size:14px}
.co-due h1{margin:6px 0 0;font-size:38px;color:#d4af37}

.co-methods{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px}
.co-method{display:flex;align-items:center;gap:12px;border:2px solid #e2e8f0;border-radius:14px;padding:14px;cursor:pointer;transition:.2s}
.co-method:hover{border-color:#d4af37}
.co-method input{display:none}
.co-method .ico{font-size:28px}
.co-method b{display:block;color:#1e293b}
.co-method small{color:#94a3b8}
.co-method.active{border-color:#d4af37;background:#fffbeb;box-shadow:0 0 0 3px rgba(212,175,55,.15)}

.co-panel{display:none;margin-bottom:18px}
.co-panel.show{display:block}
#panel-cash p{background:#f0fdf4;color:#166534;padding:14px;border-radius:12px;margin:0}
.co-qr{text-align:center}
.co-qr img{width:100%;max-width:280px;border-radius:14px;border:1px solid #e2e8f0}
.co-qr-err{display:none;background:#fef2f2;color:#991b1b;padding:12px;border-radius:12px;font-size:14px}
.co-bank{margin-top:14px;background:#f8fafc;border-radius:12px;padding:6px 16px}
.co-bank div{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid #e2e8f0;font-size:14px}
.co-bank div:last-child{border-bottom:none}
.co-bank span{color:#64748b}
.co-note{margin:12px 0 0;color:#b45309;font-size:13px;text-align:center}

.co-confirm{width:100%;border:none;padding:16px;border-radius:14px;font-size:16px;font-weight:700;color:#fff;background:#10b981;cursor:pointer;transition:.2s}
.co-confirm:hover:not(:disabled){background:#059669}
.co-confirm:disabled{background:#cbd5e1;cursor:not-allowed}

@media(max-width:768px){.co-grid,.co-methods{grid-template-columns:1fr}}
</style>

<script>
const money = '<?php echo e(number_format($due, 0, ',', '.')); ?>đ';
const btn   = document.getElementById('confirmBtn');

document.querySelectorAll('.co-method').forEach(label => {
    label.querySelector('input').addEventListener('change', () => {
        const m = label.dataset.m;

        document.querySelectorAll('.co-method').forEach(l => l.classList.toggle('active', l === label));
        document.getElementById('panel-cash').classList.toggle('show', m === 'cash');
        document.getElementById('panel-transfer').classList.toggle('show', m === 'transfer');

        btn.disabled = false;
        btn.textContent = m === 'transfer'
            ? '✅ Đã nhận chuyển khoản ' + money
            : '✅ Đã thu tiền mặt ' + money;
    });
});

document.getElementById('payForm').addEventListener('submit', e => {
    if (!confirm('Xác nhận đã nhận đủ ' + money + '? Thao tác này không hoàn tác.')) e.preventDefault();
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/staff/reservations/checkout.blade.php ENDPATH**/ ?>