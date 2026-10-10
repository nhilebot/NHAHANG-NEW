@extends('shared')

@section('title', 'Xác nhận đơn hàng - Restaurant')

@section('head')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f9f9f9; 
            color: #333; 
            margin: 0; 
            padding: 20px; 
        }
        .cart-container { 
            max-width: 900px; 
            margin: 20px auto; 
            background-color: #fff; 
            padding: 25px; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
        }
        .cart-title { 
            text-align: center; 
            font-size: 22px; 
            font-weight: bold; 
            color: #333; 
            margin-bottom: 20px; 
        }
        /* KHU VỰC THÔNG TIN KHÁCH HÀNG (Giống ảnh mẫu) */
        .customer-info {
            border: 1px dashed #d9534f; /* Viền đứt màu đỏ */
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 25px;
            background-color: #fffafb;
        }
        .info-header {
            text-align: center;
            color: #d9534f;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
            font-size: 14px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 40px;
        }
        .info-item {
            font-size: 14px;
            line-height: 1.6;
        }
        .info-item strong {
            display: inline-block;
            width: 110px;
            color: #555;
        }
        /* BẢNG GIỎ HÀNG */
        .cart-table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .cart-table th { 
            text-align: left; 
            padding: 12px; 
            border-bottom: 2px solid #eee; 
            color: #777; 
            font-size: 13px;
            text-transform: uppercase;
        }
        .cart-table td { 
            padding: 15px 12px; 
            border-bottom: 1px solid #eee; 
            vertical-align: middle; 
        }
        /* Flexbox hiển thị ảnh và tên trên 1 hàng */
        .product-cell { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
        }
        .product-img { 
            width: 55px; 
            height: 55px; 
            object-fit: cover; 
            border-radius: 4px; 
            border: 1px solid #eee; 
        }
        .product-name { 
            font-weight: 600; 
            font-size: 14px; 
        }
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .qty-btn {
            width: 25px;
            height: 25px;
            border: 1px solid #ddd;
            background: #fff;
            cursor: pointer;
            border-radius: 3px;
        }
        /* TỔNG CỘNG */
        .cart-summary { 
            text-align: right; 
            margin-top: 20px; 
            padding-top: 15px;
            font-size: 16px;
        }
        .total-amount { 
            font-size: 20px; 
            font-weight: bold; 
            color: #000; 
        }
        .note-section {
            margin-top: 20px;
        }
        .note-section label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .note-section textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
        }
    </style>
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
