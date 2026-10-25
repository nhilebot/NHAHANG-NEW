@extends('layouts.admin')

@section('admin_content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/tables-show.css') }}?v=1">
@endpush

<div class="detail-container">
    <div class="detail-header">
        <h2 class="detail-title">Chi Tiết Bàn</h2>
        <p class="text-muted">ID: {{ $table->id }}</p>
    </div>

    <div class="detail-info">
        <div class="info-card">
            <div class="info-label">Tên bàn</div>
            <div class="info-value">{{ $table->name }}</div>
        </div>

        <div class="info-card">
            <div class="info-label">Trạng thái</div>
            <div class="info-value">
                @if($table->status == 'empty')
                    <span class="status-badge status-empty">Trống</span>
                @elseif($table->status == 'serving')
                    <span class="status-badge status-serving">Đang phục vụ</span>
                @elseif($table->status == 'reserved')
                    <span class="status-badge status-reserved">Đã đặt</span>
                @endif
            </div>
        </div>

        <div class="info-card">
            <div class="info-label">Ngày tạo</div>
            <div class="info-value">{{ optional($table->created_at)->format('d/m/Y H:i') }}</div>
        </div>

        <div class="info-card">
            <div class="info-label">Cập nhật lần cuối</div>
            <div class="info-value">{{ optional($table->updated_at)->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('admin.tables.index') }}" class="btn-back">
            <i class="fa fa-arrow-left mr-2"></i> Quay lại danh sách
        </a>
        <a href="{{ route('admin.tables.edit', $table->id) }}" class="btn-edit">
            <i class="fa fa-edit mr-2"></i> Chỉnh sửa
        </a>
    </div>
</div>
@endsection