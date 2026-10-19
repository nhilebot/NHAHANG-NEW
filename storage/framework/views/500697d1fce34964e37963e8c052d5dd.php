

<?php $__env->startSection('title', 'FoodHub - Thông tin tài khoản'); ?>

<?php $__env->startSection('head'); ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --navy: #041526;
        --navy-soft: #0d2a47;
        --brass: #c8963e;
        --text: #1f2937;
        --muted: #6b7280;
        --line: #d9dee5;
        --danger: #b42318;
        --danger-bg: #fef3f2;
        --success: #067647;
        --success-bg: #ecfdf3;
    }

    * { box-sizing: border-box; }

    body {
        margin: 0;
        font-family: 'Be Vietnam Pro', system-ui, -apple-system, 'Segoe UI', sans-serif;
        color: var(--text);
        background: #eef1f5;
    }

    .page {
        padding: 96px 16px 48px; /* chừa chỗ cho thanh menu cố định của layout */
    }

    .profile-card {
        width: 100%;
        max-width: 920px;
        margin: 0 auto;
        padding: 40px;
        background: #fff;
        border-top: 5px solid var(--navy);
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(4, 21, 38, .08);
    }

    .profile-head { margin-bottom: 28px; }
    .profile-head h2 {
        margin: 0 0 6px;
        font-size: 28px;
        font-weight: 700;
        color: var(--navy);
    }
    .profile-head p { margin: 0; font-size: 15px; color: var(--muted); }
    .profile-head::after {
        content: '';
        display: block;
        width: 48px;
        height: 3px;
        margin-top: 16px;
        background: var(--brass);
        border-radius: 2px;
    }

    .alert-box {
        margin-bottom: 22px;
        padding: 12px 14px;
        border-radius: 10px;
        font-size: 14px;
        line-height: 1.5;
    }
    .alert-box.error { color: var(--danger); background: var(--danger-bg); border: 1px solid #fecdca; }
    .alert-box.ok { color: var(--success); background: var(--success-bg); border: 1px solid #abefc6; }
    .alert-box ul { margin: 0; padding-left: 18px; }

    .profile-layout {
        display: flex;
        flex-wrap: wrap;
        gap: 44px;
    }

    /* ---------- Cột trái: ảnh đại diện ---------- */
    .profile-left {
        flex: 0 0 180px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .avatar-box {
        position: relative;
        width: 160px;
        height: 160px;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 4px 18px rgba(4, 21, 38, .15);
        background: #e5e9ef;
    }

    .avatar-btn {
        position: absolute;
        right: 6px;
        bottom: 6px;
        display: grid;
        place-items: center;
        width: 40px;
        height: 40px;
        padding: 0;
        color: #fff;
        background: var(--navy);
        border: 3px solid #fff;
        border-radius: 50%;
        cursor: pointer;
        transition: background .15s;
    }
    .avatar-btn:hover { background: var(--navy-soft); }
    .avatar-btn:focus-visible { outline: 3px solid var(--brass); outline-offset: 2px; }
    .avatar-btn svg { width: 18px; height: 18px; }

    .avatar-hint {
        margin: 14px 0 0;
        font-size: 13px;
        line-height: 1.5;
        text-align: center;
        color: var(--muted);
    }
    .avatar-name {
        margin: 12px 0 0;
        font-size: 13px;
        font-weight: 600;
        color: var(--navy);
        word-break: break-all;
        text-align: center;
    }

    /* ---------- Cột phải: form ---------- */
    .profile-right { flex: 1; min-width: 300px; }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .field { display: flex; flex-direction: column; }
    .field.full { grid-column: 1 / -1; }

    .field label {
        margin-bottom: 6px;
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
    }

    .input {
        width: 100%;
        height: 48px;
        padding: 0 16px;
        font: inherit;
        font-size: 15px;
        color: var(--text);
        background: #fff;
        border: 1.5px solid var(--line);
        border-radius: 10px;
        transition: border-color .15s, box-shadow .15s;
    }
    .input::placeholder { color: #a8b0bb; }
    .input:hover { border-color: #b7bfca; }
    .input:focus {
        outline: none;
        border-color: var(--navy);
        box-shadow: 0 0 0 4px rgba(4, 21, 38, .12);
    }
    .input.is-invalid { border-color: var(--danger); }
    .input.is-invalid:focus { box-shadow: 0 0 0 4px rgba(180, 35, 24, .15); }

    .input[readonly] {
        color: var(--muted);
        background: #f3f5f8;
        border-style: dashed;
        cursor: not-allowed;
    }
    .input[readonly]:focus { box-shadow: none; border-color: var(--line); }

    .field-error { margin-top: 6px; font-size: 13px; color: var(--danger); }
    .field-hint { margin-top: 6px; font-size: 13px; color: var(--muted); }

    .actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 32px;
    }

    .btn-save {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 50px;
        padding: 0 32px;
        font: inherit;
        font-size: 16px;
        font-weight: 600;
        color: #fff;
        background: var(--navy);
        border: 0;
        border-radius: 10px;
        cursor: pointer;
        transition: background .15s, transform .1s;
    }
    .btn-save:hover { background: var(--navy-soft); }
    .btn-save:active { transform: translateY(1px); }
    .btn-save:focus-visible { outline: 3px solid var(--brass); outline-offset: 2px; }
    .btn-save:disabled { opacity: .7; cursor: wait; }
    .btn-save svg { width: 18px; height: 18px; }

    /* ---------- Điện thoại / máy tính bảng ---------- */
    @media (max-width: 768px) {
        .page { padding: 80px 12px 32px; }
        .profile-card { padding: 28px 20px; }
        .profile-layout { flex-direction: column; align-items: center; gap: 28px; }
        .profile-left { flex: none; }
        .profile-right { width: 100%; min-width: 0; }
        .form-grid { grid-template-columns: 1fr; }
        .actions .btn-save { width: 100%; }
    }

    @media (prefers-reduced-motion: reduce) {
        * { transition: none !important; }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="profile-card">

        <div class="profile-head">
            <h2>Thông tin tài khoản</h2>
        </div>

        <?php if(session('success')): ?>
            <div class="alert-box ok" role="status"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert-box error" role="alert">
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('profile.update')); ?>" method="POST" enctype="multipart/form-data" id="profileForm">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="profile-layout">

                
                <div class="profile-left">
                    <div class="avatar-box">
                        <img src="<?php echo e($user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=041526&color=fff'); ?>"
                             id="avatar-preview" class="avatar-img" alt="Ảnh đại diện của <?php echo e($user->name); ?>">

                        <button type="button" class="avatar-btn" id="avatar-btn" aria-label="Đổi ảnh đại diện">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 8h3l2-3h6l2 3h3a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1Z"/><circle cx="12" cy="13" r="3.5"/></svg>
                        </button>
                    </div>

                    <p class="avatar-hint">Ảnh JPG hoặc PNG</p>
                    <p class="avatar-name" id="avatar-name" hidden></p>

                    <input type="file" name="avatar" id="avatar-input" accept="image/jpeg,image/png" hidden>
                </div>

                
                <div class="profile-right">
                    <div class="form-grid">

                        <div class="field">
                            <label for="name">Họ và tên</label>
                            <input id="name" type="text" name="name"
                                   class="input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('name', $user->name)); ?>"
                                   placeholder="Nguyễn Văn A" autocomplete="name" required>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="field-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="field">
                            <label for="phone">Số điện thoại</label>
                            <input id="phone" type="tel" name="phone"
                                   class="input <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('phone', $user->phone ?? '')); ?>"
                                   placeholder="0901234567" autocomplete="tel" inputmode="tel">
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="field-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="field">
                            <label for="email">Email</label>
                            <input id="email" type="email" class="input" value="<?php echo e($user->email); ?>" readonly>
                            <div class="field-hint">Email không thể thay đổi.</div>
                        </div>

                        <div class="field">
                            <label for="city">Quận / Huyện</label>
                            <input id="city" type="text" name="city"
                                   class="input <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('city', $user->city ?? '')); ?>"
                                   placeholder="Ví dụ: Quận 1">
                            <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="field-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="field full">
                            <label for="address">Địa chỉ nhận hàng</label>
                            <input id="address" type="text" name="address"
                                   class="input <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('address', $user->address ?? '')); ?>"
                                   placeholder="Số nhà, tên đường, phường/xã..." autocomplete="street-address">
                            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="field-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="field">
                            <label for="joined">Ngày tham gia</label>
                            <input id="joined" type="text" class="input"
                                   value="<?php echo e($user->created_at ? $user->created_at->format('d/m/Y') : 'Chưa có'); ?>" readonly>
                        </div>

                    </div>

                    <div class="actions">
                        <button type="submit" class="btn-save" id="saveBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4h11l3 3v13H5Z"/><path d="M8 4v5h7V4M8 20v-6h8v6"/></svg>
                            <span id="saveText">Lưu thay đổi</span>
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
(function () {
    var input = document.getElementById('avatar-input');
    var preview = document.getElementById('avatar-preview');
    var nameEl = document.getElementById('avatar-name');

    document.getElementById('avatar-btn').addEventListener('click', function () {
        input.click();
    });

    // Xem trước ảnh vừa chọn
    input.addEventListener('change', function () {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) { preview.src = e.target.result; };
            reader.readAsDataURL(input.files[0]);
            nameEl.textContent = input.files[0].name;
            nameEl.hidden = false;
        }
    });

    // Tránh bấm lưu nhiều lần
    document.getElementById('profileForm').addEventListener('submit', function () {
        var btn = document.getElementById('saveBtn');
        btn.disabled = true;
        document.getElementById('saveText').textContent = 'Đang lưu...';
    });
})();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('shared', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/profile.blade.php ENDPATH**/ ?>