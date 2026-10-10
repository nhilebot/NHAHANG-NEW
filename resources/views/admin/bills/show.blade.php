@extends('layouts.admin')

@section('page_title', 'Chi tiết hóa đơn')
@section('topbar_title', 'Chi tiết hóa đơn')

@section('admin_content')

<div class="bill-wrapper screen-only">

    {{-- HEADER --}}
    <div class="bill-header">
        <div>
            <h1>🧾 Hóa đơn #{{ $bill->id }}</h1>
            <p>Ngày thanh toán:
                {{ $bill->updated_at->format('d/m/Y H:i') }}
            </p>
        </div>

        <div class="status-badge">
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
            <h3>👤 Thông tin khách hàng</h3>

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
            <h3>🪑 Thông tin bàn</h3>

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

    <h3>🍽 Danh sách món ăn</h3>

    <table class="food-table">

        <thead>
            <tr>
                <th>Món ăn</th>
                <th>SL</th>
                <th>Giá</th>
                <th>Thành tiền</th>
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
            <td><strong>{{ $item['quantity'] }}</strong></td>
            <td>{{ number_format($item['price']) }}đ</td>
            <td>{{ number_format($item['quantity'] * $item['price']) }}đ</td>
        </tr>
        @endforeach
    @else
        <tr>
            <td colspan="4" style="text-align:center; color: #94a3b8; padding: 20px;">
                🛒 Chưa có món ăn nào được ghi nhận cho hóa đơn này.
            </td>
        </tr>
    @endif
</tbody>

    </table>

</div>
    {{-- THANH TOÁN --}}
    <div class="pay-card">
        <h3>💳 Chi tiết thanh toán</h3>

        <div class="pay-row">
            <span>Tổng giá trị hóa đơn</span>
            <strong>{{ number_format($total) }}đ</strong>
        </div>

        <div class="pay-row deposit">
            <span>
                Đã thu trước (tiền cọc 20%)
                @if($bill->deposit_paid_at)
                    <small>· {{ $bill->deposit_paid_at->format('d/m/Y H:i') }}</small>
                @endif
            </span>
            <strong>− {{ number_format($deposit) }}đ</strong>
        </div>

        <div class="pay-row">
            <span>
                Thanh toán cuối buổi ăn
                <small>· {{ $bill->status === 'paid_transfer' ? 'Chuyển khoản' : ($bill->status === 'paid_cash' ? 'Tiền mặt' : '') }}
                @if($bill->paid_at) {{ $bill->paid_at->format('d/m/Y H:i') }} @endif</small>
            </span>
            <strong>{{ number_format($remaining) }}đ</strong>
        </div>
    </div>

    {{-- TỔNG TIỀN --}}
    <div class="total-card">

        <div>
            <p>Khách thanh toán cuối buổi</p>
            <h2>{{ number_format($remaining) }}đ</h2>
            <p style="font-size:13px;margin-top:8px">
                = {{ number_format($total) }}đ − {{ number_format($deposit) }}đ đã cọc
            </p>
        </div>

        <div class="money-icon">
            💰
        </div>

    </div>

    {{-- ACTION --}}
    <div class="action-bar">

        <a href="{{ route('admin.bills.index') }}"
           class="btn-back">
            ← Quay lại danh sách
        </a>

        <button onclick="window.print()"
                class="btn-print">
            🖨️ In hóa đơn
        </button>

    </div>

</div>

<style>

.bill-wrapper{
    max-width:1000px;
    margin:auto;
}

/* HEADER */

.bill-header{
    background:#fff;
    border-radius:20px;
    padding:30px;
    margin-bottom:25px;

    display:flex;
    justify-content:space-between;
    align-items:center;

    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}

.bill-header h1{
    margin:0;
    font-size:32px;
    color:#1e293b;
}

.bill-header p{
    margin-top:8px;
    color:#64748b;
}

.status-badge{
    background:#10b981;
    color:white;
    padding:12px 22px;
    border-radius:999px;
    font-weight:700;
    letter-spacing:1px;
}

/* GRID */

.info-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:24px;
    margin-bottom:25px;
}

.info-card{
    background:#fff;
    border-radius:20px;
    padding:25px;

    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}

.info-card h3{
    margin-top:0;
    margin-bottom:20px;
    color:#1e293b;
}

.info-item{
    display:flex;
    justify-content:space-between;
    padding:14px 0;
    border-bottom:1px solid #f1f5f9;
}

.info-item span{
    color:#64748b;
}

/* TOTAL */

