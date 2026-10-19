@extends('layouts.admin')
@section('title', 'Nhà cung cấp')
@section('page_title', 'Nhà cung cấp')
@section('topbar_title', 'Nhà cung cấp')

@section('admin_content')
@include('admin.inventory._style')

<div class="inv-panel">
    <div class="inv-head">
        <div>
            <h2 class="inv-title">Nhà cung cấp</h2>
            <p class="inv-sub">Nơi nhà hàng nhập nguyên liệu.</p>
        </div>
        <a href="{{ route('admin.inventory.suppliers.create') }}" class="btn btn-primary">+ Thêm nhà cung cấp</a>
    </div>

    <form method="GET" class="inv-filter">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo mã, tên, SĐT, email">
        <select name="status" onchange="this.form.submit()">
            <option value="">Tất cả trạng thái</option>
            <option value="active" @selected(request('status') === 'active')>Đang hợp tác</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Ngừng hợp tác</option>
        </select>
        <button type="submit" class="btn btn-ghost">Tìm</button>
        @if(request('search') || request('status'))<a href="{{ route('admin.inventory.suppliers.index') }}" class="btn btn-ghost">Xóa lọc</a>@endif
    </form>

    <div class="inv-wrap">
        <table class="inv-table">
            <thead>
                <tr><th>Mã</th><th>Tên</th><th>Điện thoại</th><th>Email</th><th>Trạng thái</th><th></th></tr>
            </thead>
            <tbody>
            @forelse($suppliers as $s)
                <tr class="{{ $s->is_active ? '' : 'is-off' }}">
                    <td class="inv-code">{{ $s->code }}</td>
                    <td><a href="{{ route('admin.inventory.suppliers.show', $s) }}"><strong>{{ $s->name }}</strong></a></td>
                    <td>{{ $s->phone ?: '—' }}</td>
                    <td class="inv-muted">{{ $s->email ?: '—' }}</td>
                    <td>
                        @if($s->is_active)<span class="inv-badge ok">Đang hợp tác</span>
                        @else<span class="inv-badge off">Ngừng hợp tác</span>@endif
                    </td>
                    <td>
                        <div class="inv-actions">
                            <a href="{{ route('admin.inventory.suppliers.show', $s) }}" class="btn btn-sm btn-ghost">Xem</a>
                            <a href="{{ route('admin.inventory.suppliers.edit', $s) }}" class="btn btn-sm btn-ghost">Sửa</a>
                            <form method="POST" action="{{ route('admin.inventory.suppliers.destroy', $s) }}"
                                  onsubmit="return confirm('Xóa nhà cung cấp &quot;{{ e($s->name) }}&quot;?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-ghost" style="color:#DC2626">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="inv-empty">Chưa có nhà cung cấp nào phù hợp.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="inv-pager">{{ $suppliers->links() }}</div>
</div>
@endsection
