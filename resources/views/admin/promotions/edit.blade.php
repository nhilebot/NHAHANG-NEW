@extends('layouts.admin')

@section('page_title', 'Sửa khuyến mãi')
@section('topbar_title', 'Sửa khuyến mãi')

@section('admin_content')
<div class="promo-card">
    <h3 style="margin-bottom:6px;">✏️ Sửa mã {{ $promotion->code }}</h3>
    <p style="color:#64748b;font-size:13px;margin-bottom:20px;">Đã dùng {{ $promotion->used_count }} lượt.</p>
    <form action="{{ route('admin.promotions.update', $promotion->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.promotions._form')
        <div class="promo-actions">
            <button type="submit" class="promo-btn promo-btn-primary">Cập nhật</button>
            <a href="{{ route('admin.promotions.index') }}" class="promo-btn promo-btn-ghost">Hủy</a>
        </div>
    </form>
</div>
@endsection
