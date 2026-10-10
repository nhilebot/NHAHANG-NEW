@extends('layouts.admin')

@section('admin_content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap');

    .detail-container {
        background: #fff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        max-width: 800px;
        margin: 40px auto;
        border-top: 5px solid #D4AF37;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .detail-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .detail-title {
        color: #1A2228;
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .detail-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-card {
        background: #F7FAFC;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid #D4AF37;
    }

    .info-label {
        font-weight: 600;
        color: #4A5568;
        margin-bottom: 5px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 16px;
        color: #1A2228;
        font-weight: 500;
    }

    .status-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-empty { background-color: #C6F6D5; color: #22543D; border: 1px solid #9AE6B4; }
    .status-serving { background-color: #FED7D7; color: #742A2A; border: 1px solid #FEB2B2; }
    .status-reserved { background-color: #BEE3F8; color: #2A4365; border: 1px solid #90CDF4; }

    .btn-back {
        background: #6C757D;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 12px 25px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s;
    }

    .btn-back:hover {
        background: #5A6268;
        color: white;
        transform: translateY(-2px);
    }

    .btn-edit {
        background: #D4AF37;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 12px 25px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        margin-left: 10px;
        transition: all 0.3s;
    }

    .btn-edit:hover {
        background: #B8951A;
        color: white;
        transform: translateY(-2px);
    }
</style>

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
            <div class="info-value">{{ $table->created_at->format('d/m/Y H:i') }}</div>
        </div>

        <div class="info-card">
            <div class="info-label">Cập nhật lần cuối</div>
            <div class="info-value">{{ $table->updated_at->format('d/m/Y H:i') }}</div>
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