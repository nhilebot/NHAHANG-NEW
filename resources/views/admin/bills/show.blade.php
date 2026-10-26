@extends('layouts.admin')

@section('page_title', 'Chi tiết hóa đơn')
@section('topbar_title', 'Chi tiết hóa đơn')

@section('admin_content')

{{-- CSS của trang nằm ở public/css/admin-bill.css (gồm cả phần bản in) --}}
<link rel="stylesheet" href="{{ asset('css/admin-bill.css') }}?v=1">

<div class="bill-wrapper screen-only">

    {{-- HEADER --}}
    <div class="bill-header">
        <div>
            <h1>Hóa đơn #{{ $bill->id }}</h1>
            <p>Ngày thanh toán:
                {{ $bill->updated_at->format('d/m/Y H:i') }}
            </p>
        </div>

        {{-- Màu huy hiệu đổi theo trạng thái (class s-pending, s-paid_cash, s-cancelled...) --}}
        <div class="status-badge s-{{ $bill->status }}">
            @php
    $statusText = match($bill->status) {
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'arrived' => 'Khách đã đến',
        'serving' => 'Đang phục vụ',
        'served' => 'Đã phục vụ',
        'paid_cash' => 'Đã thanh toán tiền mặt',
        'paid_transfer' => 'Đã chuyển khoản',
        'completed' => 'Hoàn tất',
        'cancelled' => 'Đã hủy',
        default => $bill->status
    };
@endphp

{{ $statusText }}
        </div>
    </div>

    {{-- INFO --}}
    <div class="info-grid">

        <div class="info-card">
            <h3>Thông tin khách hàng</h3>

            <div class="info-item">
                <span>Họ tên</span>
                <strong>{{ $bill->user->name ?? '---' }}</strong>
            </div>

            <div class="info-item">
                <span>Email</span>
                <strong>{{ $bill->user->email ?? '---' }}</strong>
            </div>
        </div>

        <div class="info-card">
            <h3>Thông tin bàn</h3>

            <div class="info-item">
                <span>Số bàn</span>
                <strong>
                    {{ $bill->table->name ?? '---' }}
                </strong>
            </div>

            <div class="info-item">
                <span>Trạng thái</span>
               <strong>{{ $statusText }}</strong>
            </div>
        </div>

    </div>

    {{-- MÓN ĂN --}}
    <div class="food-card">

        <h3>Danh sách món ăn</h3>

        <div class="food-scroll">
        <table class="food-table">

            <thead>
                <tr>
                    <th>Món ăn</th>
                    <th class="ctr">SL</th>
                    <th class="num">Giá</th>
                    <th class="num">Thành tiền</th>
                </tr>
            </thead>

            <tbody>
                @php
                    // Đảm bảo dữ liệu cart_data luôn là mảng PHP, nếu là chuỗi JSON sẽ tự decode
                    $itemsList = $bill->cart_data;
                    if (is_string($itemsList)) {
                        $itemsList = json_decode($itemsList, true);
                    }
                @endphp

                @if(is_array($itemsList) && count($itemsList) > 0)
                    @foreach($itemsList as $item)
                    <tr>
                        {{-- Vì đọc từ mảng JSON nên ta sử dụng cú pháp thẻ mảng $item['key'] --}}
                        <td>{{ $item['name'] ?? $item['product_name'] ?? 'Món ẩn' }}</td>
                        <td class="ctr"><strong>{{ $item['quantity'] }}</strong></td>
                        <td class="num">{{ number_format($item['price'], 0, ',', '.') }}đ</td>
                        <td class="num"><strong>{{ number_format($item['quantity'] * $item['price'], 0, ',', '.') }}đ</strong></td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="food-empty">
                            Chưa có món ăn nào được ghi nhận cho hóa đơn này.
                        </td>
                    </tr>
                @endif
            </tbody>

        </table>
        </div>

    </div>

    {{-- THANH TOÁN --}}
    <div class="pay-card">
        <h3>Chi tiết thanh toán</h3>

        <div class="pay-row">
            <span>Tổng giá trị hóa đơn</span>
            <strong>{{ number_format($total, 0, ',', '.') }}đ</strong>
        </div>

        @if(($discount ?? 0) > 0)
        <div class="pay-row discount">
            <span>
                Khuyến mãi
                @if($bill->promotion_code)
                    <small>· {{ $bill->promotion_code }}</small>
                @endif
            </span>
            <strong>− {{ number_format($discount, 0, ',', '.') }}đ</strong>
        </div>
        @endif

        <div class="pay-row deposit">
            <span>
                {{-- Không ghi cố định "20%": đặt bàn không kèm món có mức cọc cố định --}}
                Đã thu trước (tiền đặt cọc)
                @if($bill->deposit_paid_at)
                    <small>· {{ $bill->deposit_paid_at->format('d/m/Y H:i') }}</small>
                @endif
            </span>
            <strong>− {{ number_format($deposit, 0, ',', '.') }}đ</strong>
        </div>

        <div class="pay-row">
            <span>
                Thanh toán cuối buổi ăn
                <small>· {{ $bill->status === 'paid_transfer' ? 'Chuyển khoản' : ($bill->status === 'paid_cash' ? 'Tiền mặt' : '') }}
                @if($bill->paid_at) {{ $bill->paid_at->format('d/m/Y H:i') }} @endif</small>
            </span>
            <strong>{{ number_format($remaining, 0, ',', '.') }}đ</strong>
        </div>
    </div>

    {{-- TỔNG TIỀN --}}
    <div class="total-card">

        <div>
            <p>Khách thanh toán cuối buổi</p>
            <h2>{{ number_format($remaining, 0, ',', '.') }}đ</h2>
            <p class="total-formula">
                = {{ number_format($total, 0, ',', '.') }}đ
                @if(($discount ?? 0) > 0) − {{ number_format($discount, 0, ',', '.') }}đ khuyến mãi @endif
                − {{ number_format($deposit, 0, ',', '.') }}đ đã cọc
            </p>
        </div>


    </div>

    {{-- ACTION --}}
    <div class="action-bar">

        <a href="{{ route('admin.bills.index') }}"
           class="btn-back">
            Quay lại danh sách
        </a>

        <button onclick="window.print()"
                class="btn-print">
            In hóa đơn
        </button>

    </div>

