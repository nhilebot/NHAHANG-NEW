<?php $__env->startSection('title', 'Chi tiết nhà cung cấp'); ?>
<?php $__env->startSection('page_title', 'Chi tiết nhà cung cấp'); ?>
<?php $__env->startSection('topbar_title', 'Chi tiết nhà cung cấp'); ?>

<?php $__env->startSection('admin_content'); ?>
<?php echo $__env->make('admin.inventory._style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="inv-panel">
    <div class="inv-head">
        <div>
            <h2 class="inv-title"><?php echo e($supplier->name); ?></h2>
            <p class="inv-sub">Mã <?php echo e($supplier->code); ?></p>
        </div>
        <div class="inv-actions">
            <a href="<?php echo e(route('admin.inventory.suppliers.edit', $supplier)); ?>" class="btn btn-primary">Sửa</a>
            <a href="<?php echo e(route('admin.inventory.suppliers.index')); ?>" class="btn btn-ghost">Quay lại</a>
        </div>
    </div>

    <dl class="inv-dl">
        <dt>Trạng thái</dt>
        <dd>
            <?php if($supplier->is_active): ?><span class="inv-badge ok">Đang hợp tác</span>
            <?php else: ?><span class="inv-badge off">Ngừng hợp tác</span><?php endif; ?>
        </dd>
        <dt>Điện thoại</dt><dd><?php echo e($supplier->phone ?: '—'); ?></dd>
        <dt>Email</dt><dd><?php echo e($supplier->email ?: '—'); ?></dd>
        <dt>Địa chỉ</dt><dd><?php echo e($supplier->address ?: '—'); ?></dd>
        <dt>Ghi chú</dt><dd><?php echo nl2br(e($supplier->note ?: '—')); ?></dd>
    </dl>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/admin/inventory/suppliers/show.blade.php ENDPATH**/ ?>