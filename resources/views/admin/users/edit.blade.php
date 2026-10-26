@extends('layouts.admin')

@section('page_title', 'Chỉnh sửa tài khoản')
@section('topbar_title', 'Chỉnh sửa tài khoản')

@section('admin_content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/users.css') }}?v=1">
@endpush

<div class="ui-page users-page">

<div class="ui-head ui-head--form">
    <a href="{{ route('admin.users.index') }}" class="ui-back">← Quay lại danh sách</a>
    <h2 class="ui-title">✏️ Chỉnh sửa – {{ $user->name }}</h2>
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
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label">Họ và tên <span class="req">*</span></label>
                <input class="form-control" type="text" name="name" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email <span class="req">*</span></label>
                <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Số điện thoại</label>
                <input class="form-control" type="text" name="phone" value="{{ old('phone', $user->phone) }}">
            </div>

            <div class="form-group">
                <label class="form-label">Vai trò <span class="req">*</span></label>
                <select class="form-control form-select" name="role_id" required>
                    @foreach($roles as $r)
                        @php $rl=['admin'=>'Admin','staff'=>'Nhân viên phục vụ','chef'=>'Đầu bếp','customer'=>'Khách hàng']; @endphp
                        <option value="{{ $r->id }}" {{ (old('role_id', $user->role_id) == $r->id) ? 'selected' : '' }}>
                            {{ $rl[$r->name] ?? $r->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="pw-box">
                <p class="pw-box-title">🔐 Đổi mật khẩu (để trống nếu không muốn thay đổi)</p>
                <div class="form-grid-2">
                    <div class="form-group form-group--flush">
                        <label class="form-label">Mật khẩu mới</label>
                        <input class="form-control" type="password" name="password" placeholder="Ít nhất 6 ký tự">
                    </div>
                    <div class="form-group form-group--flush">
                        <label class="form-label">Xác nhận mật khẩu</label>
                        <input class="form-control" type="password" name="password_confirmation" placeholder="Nhập lại">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">✓ Lưu thay đổi</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Hủy</a>
            </div>
        </form>
    </div>
</div>

</div>

@endsection
