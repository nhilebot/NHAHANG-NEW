@extends('layouts.admin')

@section('page_title', 'Thêm khuyến mãi')
@section('topbar_title', 'Thêm khuyến mãi')

@section('admin_content')
<div class="promo-card">
    <h3 style="margin-bottom:20px;">🎟️ Tạo mã khuyến mãi</h3>
    <form action="{{ route('admin.promotions.store') }}" method="POST">
        @csrf
        @include('admin.promotions._form')
        <div class="promo-actions">
            <button type="submit" class="promo-btn promo-btn-primary">Lưu mã</button>
            <a href="{{ route('admin.promotions.index') }}" class="promo-btn promo-btn-ghost">Hủy</a>
        </div>
    </form>
</div>
@endsection
