@extends('shared')

@section('title', 'FoodHub - Thông tin tài khoản')

@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }

        .profile-wrapper {
            max-width: 950px;
            margin: 80px auto;
            background: #ffffff;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        .profile-title {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 700;
            color: #2d3436;
            margin-bottom: 40px;
            position: relative;
        }

        .profile-title::after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background: #d9534f;
            margin: 10px auto 0;
            border-radius: 2px;
        }

        .profile-container {
            display: flex;
            flex-wrap: wrap;
            gap: 50px;
        }

        /* --- Cột trái: Avatar --- */
        .profile-left {
            flex: 0 0 180px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .avatar-container {
            width: 160px;
            height: 160px;
            position: relative;
            transition: transform 0.3s ease;
        }

        .avatar-container:hover {
            transform: scale(1.02);
        }

        .profile-avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .edit-avatar-btn {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: #d9534f;
            color: white;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #fff;
            cursor: pointer;
            transition: 0.2s;
        }

        .edit-avatar-btn:hover {
            background: #c9302c;
            transform: rotate(15deg);
        }

        /* --- Cột phải: Form --- */
        .profile-right {
            flex: 1;
            min-width: 300px;
        }

        .profile-form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
        }

        .form-group-custom {
            display: flex;
            flex-direction: column;
        }

        .form-group-custom label {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: #636e72;
            margin-bottom: 8px;
            margin-left: 2px;
        }

        .input-custom {
            width: 100%;
            height: 48px;
            padding: 12px 16px;
            border: 1.5px solid #edf2f7;
            border-radius: 10px;
            background-color: #f8fafc;
            color: #2d3436;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .input-custom:focus {
            border-color: #d9534f;
            background-color: #fff;
            outline: none;
            box-shadow: 0 0 0 4px rgba(217, 83, 79, 0.1);
        }

        .input-readonly {
            background-color: #f1f3f5;
            color: #adb5bd;
            cursor: not-allowed;
            border-style: dashed;
        }

        /* --- Nút bấm --- */
        .btn-update-profile {
            background: #d9534f;
            color: #fff;
            padding: 14px 40px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            margin-top: 40px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-update-profile:hover {
            background: #c9302c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(217, 83, 79, 0.3);
        }

        /* Responsive cho Mobile */
        @media (max-width: 768px) {
            .profile-wrapper { padding: 30px 20px; margin: 40px 15px; }
            .profile-container { flex-direction: column; align-items: center; }
            .profile-right { width: 100%; }
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="profile-wrapper">
        <h2 class="profile-title" style="text-align: center;">Thông tin tài khoản</h2>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="profile-container">
                
                {{-- CỘT TRÁI: AVATAR --}}
                <div class="profile-left">
                    <div class="avatar-container">
                        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}" 
                             id="avatar-preview" class="profile-avatar" alt="Avatar">
                        
                        <button type="button" class="edit-avatar-btn" onclick="document.getElementById('avatar-input').click();">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <p style="margin-top: 15px; font-size: 13px; color: #95a5a6;">Định dạng: JPG, PNG</p>
                    <input type="file" name="avatar" id="avatar-input" style="display:none" onchange="previewImage(this)" accept="image/*">
                </div>

                {{-- CỘT PHẢI: FORM --}}
                <div class="profile-right">
                    <div class="profile-form-grid">
                        
                        <div class="form-group-custom">
                            <label>Họ và tên</label>
                            <input type="text" name="name" value="{{ $user->name }}" class="input-custom" required placeholder="Nhập tên của bạn">
                        </div>

                        <div class="form-group-custom">
                            <label>Số điện thoại</label>
                            <input type="tel" name="phone" value="{{ $user->phone ?? '' }}" class="input-custom" placeholder="090x xxx xxx">
                        </div>

                        <div class="form-group-custom">
                            <label>Email liên hệ</label>
                            <input type="email" value="{{ $user->email }}" class="input-custom input-readonly" readonly title="Không thể thay đổi email">
                        </div>

                        <div class="form-group-custom">
                            <label>Quận / Huyện</label>
                            <input type="text" name="city" value="{{ $user->city ?? '' }}" class="input-custom" placeholder="Ví dụ: Quận 1">
                        </div>

                        <div class="form-group-custom" style="grid-column: span 2;">
                            <label>Địa chỉ nhận hàng</label>
                            <input type="text" name="address" value="{{ $user->address ?? '' }}" class="input-custom" placeholder="Số nhà, tên đường, phường/xã...">
                        </div>

                        <div class="form-group-custom">
                            <label>Ngày gia nhập</label>
                            <input type="text" value="{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}" class="input-custom input-readonly" readonly>
                        </div>
                    </div>

                    <div style="text-align: right;">
                        <button type="submit" class="btn-update-profile">
                            <i class="fas fa-save"></i> Cập nhật ngay
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>