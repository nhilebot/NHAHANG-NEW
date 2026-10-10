@extends('shared')

@section('title', 'Quên mật khẩu | Tam Nhi Quán')

@section('body-class', 'd-flex align-items-center min-vh-100')

@section('head')
<style>
    body { background-color: #f9f9f9; font-family: "Times New Roman", serif; }
    .auth-card { background: white; max-width: 500px; margin: 80px auto; padding: 2.5rem; border-radius: 18px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); border-top: 5px solid #041526e0; }
    .auth-title { font-family: 'Pacifico', cursive; color: #041526e0; font-size: 36px; margin-bottom: 18px; }
    .form-control { border-radius: 30px; border: 1px solid #ddd; padding: 12px 18px; height: auto; }
    .form-control:focus { border-color: #041526e0; box-shadow: 0 0 0 3px rgba(231,76,60,0.18); }
    .btn-primary-custom { background-color: #041526e0; color: white; border-radius: 30px; border: none; padding: 12px 20px; width: 100%; font-weight: 700; }
    .btn-primary-custom:hover { background-color: #041526e0; }
    .text-link { color: #777; text-decoration: none; }
    .text-link:hover { color: #041526e0; text-decoration: underline; }
</style>
@endsection

@section('content')
<div class="container">
    <div class="auth-card">
        <div class="text-center mb-4">
            <h3 class="auth-title">Quên mật khẩu</h3>
            <p style="color: #555; font-size: 14px;">Nhập email để nhận mã OTP khôi phục tài khoản.</p>
        </div>

        @if(session('status'))
            <div class="alert alert-success" style="border-radius: 20px;">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="border-radius: 15px;">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="your@email.com" required autofocus>
            </div>

            <button type="submit" class="btn btn-primary-custom">Gửi mã OTP</button>

            <div class="text-center mt-4" style="font-size: 14px;">
                <a href="{{ route('login') }}" class="text-link">Quay lại đăng nhập</a>
            </div>
        </form>
    </div>
</div>
@endsection
