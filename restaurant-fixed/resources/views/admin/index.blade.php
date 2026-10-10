@extends('layouts.admin')

@section('page_title', 'Bảng điều khiển')
@section('topbar_title', 'Bảng điều khiển')

@section('admin_content')

<div style="margin-bottom:24px;">
    <h2 style="font-size:1.3rem;font-weight:800;color:#0f172a;margin:0;">📊 Tổng quan hệ thống</h2>
    <p style="font-size:.85rem;color:#64748b;margin:4px 0 0;">Xin chào, <strong>{{ auth()->user()->name }}</strong>! Đây là tình hình hôm nay.</p>
</div>

{{-- Stats --}}
<div class="stats-row">
    <div class="stat-card">
        <div>
            <div class="stat-label">Doanh thu hôm nay</div>
            <div class="stat-value" style="color:#16a34a;">{{ number_format($totalToday ?? 0) }}đ</div>
        </div>
        <div class="stat-icon">💰</div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-label">Doanh thu tháng</div>
            <div class="stat-value" style="color:#5b3cff;">{{ number_format($totalMonth ?? 0) }}đ</div>
        </div>
        <div class="stat-icon">📈</div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-label">Tổng doanh thu</div>
            <div class="stat-value" style="color:#0f172a;">{{ number_format($totalRevenue ?? 0) }}đ</div>
        </div>
        <div class="stat-icon">🏦</div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-label">Tổng đơn hàng</div>
            <div class="stat-value">{{ $orders->count() }}</div>
        </div>
        <div class="stat-icon">📋</div>
    </div>
</div>

{{-- Truy cập nhanh --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:24px;">
    <a href="{{ route('admin.users.index') }}" style="text-decoration:none;" >
        <div style="background:#fff;border-radius:14px;padding:18px;box-shadow:0 4px 20px rgba(15,23,42,.07);border-left:4px solid #5b3cff;transition:transform .15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
            <div style="font-size:1.5rem;margin-bottom:8px;">👥</div>
            <div style="font-weight:700;color:#0f172a;font-size:.9rem;">Quản lý tài khoản</div>
            <div style="font-size:.78rem;color:#64748b;margin-top:2px;">Thêm/sửa nhân viên</div>
        </div>
    </a>
    <a href="{{ route('admin.menus.index') }}" style="text-decoration:none;">
        <div style="background:#fff;border-radius:14px;padding:18px;box-shadow:0 4px 20px rgba(15,23,42,.07);border-left:4px solid #f59e0b;transition:transform .15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
            <div style="font-size:1.5rem;margin-bottom:8px;">🍽️</div>
            <div style="font-weight:700;color:#0f172a;font-size:.9rem;">Quản lý thực đơn</div>
            <div style="font-size:.78rem;color:#64748b;margin-top:2px;">Thêm/sửa món ăn</div>
        </div>
    </a>
    <a href="{{ route('admin.tables.index') }}" style="text-decoration:none;">
        <div style="background:#fff;border-radius:14px;padding:18px;box-shadow:0 4px 20px rgba(15,23,42,.07);border-left:4px solid #10b981;transition:transform .15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
            <div style="font-size:1.5rem;margin-bottom:8px;">🪑</div>
            <div style="font-weight:700;color:#0f172a;font-size:.9rem;">Quản lý bàn</div>
            <div style="font-size:.78rem;color:#64748b;margin-top:2px;">Tình trạng bàn</div>
        </div>
    </a>
    <a href="{{ route('admin.reservations.index') }}" style="text-decoration:none;">
        <div style="background:#fff;border-radius:14px;padding:18px;box-shadow:0 4px 20px rgba(15,23,42,.07);border-left:4px solid #ef4444;transition:transform .15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
            <div style="font-size:1.5rem;margin-bottom:8px;">📅</div>
            <div style="font-weight:700;color:#0f172a;font-size:.9rem;">Đặt bàn</div>
            <div style="font-size:.78rem;color:#64748b;margin-top:2px;">Xem & xử lý đơn</div>
        </div>
    </a>
</div>

{{-- Đơn hàng gần nhất --}}
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">📋 Đơn hàng gần nhất</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Bàn</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày đặt</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders->take(15) as $order)
                <tr>
                    <td><strong style="color:#5b3cff;">#{{ $order->id }}</strong></td>
                    <td>{{ $order->user->name ?? $order->name ?? 'Khách lẻ' }}</td>
                    <td>
                        <span style="background:#f1f5f9;padding:3px 10px;border-radius:8px;font-size:.82rem;font-weight:600;">
                            Bàn: {{ $order->table_number ?? 'Mang về' }}
                        </span>
                    </td>
                    <td style="font-weight:700;color:#16a34a;">{{ number_format($order->total_price) }}đ</td>
                    <td>
    <span class="badge {{ \App\Helpers\StatusHelper::getBadgeClass($order->status) }}">
        {{ \App\Helpers\StatusHelper::getTextWithEmoji($order->status) }}
    </span>
</td>
                    <td style="color:#64748b;font-size:.83rem;">{{ $order->created_at?->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">Chưa có đơn hàng nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
