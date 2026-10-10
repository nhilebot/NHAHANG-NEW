@extends('layouts.admin')

@section('page_title', 'Quản lý tài khoản')
@section('topbar_title', 'Quản lý tài khoản')

@section('admin_content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <div>
        <h2 style="font-size:1.3rem;font-weight:800;color:#0f172a;margin:0;">👥 Danh sách tài khoản</h2>
        <p style="font-size:.85rem;color:#64748b;margin:4px 0 0;">Quản lý tất cả tài khoản người dùng trong hệ thống</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">＋ Thêm tài khoản</a>
</div>

{{-- Bộ lọc --}}
<form method="GET" action="{{ route('admin.users.index') }}" style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
    <input class="form-control" style="width:240px;" type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Tên hoặc email...">
    <select class="form-control form-select" style="width:180px;" name="role">
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
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên người dùng</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Vai trò</th>
                    <th>Ngày tạo</th>
                    <th style="text-align:center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td style="color:#94a3b8;font-size:.82rem;">{{ $user->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#5b3cff,#a78bff);display:grid;place-items:center;color:#fff;font-weight:700;font-size:.85rem;flex-shrink:0;">
                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;color:#0f172a;">{{ $user->name }}</div>
                                @if($user->id === auth()->id())
                                    <span style="font-size:.72rem;background:#eff6ff;color:#1d4ed8;padding:1px 6px;border-radius:10px;">Tôi</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="color:#475569;">{{ $user->email }}</td>
                    <td style="color:#475569;">{{ $user->phone ?? '—' }}</td>
                    <td>
                        @php
                            $roleName = $user->role_id ?? 'customer';
                            $roleLabel = ['admin'=>'Admin','staff'=>'Nhân viên','chef'=>'Đầu bếp','customer'=>'Khách hàng'][$roleName] ?? $roleName;
                        @endphp
                        <span class="role-badge role-{{ $roleName }}">{{ $roleLabel }}</span>
                    </td>
                    <td style="color:#94a3b8;font-size:.83rem;">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:center;">
                        <div style="display:flex;gap:6px;justify-content:center;">
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
                    <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">
                        Không tìm thấy tài khoản nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">
        {{ $users->appends(request()->query())->links() }}
    </div>
</div>

@endsection
