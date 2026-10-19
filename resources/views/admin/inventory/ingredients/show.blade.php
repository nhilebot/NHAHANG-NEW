@extends('layouts.admin')
@section('title', 'Chi tiết nguyên liệu')
@section('page_title', 'Chi tiết nguyên liệu')
@section('topbar_title', 'Chi tiết nguyên liệu')

@section('admin_content')
@include('admin.inventory._style')

<div class="inv-panel">
    <div class="inv-head">
        <div>
            <h2 class="inv-title">{{ $ingredient->name }}</h2>
            <p class="inv-sub">Mã {{ $ingredient->code }}</p>
        </div>
        <div class="inv-actions">
            <a href="{{ route('admin.inventory.ingredients.edit', $ingredient) }}" class="btn btn-primary">Sửa</a>
            <a href="{{ route('admin.inventory.ingredients.index') }}" class="btn btn-ghost">Quay lại</a>
        </div>
    </div>

    <dl class="inv-dl">
        <dt>Nhóm</dt><dd>{{ $ingredient->category->name ?? '—' }}</dd>
        <dt>Đơn vị tính</dt><dd>{{ $ingredient->unit }}</dd>
        <dt>Tồn kho hiện tại</dt>
        <dd>
            <strong>{{ \App\Models\Ingredient::fmt($ingredient->current_stock) }} {{ $ingredient->unit }}</strong>
            @if(!$ingredient->is_active)<span class="inv-badge off">Ngừng sử dụng</span>
            @elseif($ingredient->is_out)<span class="inv-badge out">Hết hàng</span>
            @elseif($ingredient->is_low)<span class="inv-badge low">Sắp hết</span>
            @else<span class="inv-badge ok">Đủ hàng</span>@endif
        </dd>
        <dt>Mức tồn tối thiểu</dt>
        <dd>{{ $ingredient->min_stock > 0 ? \App\Models\Ingredient::fmt($ingredient->min_stock) . ' ' . $ingredient->unit : 'Không đặt' }}</dd>
        <dt>Giá nhập tham khảo</dt>
        <dd>{{ $ingredient->reference_price !== null ? number_format($ingredient->reference_price, 0, ',', '.') . 'đ / ' . $ingredient->unit : '—' }}</dd>
        <dt>Ghi chú</dt><dd>{{ $ingredient->note ?: '—' }}</dd>
    </dl>
</div>
@endsection
