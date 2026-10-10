@extends('layouts.admin')

@section('page_title', 'Thêm tài khoản')
@section('topbar_title', 'Thêm tài khoản mới')

@section('admin_content')

<div style="margin-bottom:20px;">
    <a href="{{ route('admin.users.index') }}" style="color:#64748b;text-decoration:none;font-size:.88rem;">← Quay lại danh sách</a>
    <h2 style="font-size:1.3rem;font-weight:800;color:#0f172a;margin:8px 0 4px;">➕ Thêm tài khoản mới</h2>
</div>

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:20px;">
    <strong style="color:#dc2626;">Có lỗi xảy ra:</strong>
    <ul style="margin:8px 0 0 18px;color:#dc2626;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<div style="max-width:600px;">
    <div class="panel">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Họ và tên <span style="color:#dc2626;">*</span></label>
                <input class="form-control" type="text" name="name" value="{{ old('name') }}" placeholder="Nguyễn Văn A" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email <span style="color:#dc2626;">*</span></label>
                <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="email@nhahang.vn" required>
            </div>

            <div class="form-group">
                <label class="form-label">Số điện thoại</label>
                <input class="form-control" type="text" name="phone" value="{{ old('phone') }}" placeholder="0901234567">
            </div>

            <div class="form-group">
                <label class="form-label">Vai trò <span style="color:#dc2626;">*</span></label>
                <select class="form-control form-select" name="role_id" required>
                    <option value="">-- Chọn vai trò --</option>
                    @foreach($roles as $r)
                        @php $rl=['admin'=>'Admin','staff'=>'Nhân viên phục vụ','chef'=>'Đầu bếp','customer'=>'Khách hàng']; @endphp
                        <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>
                            {{ $rl[$r->name] ?? $r->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Mật khẩu <span style="color:#dc2626;">*</span></label>
                    <input class="form-control" type="password" name="password" placeholder="Ít nhất 6 ký tự" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Xác nhận mật khẩu <span style="color:#dc2626;">*</span></label>
                    <input class="form-control" type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu" required>
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">✓ Tạo tài khoản</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Hủy</a>
            </div>
        </form>
    </div>
</div>

@endsection
