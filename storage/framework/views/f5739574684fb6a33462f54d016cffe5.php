<?php $title = $supplier ? 'Sửa nhà cung cấp' : 'Thêm nhà cung cấp'; ?>
<?php $__env->startSection('title', $title); ?>
<?php $__env->startSection('page_title', $title); ?>
<?php $__env->startSection('topbar_title', $title); ?>

<?php $__env->startSection('admin_content'); ?>
<?php echo $__env->make('admin.inventory._style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="inv-panel inv-form">
    <h2 class="inv-title"><?php echo e($title); ?></h2>

    <form method="POST" action="<?php echo e($supplier ? route('admin.inventory.suppliers.update', $supplier) : route('admin.inventory.suppliers.store')); ?>">
        <?php echo csrf_field(); ?>
        <?php if($supplier): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

        <div class="row2">
            <div>
                <label for="code">Mã nhà cung cấp</label>
                <input type="text" id="code" name="code" value="<?php echo e(old('code', $supplier->code ?? '')); ?>" placeholder="<?php echo e($suggestedCode); ?>" maxlength="30">
                <div class="inv-hint">Để trống để hệ thống tự đặt (<?php echo e($suggestedCode); ?>).</div>
                <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="inv-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label for="name">Tên nhà cung cấp</label>
                <input type="text" id="name" name="name" value="<?php echo e(old('name', $supplier->name ?? '')); ?>" required maxlength="150">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="inv-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div class="row2">
            <div>
                <label for="phone">Số điện thoại</label>
                <input type="text" id="phone" name="phone" value="<?php echo e(old('phone', $supplier->phone ?? '')); ?>" maxlength="20">
                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="inv-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo e(old('email', $supplier->email ?? '')); ?>" maxlength="150">
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="inv-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <label for="address">Địa chỉ</label>
        <input type="text" id="address" name="address" value="<?php echo e(old('address', $supplier->address ?? '')); ?>" maxlength="255">
        <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="inv-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <label for="note">Ghi chú</label>
        <textarea id="note" name="note" rows="3" maxlength="1000"><?php echo e(old('note', $supplier->note ?? '')); ?></textarea>
        <?php $__errorArgs = ['note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="inv-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <label class="inv-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $supplier->is_active ?? true)): echo 'checked'; endif; ?>>
            Đang hợp tác
        </label>

        <div class="inv-form-actions">
            <button type="submit" class="btn btn-primary">Lưu nhà cung cấp</button>
            <a href="<?php echo e(route('admin.inventory.suppliers.index')); ?>" class="btn btn-ghost">Hủy</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/admin/inventory/suppliers/form.blade.php ENDPATH**/ ?>