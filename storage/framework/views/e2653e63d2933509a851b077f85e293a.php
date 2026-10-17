
<?php $__env->startSection('title', 'Dashboard Staff'); ?>
<?php $__env->startSection('page_title', 'Dashboard'); ?>
<?php $__env->startSection('topbar_title', 'Dashboard phục vụ'); ?>

<?php $__env->startSection('admin_content'); ?>
<?php
    $tableLabel = ['empty' => 'Trống', 'reserved' => 'Đã đặt', 'occupied' => 'Có khách', 'cleaning' => 'Đang dọn'];
    $statusText = [
        'paid_cash' => 'Tiền mặt', 'paid_transfer' => 'Chuyển khoản', 'completed' => 'Hoàn tất',
    ];
?>

<div class="sd-wrap">

    <?php if(session('success')): ?><div class="sd-alert ok"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="sd-alert err"><?php echo e(session('error')); ?></div><?php endif; ?>

    
    <div class="sd-hello">
        <div>
            <h2>Xin chào, <?php echo e(auth()->user()->name); ?> 👋</h2>
            <p><?php echo e(now()->format('d/m/Y')); ?> ·
                <?php if($totalTasks > 0): ?> Có <b><?php echo e($totalTasks); ?></b> việc đang chờ bạn xử lý
                <?php else: ?> Hiện không có việc nào đang chờ ✅ <?php endif; ?>
            </p>
        </div>
        <div class="sd-hello-btns">
            <a href="<?php echo e(route('staff.reservations.create')); ?>" class="sd-btn gold">+ Mở bàn / Tạo đặt bàn</a>
            <a href="<?php echo e(route('staff.reservations.index')); ?>" class="sd-btn light">Danh sách phục vụ</a>
        </div>
    </div>

    
    <div class="sd-stats">
        <div class="sd-stat"><span class="dot" style="background:#10b981"></span><p>Bàn trống</p><h3><?php echo e($tableStats['empty']); ?></h3></div>
        <div class="sd-stat"><span class="dot" style="background:#ef4444"></span><p>Bàn có khách</p><h3><?php echo e($tableStats['occupied']); ?></h3></div>
        <div class="sd-stat"><span class="dot" style="background:#f59e0b"></span><p>Bàn đã đặt</p><h3><?php echo e($tableStats['reserved']); ?></h3></div>
        <div class="sd-stat"><span class="dot" style="background:#8b5cf6"></span><p>Bếp đang nấu</p><h3><?php echo e($cooking); ?></h3></div>
        <div class="sd-stat money"><p>Thu tại quầy hôm nay</p><h3><?php echo e(number_format($revenueToday, 0, ',', '.')); ?>đ</h3><small><?php echo e($billsToday); ?> hóa đơn</small></div>
    </div>

    <div class="sd-main">

        
        <div class="sd-side">

            
            <div class="sd-card">
                <h3>⚡ Việc cần làm</h3>

                <?php $hasTask = false; ?>
                <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($g['items']->isEmpty()) continue; ?>
                    <?php $hasTask = true; ?>

                    <div class="sd-group">
                        <div class="sd-group-title" style="border-color:<?php echo e($g['color']); ?>">
                            <span><?php echo e($g['icon']); ?> <?php echo e($g['title']); ?></span>
                            <b style="background:<?php echo e($g['color']); ?>"><?php echo e($g['items']->count()); ?></b>
                        </div>

                        <?php $__currentLoopData = $g['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="sd-task">
                                <div class="sd-task-info">
                                    <strong><?php echo e($r->table->name ?? '---'); ?></strong>
                                    <span><?php echo e($r->full_name); ?>

                                        <?php if(in_array($r->status, ['pending', 'confirmed'])): ?>
                                            · <?php echo e(optional($r->reservation_date)->format('d/m')); ?> <?php echo e(substr($r->reservation_time, 0, 5)); ?>

                                        <?php endif; ?>
                                        <?php if($r->status === 'pending_deposit'): ?>
                                            · cọc <?php echo e(number_format($r->deposit_amount ?? 0, 0, ',', '.')); ?>đ
                                        <?php endif; ?>
                                        <?php if($r->status === 'served'): ?>
                                            · cần thu <?php echo e(number_format(max(0, (int)$r->total_price - (int)($r->deposit_amount ?? 0)), 0, ',', '.')); ?>đ
                                        <?php endif; ?>
                                    </span>
                                </div>

                                <?php if($g['action']['type'] === 'link'): ?>
                                    <a href="<?php echo e(route($g['action']['route'], $r->id)); ?>" class="sd-act" style="background:<?php echo e($g['color']); ?>">
                                        <?php echo e($g['action']['label']); ?>

                                    </a>
                                <?php else: ?>
                                    <form method="POST" action="<?php echo e(route('staff.reservations.updateStatus', $r->id)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" name="status" value="<?php echo e($g['action']['value']); ?>"
                                                class="sd-act" style="background:<?php echo e($g['color']); ?>"
                                                <?php if(!empty($g['action']['confirm'])): ?> onclick="return confirm('<?php echo e($g['action']['confirm']); ?>')" <?php endif; ?>>
                                            <?php echo e($g['action']['label']); ?>

                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php if (! ($hasTask)): ?>
                    <div class="sd-empty">🎉 Không có việc nào đang chờ.</div>
                <?php endif; ?>
            </div>

            
            <div class="sd-card">
                <h3>🧾 Hóa đơn gần đây</h3>
                <?php $__empty_1 = true; $__currentLoopData = $recentBills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('staff.bills.show', $b->id)); ?>" class="sd-bill">
                        <div>
                            <strong>#<?php echo e(str_pad($b->id, 5, '0', STR_PAD_LEFT)); ?> · <?php echo e($b->table->name ?? '---'); ?></strong>
                            <small><?php echo e($statusText[$b->status] ?? $b->status); ?> · <?php echo e(optional($b->paid_at)->format('H:i d/m')); ?></small>
                        </div>
                        <b><?php echo e(number_format($b->paid_amount ?? 0, 0, ',', '.')); ?>đ</b>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="sd-muted">Chưa có hóa đơn nào.</p>
                <?php endif; ?>
                <a href="<?php echo e(route('staff.bills.index')); ?>" class="sd-more">Xem tất cả hóa đơn →</a>
            </div>
        </div>

        
        <div class="sd-side">

            
            <div class="sd-card">
                <h3>🪑 Sơ đồ bàn</h3>
                <div class="sd-legend">
                    <?php $__currentLoopData = $tableLabel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span><i class="tb-<?php echo e($k); ?>"></i><?php echo e($v); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="sd-tables">
                    <?php $__empty_1 = true; $__currentLoopData = $tables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="sd-table tb-<?php echo e($t->status); ?>" title="<?php echo e($tableLabel[$t->status] ?? $t->status); ?>">
                            <b><?php echo e($t->name); ?></b>
                            <small><?php echo e($t->capacity); ?> ghế</small>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="sd-muted">Chưa có bàn nào.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.sd-wrap{max-width:1250px;margin:auto}
