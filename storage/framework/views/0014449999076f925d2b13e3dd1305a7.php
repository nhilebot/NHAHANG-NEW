<?php $title = $category ? 'Sửa nhóm nguyên liệu' : 'Thêm nhóm nguyên liệu'; ?>
<?php $__env->startSection('title', $title); ?>
<?php $__env->startSection('page_title', $title); ?>
<?php $__env->startSection('topbar_title', $title); ?>

<?php $__env->startSection('admin_content'); ?>
<?php echo $__env->make('admin.inventory._style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="inv-panel inv-form">
    <h2 class="inv-title"><?php echo e($title); ?></h2>

    <form method="POST" action="<?php echo e($category ? route('admin.inventory.categories.update', $category) : route('admin.inventory.categories.store')); ?>">
        <?php echo csrf_field(); ?>
        <?php if($category): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

        <label for="name">Tên nhóm</label>
        <input type="text" id="name" name="name" value="<?php echo e(old('name', $category->name ?? '')); ?>" required maxlength="100">
        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="inv-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <label for="description">Mô tả (không bắt buộc)</label>
        <input type="text" id="description" name="description" value="<?php echo e(old('description', $category->description ?? '')); ?>" maxlength="255">
        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="inv-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <div class="inv-form-actions">
            <button type="submit" class="btn btn-primary">Lưu nhóm</button>
            <a href="<?php echo e(route('admin.inventory.categories.index')); ?>" class="btn btn-ghost">Hủy</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/admin/inventory/categories/form.blade.php ENDPATH**/ ?>