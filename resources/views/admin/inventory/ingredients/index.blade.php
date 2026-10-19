@extends('layouts.admin')
@section('title', 'Nguyên liệu')
@section('page_title', 'Nguyên liệu')
@section('topbar_title', 'Nguyên liệu')

@section('admin_content')
@include('admin.inventory._style')
@php $hasFilter = request()->hasAny(['search', 'category_id', 'level', 'status']); @endphp

<div class="inv-panel">
    <div class="inv-head">
        <div>
            <h2 class="inv-title">Nguyên liệu</h2>
            <p class="inv-sub">Tồn kho nguyên liệu. Khác với "số phần có thể bán" của từng món trong thực đơn.</p>
        </div>
        <a href="{{ route('admin.inventory.ingredients.create') }}" class="btn btn-primary">+ Thêm nguyên liệu</a>
    </div>

    <div class="inv-stats">
        <div class="inv-stat"><p>Tổng số nguyên liệu</p><b>{{ $totalCount }}</b></div>
        <div class="inv-stat {{ $lowCount ? 'warn' : '' }}"><p>Sắp hết</p><b>{{ $lowCount }}</b></div>
        <div class="inv-stat {{ $outCount ? 'bad' : '' }}"><p>Hết hàng</p><b>{{ $outCount }}</b></div>
    </div>

    <form method="GET" class="inv-filter">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo mã hoặc tên">
        <select name="category_id" onchange="this.form.submit()">
            <option value="">Tất cả nhóm</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <select name="level" onchange="this.form.submit()">
            <option value="">Mọi mức tồn</option>
            <option value="ok" @selected(request('level') === 'ok')>Đủ hàng</option>
            <option value="low" @selected(request('level') === 'low')>Sắp hết</option>
            <option value="out" @selected(request('level') === 'out')>Hết hàng</option>
        </select>
        <select name="status" onchange="this.form.submit()">
            <option value="">Mọi trạng thái</option>
            <option value="active" @selected(request('status') === 'active')>Đang sử dụng</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Ngừng sử dụng</option>
        </select>
        <button type="submit" class="btn btn-ghost">Tìm</button>
        @if($hasFilter)<a href="{{ route('admin.inventory.ingredients.index') }}" class="btn btn-ghost">Xóa lọc</a>@endif
    </form>

    <div class="inv-wrap">
        <table class="inv-table">
            <thead>
                <tr>
                    <th>Mã</th><th>Tên</th><th>Nhóm</th>
                    <th class="r">Tồn kho</th><th class="r">Tối thiểu</th>
                    <th>Tình trạng</th><th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($ingredients as $i)
                <tr class="{{ !$i->is_active ? 'is-off' : ($i->is_out ? 'is-out' : ($i->is_low ? 'is-low' : '')) }}">
                    <td class="inv-code">{{ $i->code }}</td>
                    <td><a href="{{ route('admin.inventory.ingredients.show', $i) }}"><strong>{{ $i->name }}</strong></a></td>
                    <td class="inv-muted">{{ $i->category->name ?? '—' }}</td>
                    <td class="r"><strong>{{ \App\Models\Ingredient::fmt($i->current_stock) }}</strong> {{ $i->unit }}</td>
                    <td class="r inv-muted">{{ $i->min_stock > 0 ? \App\Models\Ingredient::fmt($i->min_stock) . ' ' . $i->unit : '—' }}</td>
                    <td>
                        @if(!$i->is_active)<span class="inv-badge off">Ngừng sử dụng</span>
                        @elseif($i->is_out)<span class="inv-badge out">Hết hàng</span>
                        @elseif($i->is_low)<span class="inv-badge low">Sắp hết</span>
                        @else<span class="inv-badge ok">Đủ hàng</span>@endif
                    </td>
                    <td>
                        <div class="inv-actions">
                            <a href="{{ route('admin.inventory.ingredients.show', $i) }}" class="btn btn-sm btn-ghost">Xem</a>
                            <a href="{{ route('admin.inventory.ingredients.edit', $i) }}" class="btn btn-sm btn-ghost">Sửa</a>
                            <form method="POST" action="{{ route('admin.inventory.ingredients.destroy', $i) }}"
                                  onsubmit="return confirm('Xóa nguyên liệu &quot;{{ e($i->name) }}&quot;?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-ghost" style="color:#DC2626">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="inv-empty">
                    {{ $hasFilter ? 'Không có nguyên liệu nào khớp bộ lọc.' : 'Chưa có nguyên liệu nào. Hãy tạo nhóm nguyên liệu trước, rồi bấm "Thêm nguyên liệu".' }}
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="inv-pager">{{ $ingredients->links() }}</div>
</div>
@endsection
