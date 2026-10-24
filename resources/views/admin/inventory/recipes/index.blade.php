@extends('layouts.admin')
@section('title', 'Công thức món ăn')
@section('page_title', 'Công thức món ăn')
@section('topbar_title', 'Công thức món ăn')

@section('admin_content')
@include('admin.inventory._style')
@php $hasFilter = request()->hasAny(['search', 'category_id', 'status']); @endphp

<div class="inv-panel">
    <div class="inv-head">
        <div>
            <h2 class="inv-title">Công thức món ăn</h2>
            <p class="inv-sub">Định lượng nguyên liệu cho <strong>một phần</strong> của từng món trong thực đơn.</p>
        </div>
        <a href="{{ route('admin.inventory.recipes.create') }}" class="btn btn-primary">+ Thêm công thức</a>
    </div>

    <div class="inv-stats">
        <div class="inv-stat"><p>Tổng số món</p><b>{{ $totalMenus }}</b></div>
        <div class="inv-stat {{ $noRecipe ? 'warn' : '' }}"><p>Món chưa có công thức</p><b>{{ $noRecipe }}</b></div>
        <div class="inv-stat {{ $sampleCount ? 'warn' : '' }}"><p>Công thức minh họa (cần xác nhận)</p><b>{{ $sampleCount }}</b></div>
    </div>

    @if($noRecipe > 0)
        <div class="inv-readonly" style="margin-bottom:16px;border-style:solid;border-color:#FCD34D;background:#FFFBEB">
            ⚠️ Có <strong>{{ $noRecipe }}</strong> món chưa có công thức. Những món này chưa thể tính lượng nguyên liệu khi bếp chế biến.
            <a href="{{ route('admin.inventory.recipes.index', ['status' => 'none']) }}">Xem danh sách</a>
        </div>
    @endif

    <form method="GET" class="inv-filter">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên món">
        <select name="category_id" onchange="this.form.submit()">
            <option value="">Tất cả danh mục</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()">
            <option value="">Mọi trạng thái</option>
            <option value="has" @selected(request('status') === 'has')>Đã có công thức</option>
            <option value="none" @selected(request('status') === 'none')>Chưa có công thức</option>
            <option value="sample" @selected(request('status') === 'sample')>Công thức minh họa</option>
        </select>
        <button type="submit" class="btn btn-ghost">Tìm</button>
        @if($hasFilter)<a href="{{ route('admin.inventory.recipes.index') }}" class="btn btn-ghost">Xóa lọc</a>@endif
    </form>

    <div class="inv-wrap">
        <table class="inv-table">
            <thead>
                <tr>
                    <th>Món</th><th>Danh mục</th><th class="r">Giá bán</th>
                    <th class="r">Số nguyên liệu</th><th>Công thức</th><th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($menus as $m)
                @php $r = $m->recipe; @endphp
                <tr class="{{ !$r ? 'is-low' : '' }}">
                    <td>
                        <strong>{{ $m->name }}</strong>
                        @if((int) $m->status === 0)<span class="inv-badge off">Ngừng bán</span>@endif
                    </td>
                    <td class="inv-muted">{{ $m->category_relation->name ?? '—' }}</td>
                    <td class="r">{{ number_format($m->price, 0, ',', '.') }}đ</td>
                    <td class="r">{{ $r ? $r->items_count : '—' }}</td>
                    <td>
                        @if(!$r)<span class="inv-badge low">Chưa có công thức</span>
                        @elseif($r->is_sample)<span class="inv-badge low">Minh họa, cần xác nhận</span>
                        @else<span class="inv-badge ok">Đã xác nhận</span>@endif
                    </td>
                    <td>
                        <div class="inv-actions">
                            @if($r)
                                <a href="{{ route('admin.inventory.recipes.show', $r) }}" class="btn btn-sm btn-ghost">Xem</a>
                                <a href="{{ route('admin.inventory.recipes.edit', $r) }}" class="btn btn-sm btn-ghost">Sửa</a>
                                <form method="POST" action="{{ route('admin.inventory.recipes.destroy', $r) }}"
                                      onsubmit="return confirm('Xóa công thức của &quot;{{ e($m->name) }}&quot;?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-ghost" style="color:#DC2626">Xóa</button>
                                </form>
                            @else
                                <a href="{{ route('admin.inventory.recipes.create', ['menu_id' => $m->id]) }}" class="btn btn-sm btn-primary">Thêm công thức</a>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="inv-empty">
                    {{ $hasFilter ? 'Không có món nào khớp bộ lọc.' : 'Chưa có món nào trong thực đơn.' }}
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="inv-pager">{{ $menus->links() }}</div>
</div>
@endsection
