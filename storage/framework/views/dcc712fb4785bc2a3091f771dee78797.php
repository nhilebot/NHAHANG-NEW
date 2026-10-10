<?php $__env->startSection('page_title', 'Quản lý hóa đơn'); ?>
<?php $__env->startSection('topbar_title', 'Quản lý hóa đơn'); ?>

<?php $__env->startSection('admin_content'); ?>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h2>📄 Danh sách hóa đơn</h2>
    </div>

    <div style="display:flex;gap:20px;margin-bottom:20px;">
        <div class="stat-box">
            <h4>💰 Doanh thu hôm nay</h4>
            <p><?php echo e(number_format($todayRevenue)); ?>đ</p>
        </div>

        <div class="stat-box">
            <h4>🏦 Tổng doanh thu</h4>
            <p><?php echo e(number_format($totalRevenue)); ?>đ</p>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Mã HĐ</th>
                <th>Khách</th>
                <th>Bàn</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày</th>
                <th>Xem</th>
            </tr>
        </thead>

        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $bills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td>#<?php echo e($bill->id); ?></td>
                <td><?php echo e($bill->user->name ?? '---'); ?></td>
                <td><?php echo e($bill->table->name ?? '---'); ?></td>
                <td><?php echo e(number_format($bill->total_price)); ?>đ</td>
               <td>

    <?php
        $statusText = match($bill->status) {

            'pending' => 'Chờ xác nhận',

            'confirmed' => 'Đã xác nhận',

            'arrived' => 'Khách đã đến',

            'serving' => 'Đang phục vụ',

            'served' => 'Đã phục vụ',

            'paid_cash' => 'Đã thanh toán tiền mặt',

            'paid_transfer' => 'Đã chuyển khoản',

            'completed' => 'Hoàn tất',

            'cancelled' => 'Đã hủy',

            'paid' => 'Đã thanh toán',

            default => $bill->status
        };
    ?>

    <?php echo e($statusText); ?>


</td>
                <td><?php echo e($bill->updated_at->format('d/m/Y H:i')); ?></td>

                <td>
                    <a href="<?php echo e(route('admin.bills.show', $bill->id)); ?>"
                       class="btn btn-primary btn-sm">
                        Chi tiết
                    </a>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="7" style="text-align:center;">
                    Chưa có hóa đơn
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top:20px;">
        <?php echo e($bills->links()); ?>

    </div>
</div>

<style>
.card{
    background:#fff;
    padding:24px;
    border-radius:14px;
}

.table{
    width:100%;
    border-collapse:collapse;
}

.table th,
.table td{
    padding:12px;
    border-bottom:1px solid #eee;
}

.stat-box{
    background:#f8f8f8;
    padding:20px;
    border-radius:12px;
    min-width:250px;
}
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/admin/bills/index.blade.php ENDPATH**/ ?>