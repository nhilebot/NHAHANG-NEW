@extends('layouts.admin')

@section('page_title', 'Bảng điều khiển')
@section('topbar_title', 'Bảng điều khiển')

@section('admin_content')
@php
    // ── Số lượng tổng quan ──
    $countUsers      = \App\Models\User::count();
    $countMenus      = \App\Models\Menu::count();
    $countCategories = \App\Models\Category::count();

    // ── Món bán chạy nhất: $topDishes do AdminController::index() tính ──
    // (chỉ gồm món của đơn khách đã thanh toán phần còn lại). Nếu controller chưa cập nhật thì hiện rỗng.
    $topDishes = $topDishes ?? collect();
    $maxQty = max($topDishes->max('qty') ?? 0, 1);
@endphp
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}?v=1">
@endpush

<div class="ui-page dash-page">

    <div class="ui-head">
        <div>
            <h2 class="ui-title">📊 Tổng quan hệ thống</h2>
            <p class="ui-subtitle">Xin chào, <strong>{{ auth()->user()->name }}</strong>! Đây là tình hình hôm nay.</p>
        </div>
    </div>

    {{-- Doanh thu --}}
    <div class="stats-row">
        <div class="stat-card">
            <div>
                <div class="stat-label">Doanh thu hôm nay</div>
                <div class="stat-value stat-value--green">{{ number_format($totalToday ?? 0) }}đ</div>
            </div>
            <div class="stat-icon">💰</div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Doanh thu tháng</div>
                <div class="stat-value stat-value--gold">{{ number_format($totalMonth ?? 0) }}đ</div>
            </div>
            <div class="stat-icon">📈</div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Tổng doanh thu</div>
                <div class="stat-value stat-value--dark">{{ number_format($totalRevenue ?? 0) }}đ</div>
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

    {{-- Số lượng: tài khoản / món ăn / danh mục (bấm để vào trang quản lý) --}}
    <div class="count-row">
        <a href="{{ route('admin.users.index') }}" class="count-card count-card--users">
            <span class="count-icon">👥</span>
            <span class="count-body">
                <span class="count-value">{{ number_format($countUsers) }}</span>
                <span class="count-label">Tài khoản</span>
            </span>
        </a>
        <a href="{{ route('admin.menus.index') }}" class="count-card count-card--menus">
            <span class="count-icon">🍽️</span>
            <span class="count-body">
                <span class="count-value">{{ number_format($countMenus) }}</span>
                <span class="count-label">Món ăn</span>
            </span>
        </a>
        <a href="{{ route('admin.categories.index') }}" class="count-card count-card--cats">
            <span class="count-icon">🗂️</span>
            <span class="count-body">
                <span class="count-value">{{ number_format($countCategories) }}</span>
                <span class="count-label">Danh mục</span>
            </span>
        </a>
    </div>

    {{-- Món bán chạy nhất --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">🔥 Món bán chạy nhất</div>
            <span class="panel-note">Chỉ tính đơn khách đã thanh toán đủ</span>
        </div>

        @if($topDishes->isEmpty())
            <div class="empty-box">
                <div class="empty-icon">🍽️</div>
                Chưa có dữ liệu bán hàng.
            </div>
        @else
            <ol class="top-list">
                @foreach($topDishes as $i => $dish)
                @php
                    $img = optional($dish['menu'])->image;
                    $imgUrl = $img ? (str_contains($img, 'images/') ? asset($img) : asset('images/' . $img)) : null;
                    $pct = round($dish['qty'] / $maxQty * 100);
                @endphp
                <li class="top-item">
                    <span class="top-rank top-rank--{{ $i + 1 }}">{{ $i + 1 }}</span>
                    @if($imgUrl)
                        <img src="{{ $imgUrl }}" alt="{{ $dish['name'] }}" class="top-thumb" loading="lazy">
                    @else
                        <span class="top-thumb top-thumb--empty">🍽️</span>
                    @endif
                    <div class="top-info">
                        <div class="top-name">{{ $dish['name'] }}</div>
                        <div class="top-bar"><span style="width: {{ $pct }}%;"></span></div>
                    </div>
                    <div class="top-qty"><strong>{{ number_format($dish['qty']) }}</strong> <small>đã bán</small></div>
                </li>
                @endforeach
            </ol>
        @endif
    </div>

    {{-- Đơn hàng gần nhất --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">📋 Đơn hàng gần nhất</div>
        </div>
        <div class="table-wrap">
            <table class="ui-table">
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
                        <td data-label="Mã đơn"><strong class="order-code">#{{ $order->id }}</strong></td>
                        <td data-label="Khách hàng">{{ $order->user->name ?? $order->name ?? 'Khách lẻ' }}</td>
                        <td data-label="Bàn">
                            <span class="table-chip">Bàn: {{ $order->table_number ?? 'Mang về' }}</span>
                        </td>
                        <td data-label="Tổng tiền" class="order-total">{{ number_format($order->total_price) }}đ</td>
                        <td data-label="Trạng thái">
                            <span class="badge {{ \App\Helpers\StatusHelper::getBadgeClass($order->status) }}">
                                {{ \App\Helpers\StatusHelper::getTextWithEmoji($order->status) }}
                            </span>
                        </td>
                        <td data-label="Ngày đặt" class="cell-date">{{ $order->created_at?->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="cell-empty">Chưa có đơn hàng nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection