<?php $__env->startSection('title', 'Nhà cung cấp'); ?>
<?php $__env->startSection('page_title', 'Nhà cung cấp'); ?>
<?php $__env->startSection('topbar_title', 'Nhà cung cấp'); ?>

<?php $__env->startSection('admin_content'); ?>
<?php echo $__env->make('admin.inventory._style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="inv-panel">
    <div class="inv-head">
        <div>
            <h2 class="inv-title">Nhà cung cấp</h2>
            <p class="inv-sub">Nơi nhà hàng nhập nguyên liệu.</p>
        </div>
        <a href="<?php echo e(route('admin.inventory.suppliers.create')); ?>" class="btn btn-primary">+ Thêm nhà cung cấp</a>
    </div>

    <form method="GET" class="inv-filter">
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Tìm theo mã, tên, SĐT, email">
        <select name="status" onchange="this.form.submit()">
            <option value="">Tất cả trạng thái</option>
            <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>Đang hợp tác</option>
            <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>Ngừng hợp tác</option>
        </select>
        <button type="submit" class="btn btn-ghost">Tìm</button>
        <?php if(request('search') || request('status')): ?><a href="<?php echo e(route('admin.inventory.suppliers.index')); ?>" class="btn btn-ghost">Xóa lọc</a><?php endif; ?>
    </form>

    <div class="inv-wrap">
        <table class="inv-table">
            <thead>
                <tr><th>Mã</th><th>Tên</th><th>Điện thoại</th><th>Email</th><th>Trạng thái</th><th></th></tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="<?php echo e($s->is_active ? '' : 'is-off'); ?>">
                    <td class="inv-code"><?php echo e($s->code); ?></td>
                    <td><a href="<?php echo e(route('admin.inventory.suppliers.show', $s)); ?>"><strong><?php echo e($s->name); ?></strong></a></td>
                    <td><?php echo e($s->phone ?: '—'); ?></td>
                    <td class="inv-muted"><?php echo e($s->email ?: '—'); ?></td>
                    <td>
                        <?php if($s->is_active): ?><span class="inv-badge ok">Đang hợp tác</span>
                        <?php else: ?><span class="inv-badge off">Ngừng hợp tác</span><?php endif; ?>
                    </td>
                    <td>
                        <div class="inv-actions">
                            <a href="<?php echo e(route('admin.inventory.suppliers.show', $s)); ?>" class="btn btn-sm btn-ghost">Xem</a>
                            <a href="<?php echo e(route('admin.inventory.suppliers.edit', $s)); ?>" class="btn btn-sm btn-ghost">Sửa</a>
                            <form method="POST" action="<?php echo e(route('admin.inventory.suppliers.destroy', $s)); ?>"
                                  onsubmit="return confirm('Xóa nhà cung cấp &quot;<?php echo e(e($s->name)); ?>&quot;?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-ghost" style="color:#DC2626">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="inv-empty">Chưa có nhà cung cấp nào phù hợp.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="inv-pager"><?php echo e($suppliers->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/admin/inventory/suppliers/index.blade.php ENDPATH**/ ?>