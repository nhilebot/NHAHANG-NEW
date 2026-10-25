@extends('shared')

@section('title', 'Quên mật khẩu | Nhà Hàng')

@section('body-class', 'p-0')

@section('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/pages/forgot-password.css') }}?v=1">
@endsection

@section('content')
<main class="auth">
    <div class="auth-card">
                <a href="{{ route('login') }}" class="back-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                Quay lại đăng nhập
            </a>

            <h2>Quên mật khẩu</h2>
            <p class="sub">Nhập email bạn đã đăng ký, chúng tôi sẽ gửi mã OTP để khôi phục tài khoản.</p>

            @if(session('status'))
                <div class="alert-box ok" role="status">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <div class="alert-box error" role="alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" id="forgotForm" novalidate>
                @csrf

                <div class="field">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                        <input id="email" type="email" name="email"
                               class="input @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="ten@example.com"
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
@endsection