</div>

{{-- ===== BẢN IN CHO KHÁCH (chỉ hiện khi in) ===== --}}
@php
    $methodText = $bill->status === 'paid_transfer' ? 'Chuyển khoản' : ($bill->status === 'paid_cash' ? 'Tiền mặt' : '—');
    $paidAt = $bill->paid_at ?? $bill->updated_at;
@endphp
<div class="receipt print-only">
    <div class="r-head">
        <h1>{{ config('app.name', 'Nhà hàng') }}</h1>
        <p>Hóa đơn thanh toán</p>
        <p>Số: #{{ str_pad($bill->id, 5, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="r-meta">
        <div><span>Khách hàng:</span> {{ $bill->full_name ?? $bill->user->name ?? '---' }}</div>
        <div><span>Bàn:</span> {{ $bill->table->name ?? '---' }}</div>
        <div><span>Ngày thanh toán:</span> {{ $paidAt->format('d/m/Y H:i') }}</div>
        <div><span>Hình thức:</span> {{ $methodText }}</div>
    </div>

    <table class="r-table">
        <thead>
            <tr><th>Món</th><th class="n">SL</th><th class="n">Đơn giá</th><th class="n">Thành tiền</th></tr>
        </thead>
        <tbody>
        @foreach((is_array($itemsList) ? $itemsList : []) as $item)
            <tr>
                <td>{{ $item['name'] ?? $item['product_name'] ?? 'Món' }}</td>
                <td class="n">{{ $item['quantity'] }}</td>
                <td class="n">{{ number_format($item['price'], 0, ',', '.') }}đ</td>
                <td class="n">{{ number_format($item['quantity'] * $item['price'], 0, ',', '.') }}đ</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="r-sum">
        <div><span>Tổng cộng</span><b>{{ number_format($total, 0, ',', '.') }}đ</b></div>
        @if(($discount ?? 0) > 0)
        <div><span>Khuyến mãi{{ $bill->promotion_code ? ' (' . $bill->promotion_code . ')' : '' }}</span><b>− {{ number_format($discount, 0, ',', '.') }}đ</b></div>
        @endif
        <div><span>Đã thanh toán trước (tiền đặt cọc)</span><b>− {{ number_format($deposit, 0, ',', '.') }}đ</b></div>
        <div class="grand"><span>Còn phải thanh toán</span><b>{{ number_format($remaining, 0, ',', '.') }}đ</b></div>
    </div>

    <p class="r-foot">Cảm ơn quý khách – Hẹn gặp lại!</p>
</div>

@endsection