@extends('layouts.admin')

@section('page_title', 'Thêm tài khoản')
@section('topbar_title', 'Thêm tài khoản mới')

@section('admin_content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/users.css') }}?v=1">
@endpush

<div class="ui-page users-page">

<div class="ui-head ui-head--form">
    <a href="{{ route('admin.users.index') }}" class="ui-back">← Quay lại danh sách</a>
    <h2 class="ui-title">➕ Thêm tài khoản mới</h2>
</div>

@if($errors->any())
<div class="form-alert">
    <strong class="form-alert-title">Có lỗi xảy ra:</strong>
    <ul class="form-alert-list">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<div class="form-wrap">
    <div class="panel">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Họ và tên <span class="req">*</span></label>
                <input class="form-control" type="text" name="name" value="{{ old('name') }}" placeholder="Nguyễn Văn A" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email <span class="req">*</span></label>
                <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="email@nhahang.vn" required>
            </div>

            <div class="form-group">
                <label class="form-label">Số điện thoại</label>
                <input class="form-control" type="text" name="phone" value="{{ old('phone') }}" placeholder="0901234567">
            </div>

            <div class="form-group">
                <label class="form-label">Vai trò <span class="req">*</span></label>
                <select class="form-control form-select" name="role_id" required>
                    <option value="">-- Chọn vai trò --</option>
                    @foreach($roles as $r)
                        @php $rl=['admin'=>'Admin','staff'=>'Nhân viên phục vụ','chef'=>'Đầu bếp','customer'=>'Khách hàng']; @endphp
                        <option value="{{ $r->id }}" {{ (old('role_id') == $r->id || (!old('role_id') && request('role') === $r->name)) ? 'selected' : '' }}>
                            {{ $rl[$r->name] ?? $r->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Mật khẩu <span class="req">*</span></label>
                    <input class="form-control" type="password" name="password" placeholder="Ít nhất 6 ký tự" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Xác nhận mật khẩu <span class="req">*</span></label>
                    <input class="form-control" type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">✓ Tạo tài khoản</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Hủy</a>
            </div>
        </form>
    </div>
</div>

</div>

@endsection
