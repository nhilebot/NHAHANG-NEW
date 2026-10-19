<?php $__env->startSection('title', 'Chi tiết nguyên liệu'); ?>
<?php $__env->startSection('page_title', 'Chi tiết nguyên liệu'); ?>
<?php $__env->startSection('topbar_title', 'Chi tiết nguyên liệu'); ?>

<?php $__env->startSection('admin_content'); ?>
<?php echo $__env->make('admin.inventory._style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="inv-panel">
    <div class="inv-head">
        <div>
            <h2 class="inv-title"><?php echo e($ingredient->name); ?></h2>
            <p class="inv-sub">Mã <?php echo e($ingredient->code); ?></p>
        </div>
        <div class="inv-actions">
            <a href="<?php echo e(route('admin.inventory.ingredients.edit', $ingredient)); ?>" class="btn btn-primary">Sửa</a>
            <a href="<?php echo e(route('admin.inventory.ingredients.index')); ?>" class="btn btn-ghost">Quay lại</a>
        </div>
    </div>

    <dl class="inv-dl">
        <dt>Nhóm</dt><dd><?php echo e($ingredient->category->name ?? '—'); ?></dd>
        <dt>Đơn vị tính</dt><dd><?php echo e($ingredient->unit); ?></dd>
        <dt>Tồn kho hiện tại</dt>
        <dd>
            <strong><?php echo e(\App\Models\Ingredient::fmt($ingredient->current_stock)); ?> <?php echo e($ingredient->unit); ?></strong>
            <?php if(!$ingredient->is_active): ?><span class="inv-badge off">Ngừng sử dụng</span>
            <?php elseif($ingredient->is_out): ?><span class="inv-badge out">Hết hàng</span>
            <?php elseif($ingredient->is_low): ?><span class="inv-badge low">Sắp hết</span>
            <?php else: ?><span class="inv-badge ok">Đủ hàng</span><?php endif; ?>
        </dd>
        <dt>Mức tồn tối thiểu</dt>
        <dd><?php echo e($ingredient->min_stock > 0 ? \App\Models\Ingredient::fmt($ingredient->min_stock) . ' ' . $ingredient->unit : 'Không đặt'); ?></dd>
        <dt>Giá nhập tham khảo</dt>
        <dd><?php echo e($ingredient->reference_price !== null ? number_format($ingredient->reference_price, 0, ',', '.') . 'đ / ' . $ingredient->unit : '—'); ?></dd>
        <dt>Ghi chú</dt><dd><?php echo e($ingredient->note ?: '—'); ?></dd>
    </dl>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/admin/inventory/ingredients/show.blade.php ENDPATH**/ ?>