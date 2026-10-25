@extends('shared')

@section('title', 'Xác nhận đơn hàng - Aurora Garden')

@section('head')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/pages/tables-index.css') }}?v=1">
@endsection

@section('content')
<div class="cart-container">
    <h2 class="cart-title">ĐƠN HÀNG CỦA BẠN</h2>

    <div class="customer-info">
        <div class="info-header">Thông tin người đặt hàng</div>
        <div class="info-grid">
            <div class="info-item"><strong>Họ tên:</strong> {{ auth()->user()->name ?? 'Chưa đăng nhập' }}</div>
            <div class="info-item"><strong>Ngày đặt:</strong> {{ date('d/m/Y') }}</div>
            <div class="info-item"><strong>Email:</strong> {{ auth()->user()->email ?? '...' }}</div>
            <div class="info-item"><strong>Giờ đặt:</strong> {{ date('H:i') }}</div>
            <div class="info-item"><strong>Số điện thoại:</strong> {{ auth()->user()->phone ?? '...' }}</div>
            <div class="info-item"><strong>Trạng thái:</strong> <span style="color: #d9534f;">Đang chờ xác nhận</span></div>
        </div>
    </div>

    @if(session('cart') && count(session('cart')) > 0)
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Món ăn</th>
                    <th style="text-align: center;">Số lượng</th>
                    <th style="text-align: right;">Đơn giá</th>
                </tr>
            </thead>
            <tbody>
                @foreach(session('cart') as $id => $item)
                    <tr>
                        <td>
                            <div class="product-cell">
                                <img src="{{ asset($item['image']) }}" class="product-img" onerror="this.src='https://via.placeholder.com/55'">
                                <span class="product-name">{{ $item['name'] }}</span>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <div class="quantity-control" style="justify-content: center;">
                                <button class="qty-btn">-</button>
                                <span style="margin: 0 10px; font-weight: bold;">{{ $item['quantity'] }}</span>
                                <button class="qty-btn">+</button>
                            </div>
                        </td>
                        <td style="text-align: right; font-weight: bold;">
                            {{ number_format($item['price'], 0, ',', '.') }}₫
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cart-summary">
            <strong>Tổng cộng: </strong>
            <span class="total-amount">
                {{ number_format(collect(session('cart'))->sum(fn($i) => $i['price'] * $i['quantity']), 0, ',', '.') }} VNĐ
            </span>
        </div>

        <div class="note-section">
            <label>Ghi chú</label>
            <textarea rows="3" placeholder="Ví dụ: Ít cay, không lấy hành..."></textarea>
        </div>

        <div style="margin-top: 30px; text-align: center;">
            <button style="background: #d9534f; color: white; border: none; padding: 12px 30px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 16px;">
                XÁC NHẬN ĐẶT MÓN
            </button>
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #888;">
            <i class="fas fa-shopping-basket" style="font-size: 40px; margin-bottom: 10px;"></i>
            <p>Giỏ hàng của bạn đang trống!</p>
        </div>
    @endif
</div>
@endsection
