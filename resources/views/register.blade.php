@extends('shared')

@section('title', 'Đăng ký | Nhà Hàng')

@section('body-class', 'p-0')

@section('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="{{ asset('css/pages/register.css') }}?v=1">
@endsection

@section('content')
<main class="auth">
    <div class="auth-card">
            <h2>Tạo tài khoản</h2>
            <p class="sub">Điền thông tin bên dưới, chỉ mất chưa đến một phút.</p>

            <form id="registerForm" novalidate>
                @csrf

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
                    <a href="{{ route('login') }}" class="link">Đăng nhập</a>
                </p>
            </form>
        </div>
</main>
@endsection

@section('scripts')
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
            url: "{{ route('register.post') }}",
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
                    window.location.href = "{{ route('login') }}";
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
@endsection