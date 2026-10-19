@extends('layouts.admin')
@section('title', 'Chi tiết nhà cung cấp')
@section('page_title', 'Chi tiết nhà cung cấp')
@section('topbar_title', 'Chi tiết nhà cung cấp')

@section('admin_content')
@include('admin.inventory._style')

<div class="inv-panel">
    <div class="inv-head">
        <div>
            <h2 class="inv-title">{{ $supplier->name }}</h2>
            <p class="inv-sub">Mã {{ $supplier->code }}</p>
        </div>
        <div class="inv-actions">
            <a href="{{ route('admin.inventory.suppliers.edit', $supplier) }}" class="btn btn-primary">Sửa</a>
            <a href="{{ route('admin.inventory.suppliers.index') }}" class="btn btn-ghost">Quay lại</a>
        </div>
    </div>

    <dl class="inv-dl">
        <dt>Trạng thái</dt>
        <dd>
            @if($supplier->is_active)<span class="inv-badge ok">Đang hợp tác</span>
            @else<span class="inv-badge off">Ngừng hợp tác</span>@endif
        </dd>
        <dt>Điện thoại</dt><dd>{{ $supplier->phone ?: '—' }}</dd>
        <dt>Email</dt><dd>{{ $supplier->email ?: '—' }}</dd>
        <dt>Địa chỉ</dt><dd>{{ $supplier->address ?: '—' }}</dd>
        <dt>Ghi chú</dt><dd>{!! nl2br(e($supplier->note ?: '—')) !!}</dd>
    </dl>
</div>
@endsection
