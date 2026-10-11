<?php $__env->startSection('title', 'Quên mật khẩu | Nhà Hàng'); ?>

<?php $__env->startSection('body-class', 'p-0'); ?>

<?php $__env->startSection('head'); ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
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
        background: #fff;
    }

    /* ---------- Hộp form ở giữa trang ---------- */
    body { background: #eef1f5; }

    .auth {
        display: flex;
        min-height: 100vh;
        padding: 96px 16px 48px; /* chừa chỗ cho thanh menu cố định của layout */
    }

    /* margin:auto giúp hộp nằm giữa khi còn chỗ và không bị cắt phần đầu khi form cao */
    .auth-card {
        width: 100%;
        max-width: 440px;
        margin: auto;
        padding: 40px;
        background: #fff;
        border-top: 5px solid var(--navy);
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(4, 21, 38, .08);
    }

    .brand { margin-bottom: 24px; text-align: center; }
    .brand-name {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 28px;
        font-weight: 700;
        letter-spacing: .5px;
        color: var(--navy);
    }
    .brand::after {
        content: '';
        display: block;
        width: 40px;
        height: 3px;
        margin: 10px auto 0;
        background: var(--brass);
        border-radius: 2px;
    }

    .auth-card h2, .auth-card .sub { text-align: center; }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 28px;
        font-size: 14px;
        font-weight: 600;
        color: var(--muted);
        text-decoration: none;
    }
    .back-link:hover { color: var(--navy); }
    .back-link:focus-visible { outline: 2px solid var(--navy); outline-offset: 2px; border-radius: 4px; }
    .back-link svg { width: 18px; height: 18px; }

    .auth-card h2 {
        margin: 0 0 8px;
        font-size: 28px;
        font-weight: 700;
        color: var(--navy);
    }

    .auth-card .sub {
        margin: 0 0 26px;
        font-size: 15px;
        line-height: 1.6;
        color: var(--muted);
    }

    .alert-box {
        margin-bottom: 20px;
        padding: 12px 14px;
        border-radius: 10px;
        font-size: 14px;
        line-height: 1.5;
    }
    .alert-box.error { color: var(--danger); background: var(--danger-bg); border: 1px solid #fecdca; }
    .alert-box.ok { color: var(--success); background: var(--success-bg); border: 1px solid #abefc6; }
    .alert-box ul { margin: 0; padding-left: 18px; }

    .field { margin-bottom: 20px; }

    .field label {
        display: block;
        margin-bottom: 6px;
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
    }

    .input-wrap { position: relative; }

    .input-wrap .icon {
        position: absolute;
        top: 50%;
        left: 14px;
        width: 20px;
        height: 20px;
        transform: translateY(-50%);
        color: #9aa3af;
        pointer-events: none;
    }

    .input {
        width: 100%;
        height: 48px;
        padding: 0 16px 0 44px;
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

    .btn-submit {
        width: 100%;
        height: 50px;
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
    .btn-submit:hover { background: var(--navy-soft); }
    .btn-submit:active { transform: translateY(1px); }
    .btn-submit:focus-visible { outline: 3px solid var(--brass); outline-offset: 2px; }
    .btn-submit:disabled { opacity: .7; cursor: wait; }

    @media (max-width: 520px) {
        .auth { padding: 80px 12px 32px; }
        .auth-card { padding: 28px 20px; }
    }

    @media (prefers-reduced-motion: reduce) {
        * { transition: none !important; }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<main class="auth">
    <div class="auth-card">
                <a href="<?php echo e(route('login')); ?>" class="back-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                Quay lại đăng nhập
            </a>

            <h2>Quên mật khẩu</h2>
            <p class="sub">Nhập email bạn đã đăng ký, chúng tôi sẽ gửi mã OTP để khôi phục tài khoản.</p>

            <?php if(session('status')): ?>
                <div class="alert-box ok" role="status"><?php echo e(session('status')); ?></div>
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

            <form method="POST" action="<?php echo e(route('password.email')); ?>" id="forgotForm" novalidate>
                <?php echo csrf_field(); ?>

                <div class="field">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                        <input id="email" type="email" name="email"
                               class="input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('email')); ?>" placeholder="ten@example.com"
                               autocomplete="email" required autofocus>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">Gửi mã OTP</button>
            </form>
        </div>
</main>

<script>
    // Tránh bấm gửi nhiều lần (mỗi lần gửi sẽ tạo một mã OTP mới)
    document.getElementById('forgotForm').addEventListener('submit', function () {
        var btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.textContent = 'Đang gửi mã...';
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('shared', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/forgot-password.blade.php ENDPATH**/ ?>