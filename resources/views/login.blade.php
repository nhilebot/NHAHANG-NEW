@extends('shared')

@section('title', 'Đăng nhập | Nhà Hàng')

@section('body-class', 'p-0')

@section('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/pages/login.css') }}?v=1">
@endsection

@section('content')
<main class="auth">
    <div class="auth-card">
            <h2>Đăng nhập</h2>
            <p class="sub">Chào mừng bạn quay trở lại!</p>

            @if(session('success'))
                <div class="alert-box ok" role="status">{{ session('success') }}</div>
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

            <form method="POST" action="{{ route('login.post') }}" id="loginForm" novalidate>
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

                <div class="field">
                    <label for="password">Mật khẩu</label>
                    <div class="input-wrap">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                        <input id="password" type="password" name="password"
                               class="input @error('password') is-invalid @enderror"
                               placeholder="Nhập mật khẩu"
                               autocomplete="current-password" required>
                        <button type="button" class="toggle-pass" id="togglePass" aria-label="Hiện mật khẩu" aria-pressed="false">
                            <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <div class="row-between">
                    <label class="remember" for="remember">
                        <input type="checkbox" id="remember" name="remember">
                        Ghi nhớ tôi
                    </label>
                    <a href="{{ route('password.request') }}" class="link">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn-login" id="submitBtn">Đăng nhập</button>

                <p class="register">
                    Bạn chưa có tài khoản?
                    <a href="{{ route('register') }}" class="link">Đăng ký ngay</a>
                </p>
            </form>
        </div>
</main>

<script>
    (function () {
        var pass = document.getElementById('password');
        var btn = document.getElementById('togglePass');
        var eye = document.getElementById('eyeIcon');

        var eyeOpen = '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>';
        var eyeOff = '<path d="M3 3l18 18"/><path d="M10.6 5.1A10 10 0 0 1 12 5c6.5 0 10 7 10 7a17 17 0 0 1-3.2 4.1M6.6 6.6C3.9 8.4 2 12 2 12s3.5 7 10 7a9.7 9.7 0 0 0 4.4-1"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/>';

        btn.addEventListener('click', function () {
            var show = pass.type === 'password';
            pass.type = show ? 'text' : 'password';
            btn.setAttribute('aria-pressed', show);
            btn.setAttribute('aria-label', show ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
            eye.innerHTML = show ? eyeOff : eyeOpen;
            pass.focus();
        });

        // Tránh bấm đăng nhập nhiều lần
        var form = document.getElementById('loginForm');
        var submit = document.getElementById('submitBtn');
        form.addEventListener('submit', function () {
            submit.disabled = true;
            submit.textContent = 'Đang đăng nhập...';
        });
    })();
</script>
@endsection