<?php $__env->startSection('title', 'Nhóm nguyên liệu'); ?>
<?php $__env->startSection('page_title', 'Nhóm nguyên liệu'); ?>
<?php $__env->startSection('topbar_title', 'Nhóm nguyên liệu'); ?>

<?php $__env->startSection('admin_content'); ?>
<?php echo $__env->make('admin.inventory._style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="inv-panel">
    <div class="inv-head">
        <div>
            <h2 class="inv-title">Nhóm nguyên liệu</h2>
            <p class="inv-sub">Thịt, cá, hải sản, rau củ, gia vị, đồ uống...</p>
        </div>
        <a href="<?php echo e(route('admin.inventory.categories.create')); ?>" class="btn btn-primary">+ Thêm nhóm</a>
    </div>

    <form method="GET" class="inv-filter">
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Tìm theo tên nhóm">
        <button type="submit" class="btn btn-ghost">Tìm</button>
        <?php if(request('search')): ?><a href="<?php echo e(route('admin.inventory.categories.index')); ?>" class="btn btn-ghost">Xóa lọc</a><?php endif; ?>
    </form>

    <div class="inv-wrap">
        <table class="inv-table">
            <thead>
                <tr><th>Tên nhóm</th><th>Mô tả</th><th class="r">Số nguyên liệu</th><th></th></tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><strong><?php echo e($c->name); ?></strong></td>
                    <td class="inv-muted"><?php echo e($c->description ?: '—'); ?></td>
                    <td class="r">
                        <a href="<?php echo e(route('admin.inventory.ingredients.index', ['category_id' => $c->id])); ?>"><?php echo e($c->ingredients_count); ?></a>
                    </td>
                    <td>
                        <div class="inv-actions">
                            <a href="<?php echo e(route('admin.inventory.categories.edit', $c)); ?>" class="btn btn-sm btn-ghost">Sửa</a>
                            <form method="POST" action="<?php echo e(route('admin.inventory.categories.destroy', $c)); ?>"
                                  onsubmit="return confirm('Xóa nhóm &quot;<?php echo e(e($c->name)); ?>&quot;?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-ghost" style="color:#DC2626">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="inv-empty">Chưa có nhóm nguyên liệu nào. Bấm "Thêm nhóm" để tạo nhóm đầu tiên.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="inv-pager"><?php echo e($categories->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/admin/inventory/categories/index.blade.php ENDPATH**/ ?>