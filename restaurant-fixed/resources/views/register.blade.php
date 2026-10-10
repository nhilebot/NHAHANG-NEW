@extends('shared')

@section('title', 'Đăng ký | Tam Nhi Quán')

@section('body-class', 'd-flex align-items-center min-vh-100')

@section('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    /* Giữ nguyên các style cũ của bạn */
    body { font-family: "Times New Roman"; }
    .breeze-card { 
        background: white; max-width: 500px; margin: 100px auto; 
        padding: 2.5rem; border-radius: 15px; 
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border-top: 5px solid #041526e0;
    }
    .login-title { font-family: 'Pacifico', cursive; color: #041526e0; font-size: 36px; margin-bottom: 10px; }
    .form-control { border-radius: 30px; border: 1px solid #ddd; padding: 10px 20px; height: auto; font-family: sans-serif; font-size: 14px; }
    .btn-breeze { 
        background-color: #041526e0; color: white; border-radius: 30px; 
        padding: 12px 25px; font-weight: bold; text-transform: uppercase; 
        width: 100%; margin-top: 10px; border: none; transition: 0.3s; 
    }
    .btn-breeze:hover { background-color: #041526e0; transform: scale(1.02); }
    .breeze-label { display: block; font-size: 16px; font-weight: 500; color: #333; margin-bottom: 0.3rem; padding-left: 10px; }
    .login-link { font-size: 15px; color: #777; text-decoration: none; font-family: sans-serif; }
</style>
@endsection

@section('content')
<div class="container">
    <div class="breeze-card">
        <div class="text-center mb-4">
            <h3 class="login-title">Đăng Ký</h3>
            <p class="subtitle-text">Nhà hàng Việt</p>
        </div>

        <form id="registerForm">
            @csrf
            <div class="mb-3">
                <label for="name" class="breeze-label">Họ và tên</label>
                <input id="name" type="text" name="name" class="form-control" placeholder="Nguyễn Văn A" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="breeze-label">Số điện thoại</label>
                <input id="phone" type="text" name="phone" class="form-control" placeholder="090xxxxxxx">
            </div>

            <div class="mb-3">
                <label for="email" class="breeze-label">Email</label>
                <input id="email" type="email" name="email" class="form-control" placeholder="email@vi-du.com" required>
            </div>

            <div class="mb-3">
                <label for="password" class="breeze-label">Mật khẩu</label>
                <input id="password" type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="breeze-label">Xác nhận mật khẩu</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
            </div>

            <!-- <div class="mb-4 form-check custom-check">
                <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                <label class="form-check-label" for="terms">
                    Tôi đồng ý với các Điều khoản & Dịch vụ
                </label>
            </div> -->

            <button type="submit" id="btnSubmit" class="btn btn-breeze">TẠO TÀI KHOẢN</button>
            
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="login-link">Đã có tài khoản? <strong>Đăng nhập ngay</strong></a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('#registerForm').on('submit', function(e) {
        e.preventDefault(); // Ngăn load lại trang

        let formData = $(this).serialize();
        let btn = $('#btnSubmit');

        btn.prop('disabled', true).text('ĐANG XỬ LÝ...');

        $.ajax({
            url: "{{ route('register.post') }}",
            method: "POST",
            data: formData,
            success: function(response) {
                // Hiển thị thông báo thành công bằng SweetAlert2
                Swal.fire({
                    icon: 'success',
                    title: 'Đăng ký thành công!',
                    // text: 'Chào mừng bạn đến với Aurora Garden.',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = "{{ route('login') }}";
                });
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('TẠO TÀI KHOẢN');
                
                // Lấy lỗi từ Laravel Validation
                let errors = xhr.responseJSON.errors;
                let errorString = '';
                
                if (errors) {
                    $.each(errors, function(key, value) {
                        errorString += value[0] + '<br>';
                    });
                } else {
                    errorString = 'Có lỗi xảy ra, vui lòng thử lại!';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Địa chỉ email đó đã được sử dụng rồi.',
                    // html: errorString,
                });
            }
        });
    });
});
</script>
@endsection