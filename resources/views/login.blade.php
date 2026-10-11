@extends('shared')

@section('title', 'Đăng nhập | Nhà Hàng')

@section('body-class', 'p-0')

@section('head')
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

    .auth-card h2 {
        margin: 0 0 6px;
        font-size: 28px;
        font-weight: 700;
        color: var(--navy);
    }

    .auth-card .sub {
        margin: 0 0 28px;
        font-size: 15px;
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

    .field { margin-bottom: 18px; }

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
        padding: 0 44px 0 44px;
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

    .row-between {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 22px;
        font-size: 14px;
    }

    .remember {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #4b5563;
        cursor: pointer;
        user-select: none;
    }
    .remember input {
        width: 17px;
        height: 17px;
        accent-color: var(--navy);
        cursor: pointer;
    }

    .link {
        color: var(--navy);
        font-weight: 600;
        text-decoration: none;
    }
    .link:hover { color: var(--brass); text-decoration: underline; }
    .link:focus-visible { outline: 2px solid var(--navy); outline-offset: 2px; border-radius: 4px; }

    .btn-login {
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
    .btn-login:hover { background: var(--navy-soft); }
    .btn-login:active { transform: translateY(1px); }
    .btn-login:focus-visible { outline: 3px solid var(--brass); outline-offset: 2px; }
    .btn-login:disabled { opacity: .7; cursor: wait; }

    .register {
        margin: 26px 0 0;
        text-align: center;
        font-size: 15px;
        color: var(--muted);
    }

    @media (max-width: 520px) {
        .auth { padding: 80px 12px 32px; }
        .auth-card { padding: 28px 20px; }
    }

    @media (prefers-reduced-motion: reduce) {
        * { transition: none !important; }
    }
</style>
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