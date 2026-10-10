<?php $__env->startSection('admin_content'); ?>
<style>
    /* CSS Badge Tiếng Việt */
    .badge-status {
        padding: 6px 14px; font-weight: 600; font-size: 0.85rem;
        border-radius: 50px; display: inline-flex; align-items: center; gap: 6px;
    }
    .st-pending { background-color: #fff4e5; color: #664d03; }   
    .st-confirmed { background-color: #e7f6f8; color: #087990; } 
    .st-arrived { background-color: #e7f0ff; color: #0d6efd; }   
    .st-serving { background-color: #fff9db; color: #856404; }   
    .st-served { background-color: #e0f7fa; color: #006064; }    
    .st-success { background-color: #e9f7ef; color: #198754; }   
    .st-danger { background-color: #fdf2f2; color: #dc3545; }    
</style>

<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">🗓️ Quản lý Đặt bàn & Phục vụ</h5>
<a href="<?php echo e(route('admin.reservations.create')); ?>" class="btn btn-primary btn-sm">+ Đặt bàn tại chỗ</a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Khách hàng</th>
                            <th>Thông tin bàn</th>
                            <th>Trạng thái hiện tại</th>
                            <th class="text-center">Thao tác nghiệp vụ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $res): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <strong><?php echo e($res->full_name); ?></strong>
                                <div class="small text-muted"><?php echo e($res->phone); ?></div>
                            </td>
                            <td>
                                 <span class="badge bg-info text-white fs-6">
                                    <div class="table-name"> <?php echo e($res->table->name); ?> - <?php echo e($res->table->capacity); ?> Ghế </div>
                                </span>
                                <div class="small mt-1 text-primary fw-bold"><?php echo e($res->reservation_time); ?></div>
                                <a href="<?php echo e(route('staff.reservations.edit_items', $res->id)); ?>" class="btn btn-outline-primary btn-sm mt-2">
                                    <i class="fas fa-file-invoice"></i> Chỉnh sửa đơn
                                </a>
                            </td>
                            <td>
                                <span class="badge"><?php echo e(\App\Helpers\StatusHelper::getTextWithEmoji($res->status)); ?></span>
                            </td>
                            <td class="text-center">
                                <?php
                                    $roleId = auth()->user()->role_id ?? 0;
                                    $prefix = auth()->user()->isAdmin() ? 'admin' : 'staff';
                                ?>

                                <form action="<?php echo e(route($prefix . '.reservations.updateStatus', $res->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <div class="btn-group btn-group-sm shadow-sm">
                                        <?php if($res->status == 'pending_deposit'): ?>
                                            <button type="submit" name="status" value="pending" class="btn btn-warning text-dark"
                                                onclick="return confirm('Xác nhận đã nhận cọc <?php echo e(number_format($res->deposit_amount ?? 0, 0, ',', '.')); ?>đ?')">
                                                Xác nhận cọc <?php echo e(number_format($res->deposit_amount ?? 0, 0, ',', '.')); ?>đ
                                            </button>
                                        <?php elseif($res->status == 'pending'): ?>
                                            <button type="submit" name="status" value="confirmed" class="btn btn-info text-white">Duyệt đơn</button>
                                        <?php elseif($res->status == 'confirmed'): ?>
                                            <button type="submit" name="status" value="arrived" class="btn btn-primary">Khách nhận bàn</button>
                                        <?php elseif($res->status == 'arrived'): ?>
                                            <button type="submit" name="status" value="serving" class="btn btn-warning">Báo bếp nấu</button>
                                        <?php elseif($res->status == 'serving' || $res->status == 'ready'): ?>
                                            <button type="submit" name="status" value="served" class="btn btn-info text-white">Đã lên món</button>
                                        <?php elseif($res->status == 'served'): ?>
                                            <?php $payUrl = route($prefix . '.reservations.pay', $res->id); ?>
                                            <button type="submit" name="payment_method" value="cash" formaction="<?php echo e($payUrl); ?>" class="btn btn-success">💰 Tiền mặt</button>
                                            <button type="submit" name="payment_method" value="transfer" formaction="<?php echo e($payUrl); ?>" class="btn btn-dark">💳 CK</button>
                                        <?php endif; ?>

                                        <?php if(in_array($res->status, \App\Models\Reservation::PAID_STATUSES, true)): ?>
                                            <button type="submit" name="status" value="completed" class="btn btn-secondary">🏁 Dọn bàn & Xong</button>
                                        <?php endif; ?>
                                    </div>

                                    <?php if(in_array($res->status, ['pending_deposit', 'pending', 'confirmed', 'arrived'], true)): ?>
                                        <div class="mt-2">
                                            <button type="submit" name="status" value="cancelled" class="btn btn-sm btn-link text-danger" onclick="return confirm('Hủy đơn này?')">Hủy đơn</button>
                                        </div>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">Hiện không có yêu cầu nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/admin/reservations/index.blade.php ENDPATH**/ ?>