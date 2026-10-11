@extends('shared')

@section('title', 'Quên mật khẩu | Nhà Hàng')

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

    .auth {
        display: grid;
        grid-template-columns: 1fr 1fr;
        min-height: 100vh;
    }

    /* ---------- Cột trái: thương hiệu ---------- */
    .auth-brand {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 64px;
        color: #fff;
        background:
            linear-gradient(160deg, rgba(4, 21, 38, .92), rgba(13, 42, 71, .88)),
            repeating-linear-gradient(45deg, rgba(255,255,255,.03) 0 2px, transparent 2px 14px),
            var(--navy);
        overflow: hidden;
    }

    .auth-brand::after {
        content: '';
        position: absolute;
        right: -120px;
        bottom: -120px;
        width: 360px;
        height: 360px;
        border: 1px solid rgba(200, 150, 62, .45);
        border-radius: 50%;
    }

    .auth-brand h1 {
        margin: 0 0 16px;
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 48px;
        line-height: 1.15;
        font-weight: 700;
        letter-spacing: .5px;
    }

    .auth-brand p {
        max-width: 380px;
        margin: 0;
        font-size: 17px;
        line-height: 1.7;
        color: rgba(255, 255, 255, .78);
    }

    .auth-brand .brass-line {
        width: 56px;
        height: 3px;
        margin-bottom: 28px;
        background: var(--brass);
        border-radius: 2px;
    }

    /* ---------- Cột phải: form ---------- */
    .auth-form-side {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 24px;
        background: #fff;
    }

    .auth-card { width: 100%; max-width: 400px; }

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

    /* ---------- Điện thoại / máy tính bảng ---------- */
    @media (max-width: 900px) {
        .auth { grid-template-columns: 1fr; }
        .auth-brand { padding: 32px 24px; }
        .auth-brand::after { display: none; }
        .auth-brand h1 { font-size: 30px; margin-bottom: 0; }
        .auth-brand p, .auth-brand .brass-line { display: none; }
        .auth-form-side { align-items: flex-start; padding-top: 32px; }
    }

    @media (prefers-reduced-motion: reduce) {
        * { transition: none !important; }
    }
</style>
@endsection

@section('content')
<main class="auth">

    <section class="auth-brand">
        <div class="brass-line"></div>
        <h1>Nhà Hàng</h1>
        <p>Quên mật khẩu không sao cả. Chúng tôi sẽ gửi mã xác nhận để bạn đặt lại ngay.</p>
    </section>

    <section class="auth-form-side">
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
    </section>

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