.sd-alert{padding:12px 16px;border-radius:12px;margin-bottom:16px;font-weight:600}
.sd-alert.ok{background:#ecfdf5;color:#065f46}.sd-alert.err{background:#fef2f2;color:#991b1b}

.sd-hello{display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;
    background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff;border-radius:22px;padding:28px 32px;margin-bottom:22px}
.sd-hello h2{margin:0;font-size:26px}
.sd-hello p{margin:6px 0 0;opacity:.8}
.sd-hello b{color:#d4af37}
.sd-hello-btns{display:flex;gap:10px;flex-wrap:wrap}
.sd-btn{padding:11px 18px;border-radius:12px;font-weight:700;text-decoration:none;font-size:14px}
.sd-btn.gold{background:#d4af37;color:#fff}.sd-btn.gold:hover{background:#b89020}
.sd-btn.light{background:rgba(255,255,255,.12);color:#fff}.sd-btn.light:hover{background:rgba(255,255,255,.22)}

.sd-stats{display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:22px}
.sd-stat{background:#fff;border-radius:18px;padding:18px 20px;box-shadow:0 10px 30px rgba(0,0,0,.05);position:relative}
.sd-stat p{margin:0;color:#64748b;font-size:13px}
.sd-stat h3{margin:6px 0 0;font-size:30px;color:#1e293b}
.sd-stat small{color:#94a3b8}
.sd-stat .dot{position:absolute;top:18px;right:18px;width:10px;height:10px;border-radius:50%}
.sd-stat.money{background:#fffbeb;border:1px solid #fde68a}
.sd-stat.money h3{font-size:22px;color:#b45309}

.sd-main{display:grid;grid-template-columns:1.2fr 1fr;gap:22px;align-items:start}
.sd-side{display:flex;flex-direction:column;gap:22px}
.sd-card{background:#fff;border-radius:20px;padding:24px;box-shadow:0 10px 30px rgba(0,0,0,.05)}
.sd-card h3{margin:0 0 16px;font-size:18px;color:#1e293b}

.sd-group{margin-bottom:18px}
.sd-group-title{display:flex;justify-content:space-between;align-items:center;border-left:4px solid;padding:4px 10px;
    margin-bottom:8px;font-weight:700;color:#334155;font-size:14px}
.sd-group-title b{color:#fff;border-radius:999px;padding:1px 10px;font-size:12px}
.sd-task{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;background:#f8fafc;margin-bottom:6px}
.sd-task-info strong{display:block;color:#1e293b}
.sd-task-info span{color:#64748b;font-size:13px}
.sd-act{border:none;color:#fff;font-weight:700;font-size:13px;padding:8px 14px;border-radius:10px;cursor:pointer;text-decoration:none;white-space:nowrap}
.sd-act:hover{filter:brightness(.92);color:#fff}
.sd-empty{text-align:center;padding:40px 10px;color:#94a3b8}

.sd-legend{display:flex;gap:14px;flex-wrap:wrap;font-size:12px;color:#64748b;margin-bottom:12px}
.sd-legend i{display:inline-block;width:10px;height:10px;border-radius:3px;margin-right:5px}
.sd-tables{display:grid;grid-template-columns:repeat(auto-fill,minmax(88px,1fr));gap:10px}
.sd-table{border-radius:14px;padding:12px 6px;text-align:center;border:2px solid transparent}
.sd-table b{display:block;font-size:14px}.sd-table small{font-size:11px;opacity:.8}
.tb-empty{background:#ecfdf5;color:#065f46;border-color:#a7f3d0}
.tb-occupied{background:#fef2f2;color:#991b1b;border-color:#fecaca}
.tb-reserved{background:#fffbeb;color:#92400e;border-color:#fde68a}
.tb-cleaning{background:#f1f5f9;color:#475569;border-color:#cbd5e1}
.sd-legend .tb-empty{background:#10b981}.sd-legend .tb-occupied{background:#ef4444}
.sd-legend .tb-reserved{background:#f59e0b}.sd-legend .tb-cleaning{background:#94a3b8}

.sd-bill{display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid #f1f5f9;text-decoration:none;color:#1e293b}
.sd-bill small{display:block;color:#94a3b8}
.sd-bill:hover{background:#f8fafc}
.sd-more{display:block;text-align:center;margin-top:12px;color:#b45309;font-weight:700;text-decoration:none;font-size:14px}
.sd-muted{color:#94a3b8;text-align:center;margin:10px 0}

@media(max-width:1100px){.sd-stats{grid-template-columns:repeat(2,1fr)}.sd-main{grid-template-columns:1fr}}
</style>

<script>
    setTimeout(() => location.reload(), 30000);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/staff/dashboard.blade.php ENDPATH**/ ?>