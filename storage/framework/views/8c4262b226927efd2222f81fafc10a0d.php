<?php $__env->startSection('title', 'Quản lý Đặt bàn - Staff'); ?>

<?php $__env->startSection('admin_content'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
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

    /* Cải thiện hiển thị cột khách hàng */
    .customer-cell {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .customer-info {
        flex: 1;
    }
    .customer-actions {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-left: 10px;
    }
    .btn-action-mini {
        padding: 2px 8px;
        font-size: 11px;
        border-radius: 4px;
        text-decoration: none;
        white-space: nowrap;
    }
</style>

<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3">
            <h5 class="m-0 font-weight-bold text-primary">📋 Danh sách Đặt bàn - Quản lý Phục vụ</h5>
        </div>
        <div class="card-body">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Bàn</th>
                            <th>Khách hàng & Hóa đơn</th>
                            <th>Trạng thái hiện tại</th>
                            <th class="text-center">Thao tác phục vụ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $res): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <span class="badge bg-info text-white fs-6">
                                    <div class="table-name"> <?php echo e($res->table->name); ?> - <?php echo e($res->table->capacity); ?> Ghế </div>
                                </span>
                            </td>

                            <td>
                                <div class="customer-cell">
                                    <div class="customer-info">
                                        <strong><?php echo e($res->full_name); ?></strong>
                                        <div class="small text-muted"><i class="fas fa-phone fa-xs"></i> <?php echo e($res->phone); ?></div>
                                    </div>

                                    <div class="customer-actions">
                                        
                                        <?php if($res->status != 'completed'): ?>
                                            <a href="<?php echo e(route('staff.reservations.edit_items', $res->id)); ?>" 
                                               class="btn btn-sm btn-light border text-primary" 
                                               title="Chỉnh sửa món">
                                                <i class="fas fa-pen-to-square"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="badge <?php echo e(\App\Helpers\StatusHelper::getBadgeClass($res->status)); ?>">
                                    <?php echo e(\App\Helpers\StatusHelper::getTextWithEmoji($res->status)); ?>

                                </span>
                            </td>

                            <td class="text-center">
                                <form action="<?php echo e(route('staff.reservations.updateStatus', $res->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                   <div class="btn-group btn-group-sm shadow-sm">
    
    <?php if($res->status == 'pending_deposit'): ?>
        <?php if($res->payment_status === 'deposit_submitted'): ?>
            <button
                type="submit"
                name="status"
                value="pending"
                class="btn btn-warning text-dark"
                onclick="return confirm('Xác nhận đã nhận cọc <?php echo e(number_format($res->deposit_amount ?? 0, 0, ',', '.')); ?>đ qua <?php echo e($res->payment_method == 'BANK' ? 'chuyển khoản' : 'tại quầy'); ?>?')"
            >
                <i class="fas fa-coins"></i> Xác nhận cọc <?php echo e(number_format($res->deposit_amount ?? 0, 0, ',', '.')); ?>đ
            </button>
        <?php else: ?>
            <span class="text-muted small">Chờ khách xác nhận chuyển khoản</span>
        <?php endif; ?>

    
    <?php elseif($res->status == 'pending'): ?>
        <button type="submit" name="status" value="confirmed" class="btn btn-info text-white">Duyệt đơn</button>
    
    
    <?php elseif($res->status == 'confirmed'): ?>
        <button type="submit" name="status" value="arrived" class="btn btn-primary">Khách vào bàn</button>
    
    
    <?php elseif($res->status == 'arrived'): ?>
        <button type="submit" name="status" value="serving" class="btn btn-warning">Báo bếp nấu</button>
    
    
    <?php elseif($res->status == 'ready'): ?>
        <button type="submit" name="status" value="served" class="btn btn-primary">
            <i class="fas fa-check"></i> Đã bưng món
        </button>

    
<?php elseif($res->status == 'served'): ?>
    <a href="<?php echo e(route('staff.reservations.checkout', $res->id)); ?>" class="btn btn-success">💰 Thanh toán</a>
<?php endif; ?>

    
    <?php if(in_array($res->status, \App\Models\Reservation::PAID_STATUSES, true)): ?>
        <button type="submit" name="status" value="completed" class="btn btn-secondary">🏁 Xong & Dọn bàn</button>
    <?php endif; ?>
</div>
<?php if(in_array($res->status, ['pending_deposit', 'pending', 'confirmed'], true)): ?>
    <div class="mt-2">
        <button type="submit" name="status" value="cancelled" class="btn btn-sm btn-link text-danger"
                onclick="return confirm('Hủy đơn này?')">Hủy đơn</button>
    </div>
<?php endif; ?>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="text-center py-5 text-muted">📭 Hiện không có yêu cầu đặt bàn nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php if(session('auto_print')): ?>
<script>window.addEventListener('load', () => window.print());</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/staff/reservations.blade.php ENDPATH**/ ?>