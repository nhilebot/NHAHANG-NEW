<?php $title = $ingredient ? 'Sửa nguyên liệu' : 'Thêm nguyên liệu'; ?>
<?php $__env->startSection('title', $title); ?>
<?php $__env->startSection('page_title', $title); ?>
<?php $__env->startSection('topbar_title', $title); ?>

<?php $__env->startSection('admin_content'); ?>
<?php echo $__env->make('admin.inventory._style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="inv-panel inv-form">
    <h2 class="inv-title"><?php echo e($title); ?></h2>

    <?php if($categories->isEmpty()): ?>
        <p class="inv-err" style="margin-top:14px">
            Chưa có nhóm nguyên liệu nào. <a href="<?php echo e(route('admin.inventory.categories.create')); ?>">Tạo nhóm trước</a> rồi quay lại.
        </p>
    <?php endif; ?>

    <form method="POST" action="<?php echo e($ingredient ? route('admin.inventory.ingredients.update', $ingredient) : route('admin.inventory.ingredients.store')); ?>">
        <?php echo csrf_field(); ?>
        <?php if($ingredient): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

        <div class="row2">
            <div>
                <label for="code">Mã nguyên liệu</label>
                <input type="text" id="code" name="code" value="<?php echo e(old('code', $ingredient->code ?? '')); ?>" placeholder="<?php echo e($suggestedCode); ?>" maxlength="30">
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
                <label for="name">Tên nguyên liệu</label>
                <input type="text" id="name" name="name" value="<?php echo e(old('name', $ingredient->name ?? '')); ?>" required maxlength="150">
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
                <label for="category_id">Nhóm nguyên liệu</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Chọn nhóm</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php if(old('category_id', $ingredient->category_id ?? '') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="inv-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label for="unit">Đơn vị tính</label>
                <input type="text" id="unit" name="unit" list="units" value="<?php echo e(old('unit', $ingredient->unit ?? '')); ?>" required maxlength="30">
                <datalist id="units">
                    <?php $__currentLoopData = ['kg','g','lít','ml','chai','lon','hộp','gói','cái','quả']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($u); ?>"><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </datalist>
                <?php $__errorArgs = ['unit'];
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
                <label for="min_stock">Mức tồn tối thiểu</label>
                <input type="number" id="min_stock" name="min_stock" step="0.001" min="0" value="<?php echo e(old('min_stock', $ingredient->min_stock ?? 0)); ?>">
                <div class="inv-hint">Tồn bằng hoặc thấp hơn mức này sẽ báo "Sắp hết". Để 0 nếu không cần cảnh báo.</div>
                <?php $__errorArgs = ['min_stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="inv-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label for="reference_price">Giá nhập tham khảo (đ / đơn vị)</label>
                <input type="number" id="reference_price" name="reference_price" step="1" min="0" value="<?php echo e(old('reference_price', isset($ingredient->reference_price) ? (int) $ingredient->reference_price : '')); ?>">
                <?php $__errorArgs = ['reference_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="inv-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <label>Tồn kho hiện tại</label>
        <div class="inv-readonly">
            <?php if($ingredient): ?>
                <strong><?php echo e(\App\Models\Ingredient::fmt($ingredient->current_stock)); ?> <?php echo e($ingredient->unit); ?></strong>
            <?php else: ?>
                <strong>0</strong>
            <?php endif; ?>
            <div class="inv-hint">Số lượng chỉ thay đổi qua phiếu nhập kho, phiếu xuất kho hoặc kiểm kê để luôn có lịch sử đối chiếu.</div>
        </div>

        <label for="note">Ghi chú</label>
        <input type="text" id="note" name="note" value="<?php echo e(old('note', $ingredient->note ?? '')); ?>" maxlength="255">
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
            <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $ingredient->is_active ?? true)): echo 'checked'; endif; ?>>
            Đang sử dụng
        </label>

        <div class="inv-form-actions">
            <button type="submit" class="btn btn-primary">Lưu nguyên liệu</button>
            <a href="<?php echo e(route('admin.inventory.ingredients.index')); ?>" class="btn btn-ghost">Hủy</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/admin/inventory/ingredients/form.blade.php ENDPATH**/ ?>