<?php $__env->startSection('title', 'Đăng ký | Nhà Hàng'); ?>

<?php $__env->startSection('body-class', 'p-0'); ?>

<?php $__env->startSection('head'); ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    :root {
        --navy: #041526;
        --navy-soft: #0d2a47;
        --brass: #c8963e;
        --text: #1f2937;
        --muted: #6b7280;
        --line: #d9dee5;
        --danger: #b42318;
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

    .auth-card h2 {
        margin: 0 0 6px;
        font-size: 28px;
        font-weight: 700;
        color: var(--navy);
    }

    .auth-card .sub {
        margin: 0 0 26px;
        font-size: 15px;
        color: var(--muted);
    }

    .field { margin-bottom: 16px; }

    .field label {
        display: block;
        margin-bottom: 6px;
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
    }

    .field .optional {
        font-weight: 400;
        color: var(--muted);
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
        padding: 0 44px;
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

    .field-error {
        display: none;
        margin-top: 6px;
        font-size: 13px;
        color: var(--danger);
    }
    .field-error.show { display: block; }

    .hint {
        margin-top: 6px;
        font-size: 13px;
        color: var(--muted);
    }

    .toggle-pass {
        position: absolute;
        top: 50%;
        right: 6px;
        display: grid;
        place-items: center;
        width: 36px;
        height: 36px;
        padding: 0;
        color: #8a93a0;
        background: none;
        border: 0;
        border-radius: 8px;
        transform: translateY(-50%);
        cursor: pointer;
    }
    .toggle-pass:hover { color: var(--navy); background: #f1f3f6; }
    .toggle-pass:focus-visible { outline: 2px solid var(--navy); outline-offset: 1px; }
    .toggle-pass svg { width: 20px; height: 20px; }

    .btn-submit {
        width: 100%;
        height: 50px;
        margin-top: 8px;
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

    .login-row {
        margin: 24px 0 0;
        text-align: center;
        font-size: 15px;
        color: var(--muted);
    }

    .link {
        color: var(--navy);
        font-weight: 600;
        text-decoration: none;
    }
    .link:hover { color: var(--brass); text-decoration: underline; }
    .link:focus-visible { outline: 2px solid var(--navy); outline-offset: 2px; border-radius: 4px; }

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
            <h2>Tạo tài khoản</h2>
            <p class="sub">Điền thông tin bên dưới, chỉ mất chưa đến một phút.</p>

            <form id="registerForm" novalidate>
                <?php echo csrf_field(); ?>

                <div class="field">
                    <label for="name">Họ và tên</label>
                    <div class="input-wrap">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>
                        <input id="name" type="text" name="name" class="input" placeholder="Nguyễn Văn A" autocomplete="name" required autofocus>
                    </div>
                    <div class="field-error" data-error-for="name"></div>
                </div>

                <div class="field">
                    <label for="phone">Số điện thoại <span class="optional">(không bắt buộc)</span></label>
                    <div class="input-wrap">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"/></svg>
                        <input id="phone" type="tel" name="phone" class="input" placeholder="0901234567" autocomplete="tel" inputmode="tel">
                    </div>
                    <div class="field-error" data-error-for="phone"></div>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                        <input id="email" type="email" name="email" class="input" placeholder="ten@example.com" autocomplete="email" required>
                    </div>
                    <div class="field-error" data-error-for="email"></div>
                </div>

                <div class="field">
                    <label for="password">Mật khẩu</label>
                    <div class="input-wrap">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                        <input id="password" type="password" name="password" class="input" placeholder="Nhập mật khẩu" autocomplete="new-password" required>
                        <button type="button" class="toggle-pass" data-target="password" aria-label="Hiện mật khẩu" aria-pressed="false">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div class="field-error" data-error-for="password"></div>
                </div>

                <div class="field">
                    <label for="password_confirmation">Nhập lại mật khẩu</label>
                    <div class="input-wrap">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="input" placeholder="Nhập lại mật khẩu" autocomplete="new-password" required>
                        <button type="button" class="toggle-pass" data-target="password_confirmation" aria-label="Hiện mật khẩu" aria-pressed="false">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div class="field-error" data-error-for="password_confirmation"></div>
                </div>

                <button type="submit" id="btnSubmit" class="btn-submit">Tạo tài khoản</button>

                <p class="login-row">
                    Đã có tài khoản?
                    <a href="<?php echo e(route('login')); ?>" class="link">Đăng nhập</a>
                </p>
            </form>
        </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    var eyeOpen = '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>';
    var eyeOff = '<path d="M3 3l18 18"/><path d="M10.6 5.1A10 10 0 0 1 12 5c6.5 0 10 7 10 7a17 17 0 0 1-3.2 4.1M6.6 6.6C3.9 8.4 2 12 2 12s3.5 7 10 7a9.7 9.7 0 0 0 4.4-1"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/>';

    // Hiện / ẩn mật khẩu
    $('.toggle-pass').on('click', function () {
        var $btn = $(this);
        var $input = $('#' + $btn.data('target'));
        var show = $input.attr('type') === 'password';
        $input.attr('type', show ? 'text' : 'password');
        $btn.attr('aria-pressed', show)
            .attr('aria-label', show ? 'Ẩn mật khẩu' : 'Hiện mật khẩu')
            .find('svg').html(show ? eyeOff : eyeOpen);
        $input.focus();
    });

    function clearErrors() {
        $('.input').removeClass('is-invalid');
        $('.field-error').removeClass('show').text('');
    }

    function showError(name, message) {
        $('#' + name).addClass('is-invalid');
        $('[data-error-for="' + name + '"]').text(message).addClass('show');
    }

    $('#registerForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        // Kiểm tra nhanh trước khi gửi
        var hasError = false;
        if (!$('#name').val().trim()) { showError('name', 'Vui lòng nhập họ và tên.'); hasError = true; }
        if (!$('#email').val().trim()) { showError('email', 'Vui lòng nhập email.'); hasError = true; }
        if (!$('#password').val()) { showError('password', 'Vui lòng nhập mật khẩu.'); hasError = true; }
        if ($('#password').val() !== $('#password_confirmation').val()) {
            showError('password_confirmation', 'Mật khẩu nhập lại chưa khớp.');
            hasError = true;
        }
        if (hasError) {
            $('.input.is-invalid').first().focus();
            return;
        }

        var btn = $('#btnSubmit');
        btn.prop('disabled', true).text('Đang xử lý...');

        $.ajax({
            url: "<?php echo e(route('register.post')); ?>",
            method: 'POST',
            data: $(this).serialize(),
            success: function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Đăng ký thành công!',
                    text: 'Đang chuyển đến trang đăng nhập...',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                }).then(function () {
                    window.location.href = "<?php echo e(route('login')); ?>";
                });
            },
            error: function (xhr) {
                btn.prop('disabled', false).text('Tạo tài khoản');

                var errors = xhr.responseJSON && xhr.responseJSON.errors;

                if (errors) {
                    // Hiện lỗi ngay dưới từng ô nhập
                    $.each(errors, function (key, messages) {
                        showError(key, messages[0]);
                    });
                    $('.input.is-invalid').first().focus();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Chưa thể đăng ký',
                        text: 'Có lỗi xảy ra, vui lòng thử lại sau.'
                    });
                }
            }
        });
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('shared', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/register.blade.php ENDPATH**/ ?>