@extends('layouts.admin')
@section('title', 'Nhóm nguyên liệu')
@section('page_title', 'Nhóm nguyên liệu')
@section('topbar_title', 'Nhóm nguyên liệu')

@section('admin_content')
@include('admin.inventory._style')

<div class="inv-panel">
    <div class="inv-head">
        <div>
            <h2 class="inv-title">Nhóm nguyên liệu</h2>
            <p class="inv-sub">Thịt, cá, hải sản, rau củ, gia vị, đồ uống...</p>
        </div>
        <a href="{{ route('admin.inventory.categories.create') }}" class="btn btn-primary">+ Thêm nhóm</a>
    </div>

    <form method="GET" class="inv-filter">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên nhóm">
        <button type="submit" class="btn btn-ghost">Tìm</button>
        @if(request('search'))<a href="{{ route('admin.inventory.categories.index') }}" class="btn btn-ghost">Xóa lọc</a>@endif
    </form>

    <div class="inv-wrap">
        <table class="inv-table">
            <thead>
                <tr><th>Tên nhóm</th><th>Mô tả</th><th class="r">Số nguyên liệu</th><th></th></tr>
            </thead>
            <tbody>
            @forelse($categories as $c)
                <tr>
                    <td><strong>{{ $c->name }}</strong></td>
                    <td class="inv-muted">{{ $c->description ?: '—' }}</td>
                    <td class="r">
                        <a href="{{ route('admin.inventory.ingredients.index', ['category_id' => $c->id]) }}">{{ $c->ingredients_count }}</a>
                    </td>
                    <td>
                        <div class="inv-actions">
                            <a href="{{ route('admin.inventory.categories.edit', $c) }}" class="btn btn-sm btn-ghost">Sửa</a>
                            <form method="POST" action="{{ route('admin.inventory.categories.destroy', $c) }}"
                                  onsubmit="return confirm('Xóa nhóm &quot;{{ e($c->name) }}&quot;?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-ghost" style="color:#DC2626">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="inv-empty">Chưa có nhóm nguyên liệu nào. Bấm "Thêm nhóm" để tạo nhóm đầu tiên.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="inv-pager">{{ $categories->links() }}</div>
</div>
@endsection
