<?php $__env->startSection('title', 'Nguyên liệu'); ?>
<?php $__env->startSection('page_title', 'Nguyên liệu'); ?>
<?php $__env->startSection('topbar_title', 'Nguyên liệu'); ?>

<?php $__env->startSection('admin_content'); ?>
<?php echo $__env->make('admin.inventory._style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $hasFilter = request()->hasAny(['search', 'category_id', 'level', 'status']); ?>

<div class="inv-panel">
    <div class="inv-head">
        <div>
            <h2 class="inv-title">Nguyên liệu</h2>
            <p class="inv-sub">Tồn kho nguyên liệu. Khác với "số phần có thể bán" của từng món trong thực đơn.</p>
        </div>
        <a href="<?php echo e(route('admin.inventory.ingredients.create')); ?>" class="btn btn-primary">+ Thêm nguyên liệu</a>
    </div>

    <div class="inv-stats">
        <div class="inv-stat"><p>Tổng số nguyên liệu</p><b><?php echo e($totalCount); ?></b></div>
        <div class="inv-stat <?php echo e($lowCount ? 'warn' : ''); ?>"><p>Sắp hết</p><b><?php echo e($lowCount); ?></b></div>
        <div class="inv-stat <?php echo e($outCount ? 'bad' : ''); ?>"><p>Hết hàng</p><b><?php echo e($outCount); ?></b></div>
    </div>

    <form method="GET" class="inv-filter">
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Tìm theo mã hoặc tên">
        <select name="category_id" onchange="this.form.submit()">
            <option value="">Tất cả nhóm</option>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($c->id); ?>" <?php if(request('category_id') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="level" onchange="this.form.submit()">
            <option value="">Mọi mức tồn</option>
            <option value="ok" <?php if(request('level') === 'ok'): echo 'selected'; endif; ?>>Đủ hàng</option>
            <option value="low" <?php if(request('level') === 'low'): echo 'selected'; endif; ?>>Sắp hết</option>
            <option value="out" <?php if(request('level') === 'out'): echo 'selected'; endif; ?>>Hết hàng</option>
        </select>
        <select name="status" onchange="this.form.submit()">
            <option value="">Mọi trạng thái</option>
            <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>Đang sử dụng</option>
            <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>Ngừng sử dụng</option>
        </select>
        <button type="submit" class="btn btn-ghost">Tìm</button>
        <?php if($hasFilter): ?><a href="<?php echo e(route('admin.inventory.ingredients.index')); ?>" class="btn btn-ghost">Xóa lọc</a><?php endif; ?>
    </form>

    <div class="inv-wrap">
        <table class="inv-table">
            <thead>
                <tr>
                    <th>Mã</th><th>Tên</th><th>Nhóm</th>
                    <th class="r">Tồn kho</th><th class="r">Tối thiểu</th>
                    <th>Tình trạng</th><th></th>
                </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $ingredients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="<?php echo e(!$i->is_active ? 'is-off' : ($i->is_out ? 'is-out' : ($i->is_low ? 'is-low' : ''))); ?>">
                    <td class="inv-code"><?php echo e($i->code); ?></td>
                    <td><a href="<?php echo e(route('admin.inventory.ingredients.show', $i)); ?>"><strong><?php echo e($i->name); ?></strong></a></td>
                    <td class="inv-muted"><?php echo e($i->category->name ?? '—'); ?></td>
                    <td class="r"><strong><?php echo e(\App\Models\Ingredient::fmt($i->current_stock)); ?></strong> <?php echo e($i->unit); ?></td>
                    <td class="r inv-muted"><?php echo e($i->min_stock > 0 ? \App\Models\Ingredient::fmt($i->min_stock) . ' ' . $i->unit : '—'); ?></td>
                    <td>
                        <?php if(!$i->is_active): ?><span class="inv-badge off">Ngừng sử dụng</span>
                        <?php elseif($i->is_out): ?><span class="inv-badge out">Hết hàng</span>
                        <?php elseif($i->is_low): ?><span class="inv-badge low">Sắp hết</span>
                        <?php else: ?><span class="inv-badge ok">Đủ hàng</span><?php endif; ?>
                    </td>
                    <td>
                        <div class="inv-actions">
                            <a href="<?php echo e(route('admin.inventory.ingredients.show', $i)); ?>" class="btn btn-sm btn-ghost">Xem</a>
                            <a href="<?php echo e(route('admin.inventory.ingredients.edit', $i)); ?>" class="btn btn-sm btn-ghost">Sửa</a>
                            <form method="POST" action="<?php echo e(route('admin.inventory.ingredients.destroy', $i)); ?>"
                                  onsubmit="return confirm('Xóa nguyên liệu &quot;<?php echo e(e($i->name)); ?>&quot;?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-ghost" style="color:#DC2626">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="inv-empty">
                    <?php echo e($hasFilter ? 'Không có nguyên liệu nào khớp bộ lọc.' : 'Chưa có nguyên liệu nào. Hãy tạo nhóm nguyên liệu trước, rồi bấm "Thêm nguyên liệu".'); ?>

                </td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="inv-pager"><?php echo e($ingredients->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/admin/inventory/ingredients/index.blade.php ENDPATH**/ ?>