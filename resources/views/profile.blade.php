@extends('shared')

@section('title', 'FoodHub - Thông tin tài khoản')

@section('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/pages/profile.css') }}?v=1">
@endsection

@section('content')
<div class="page">
    <div class="profile-card">

        <div class="profile-head">
            <h2>Thông tin tài khoản</h2>
        </div>

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

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
            @csrf
            @method('PUT')

            <div class="profile-layout">

                {{-- CỘT TRÁI: ẢNH ĐẠI DIỆN --}}
                <div class="profile-left">
                    <div class="avatar-box">
                        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=041526&color=fff' }}"
                             id="avatar-preview" class="avatar-img" alt="Ảnh đại diện của {{ $user->name }}">

                        <button type="button" class="avatar-btn" id="avatar-btn" aria-label="Đổi ảnh đại diện">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 8h3l2-3h6l2 3h3a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1Z"/><circle cx="12" cy="13" r="3.5"/></svg>
                        </button>
                    </div>

                    <p class="avatar-hint">Ảnh JPG hoặc PNG</p>
                    <p class="avatar-name" id="avatar-name" hidden></p>

                    <input type="file" name="avatar" id="avatar-input" accept="image/jpeg,image/png" hidden>
                </div>

                {{-- CỘT PHẢI: THÔNG TIN --}}
                <div class="profile-right">
                    <div class="form-grid">

                        <div class="field">
                            <label for="name">Họ và tên</label>
                            <input id="name" type="text" name="name"
                                   class="input @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}"
                                   placeholder="Nguyễn Văn A" autocomplete="name" required>
                            @error('name') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label for="phone">Số điện thoại</label>
                            <input id="phone" type="tel" name="phone"
                                   class="input @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $user->phone ?? '') }}"
                                   placeholder="0901234567" autocomplete="tel" inputmode="tel">
                            @error('phone') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label for="email">Email</label>
                            <input id="email" type="email" class="input" value="{{ $user->email }}" readonly>
                            <div class="field-hint">Email không thể thay đổi.</div>
                        </div>

                        <div class="field">
                            <label for="city">Quận / Huyện</label>
                            <input id="city" type="text" name="city"
                                   class="input @error('city') is-invalid @enderror"
                                   value="{{ old('city', $user->city ?? '') }}"
                                   placeholder="Ví dụ: Quận 1">
                            @error('city') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field full">
                            <label for="address">Địa chỉ nhận hàng</label>
                            <input id="address" type="text" name="address"
                                   class="input @error('address') is-invalid @enderror"
                                   value="{{ old('address', $user->address ?? '') }}"
                                   placeholder="Số nhà, tên đường, phường/xã..." autocomplete="street-address">
                            @error('address') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label for="joined">Ngày tham gia</label>
                            <input id="joined" type="text" class="input"
                                   value="{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'Chưa có' }}" readonly>
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
@endsection