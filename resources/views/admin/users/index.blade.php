@extends('layouts.admin')

@section('page_title', 'Quản lý tài khoản')
@section('topbar_title', 'Quản lý tài khoản')

@section('admin_content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/users.css') }}?v=1">
@endpush

<div class="ui-page users-page">

<div class="ui-head">
    <div>
        <h2 class="ui-title">👥 Danh sách tài khoản</h2>
        <p class="ui-subtitle">Quản lý tất cả tài khoản người dùng trong hệ thống</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">＋ Thêm tài khoản</a>
</div>

{{-- Bộ lọc --}}
<form method="GET" action="{{ route('admin.users.index') }}" class="ui-filter">
    <input class="form-control ui-filter-search" type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Tên hoặc email...">
    <select class="form-control form-select ui-filter-select" name="role">
        <option value="">Tất cả vai trò</option>
        @foreach($roles as $r)
            <option value="{{ $r->name }}" {{ request('role') == $r->name ? 'selected' : '' }}>
                {{ ['admin'=>'Admin','staff'=>'Nhân viên','chef'=>'Đầu bếp','customer'=>'Khách hàng'][$r->name] ?? $r->name }}
            </option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-ghost">Lọc</button>
    @if(request('search') || request('role'))
        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">✕ Xóa lọc</a>
    @endif
</form>

<div class="panel">
    <div class="table-wrap">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên người dùng</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Vai trò</th>
                    <th>Ngày tạo</th>
                    <th class="col-actions">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="cell-id" data-label="#">{{ $user->id }}</td>
                    <td class="cell-user" data-label="Tên người dùng">
                        <div class="ui-user">
                            <div class="ui-avatar">
                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="ui-user-name">{{ $user->name }}</div>
                                @if($user->id === auth()->id())
                                    <span class="ui-tag">Tôi</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="cell-text" data-label="Email">{{ $user->email }}</td>
                    <td class="cell-text" data-label="Số điện thoại">{{ $user->phone ?? '—' }}</td>
                    <td class="cell-role" data-label="Vai trò">
                        @php
                            $roleName = $user->role_id ?? 'customer';
                            $roleLabel = ['admin'=>'Admin','staff'=>'Nhân viên','chef'=>'Đầu bếp','customer'=>'Khách hàng'][$roleName] ?? $roleName;
                        @endphp
                        <span class="role-badge role-{{ $roleName }}">{{ $roleLabel }}</span>
                    </td>
                    <td class="cell-date" data-label="Ngày tạo">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="cell-actions" data-label="Thao tác">
                        <div class="row-actions">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-ghost btn-sm">✏️ Sửa</a>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Xóa tài khoản {{ $user->name }}?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">🗑 Xóa</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="cell-empty">
                        Không tìm thấy tài khoản nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="ui-pager">
        {{ $users->appends(request()->query())->links() }}
    </div>
</div>

</div>

@endsection