.total-card{
    background:linear-gradient(135deg,#0f172a,#1e293b);
    color:white;

    border-radius:24px;
    padding:35px;

    display:flex;
    justify-content:space-between;
    align-items:center;

    margin-bottom:25px;
}

.total-card p{
    margin:0;
    opacity:0.8;
}

.total-card h2{
    margin-top:10px;
    font-size:42px;
}

.money-icon{
    font-size:60px;
}

/* ACTION */

.action-bar{
    display:flex;
    gap:16px;
}

.btn-back,
.btn-print{
    border:none;
    padding:14px 24px;
    border-radius:14px;
    font-weight:700;
    cursor:pointer;
    text-decoration:none;
    transition:0.3s;
}

.btn-back{
    background:#e2e8f0;
    color:#1e293b;
}

.btn-back:hover{
    background:#cbd5e1;
}

.btn-print{
    background:#d4af37;
    color:white;
}

.btn-print:hover{
    background:#b89020;
}

/* RESPONSIVE */

@media(max-width:768px){

    .info-grid{
        grid-template-columns:1fr;
    }

    .bill-header{
        flex-direction:column;
        gap:20px;
        align-items:flex-start;
    }

    .total-card{
        flex-direction:column;
        align-items:flex-start;
        gap:20px;
    }

}
/* PAY */
.pay-card{background:#fff;border-radius:20px;padding:25px;margin-bottom:25px;box-shadow:0 10px 30px rgba(0,0,0,0.05)}
.pay-card h3{margin-top:0;margin-bottom:10px}
.pay-row{display:flex;justify-content:space-between;padding:14px 0;border-bottom:1px solid #f1f5f9}
.pay-row:last-child{border-bottom:none}
.pay-row small{color:#94a3b8;font-weight:400}
.pay-row.deposit strong{color:#10b981}
@media print{.action-bar{display:none}}
/* FOOD */

.food-card{
    background:#fff;
    border-radius:20px;
    padding:25px;
    margin-bottom:25px;

    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}

.food-card h3{
    margin-top:0;
    margin-bottom:20px;
}

.food-table{
    width:100%;
    border-collapse:collapse;
}

.food-table th,
.food-table td{
    padding:14px;
    border-bottom:1px solid #eee;
    text-align:left;
}
</style>

{{-- ===== BẢN IN CHO KHÁCH (chỉ hiện khi in) ===== --}}
@php
    $methodText = $bill->status === 'paid_transfer' ? 'Chuyển khoản' : ($bill->status === 'paid_cash' ? 'Tiền mặt' : '—');
    $paidAt = $bill->paid_at ?? $bill->updated_at;
@endphp
<div class="receipt print-only">
    <div class="r-head">
        <h1>{{ config('app.name', 'Nhà hàng') }}</h1>
        <p>HÓA ĐƠN THANH TOÁN</p>
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
                <td class="n">{{ number_format($item['price']) }}đ</td>
                <td class="n">{{ number_format($item['quantity'] * $item['price']) }}đ</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="r-sum">
        <div><span>Tổng cộng</span><b>{{ number_format($total) }}đ</b></div>
        <div><span>Đã thanh toán trước (cọc 20%)</span><b>− {{ number_format($deposit) }}đ</b></div>
        <div class="grand"><span>CÒN PHẢI THANH TOÁN</span><b>{{ number_format($remaining) }}đ</b></div>
    </div>

    <p class="r-foot">Cảm ơn quý khách – Hẹn gặp lại!</p>
</div>

<style>
.print-only{display:none}
.receipt{font-family:"Courier New",monospace;color:#000;max-width:380px;margin:0 auto}
.r-head{text-align:center;border-bottom:1px dashed #000;padding-bottom:10px;margin-bottom:10px}
.r-head h1{font-size:20px;margin:0 0 4px}
.r-head p{margin:2px 0;font-size:13px}
.r-meta div{font-size:13px;margin:3px 0}
.r-meta span{display:inline-block;min-width:120px}
.r-table{width:100%;border-collapse:collapse;margin:12px 0;font-size:13px}
.r-table th,.r-table td{padding:5px 2px;border-bottom:1px dashed #999;text-align:left}
.r-table .n{text-align:right}
.r-sum div{display:flex;justify-content:space-between;font-size:14px;margin:6px 0}
.r-sum .grand{border-top:2px solid #000;padding-top:8px;font-size:16px}
.r-foot{text-align:center;margin-top:18px;font-size:13px;border-top:1px dashed #000;padding-top:10px}
@media print{
    body{background:#fff !important}
    .sidebar,.topbar,.screen-only{display:none !important}
    .main{margin:0 !important;padding:0 !important;width:100% !important}
    .content{padding:0 !important}
    .print-only{display:block !important}
    @page{margin:10mm}
}
</style>

@endsection