@extends('layouts.admin')

@section('page_title', 'Thanh Toán & Hóa Đơn - POS')
@section('topbar_title', 'Thanh Toán & Hóa Đơn')

@section('admin_content')

@php
    $subtotal = $subtotal ?? $order->total_price ?? 0;
    $vat = $subtotal * 0.1;
    $grandTotal = round($subtotal + $vat);

    $statusText = match($order->status ?? 'pending') {
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'arrived' => 'Khách đã đến',
        'serving' => 'Đang phục vụ',
        'served' => 'Đã phục vụ',
        'paid_cash' => 'Đã thanh toán tiền mặt',
        'paid_transfer' => 'Đã chuyển khoản',
        'completed' => 'Hoàn tất',
        'cancelled' => 'Đã hủy',
        default => 'Không xác định'
    };
@endphp

<div style="max-width:1400px; margin:auto;">

    {{-- HEADER --}}
    <div class="panel"
         style="margin-bottom:24px; display:flex; justify-content:space-between; align-items:center;">

        <div>
            <h2 style="margin:0 0 10px; font-size:30px; color:#0f172a;">
                🧾 Hóa Đơn #{{ $order->id }}
            </h2>

            <p style="margin:0; color:#64748b;">
                <strong>Bàn:</strong>
                {{ $bill->table->name ?? $bill->table->table_number ?? '---' }}

                |

                <strong>Thời gian:</strong>
                {{ $order->created_at?->format('H:i - d/m/Y') }}
            </p>
        </div>

        <div style="
            background:#10b981;
            color:white;
            padding:12px 22px;
            border-radius:999px;
            font-weight:700;
        ">
            {{ $statusText }}
        </div>
    </div>

    {{-- MAIN --}}
    <div style="display:grid; grid-template-columns:60% 1fr; gap:24px;">

        {{-- LEFT --}}
        <div>

            <div class="panel">

                <h3 style="
                    margin-top:0;
                    margin-bottom:20px;
                    color:#0f172a;
                    font-size:22px;
                ">
                    🍽️ Chi Tiết Món Ăn
                </h3>

                <div style="overflow-x:auto;">

                    <table style="
                        width:100%;
                        border-collapse:collapse;
                    ">

                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="padding:14px; text-align:left;">Tên món</th>
                                <th style="padding:14px; text-align:center;">SL</th>
                                <th style="padding:14px; text-align:right;">Đơn giá</th>
                                <th style="padding:14px; text-align:right;">Thành tiền</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($order->items as $item)

                                <tr style="border-top:1px solid #e2e8f0;">

                                    <td style="padding:16px;">

                                        <div style="font-weight:700; color:#0f172a;">
                                            {{ $item->menu->name ?? 'Không có tên món' }}
                                        </div>

                                        @if($item->note)
                                            <div style="
                                                margin-top:5px;
                                                font-size:13px;
                                                color:#64748b;
                                            ">
                                                📝 {{ $item->note }}
                                            </div>
                                        @endif

                                    </td>

                                    <td style="
                                        text-align:center;
                                        font-weight:700;
                                    ">
                                        {{ $item->quantity }}
                                    </td>

                                    <td style="text-align:right;">
                                        {{ number_format($item->price) }}đ
                                    </td>

                                    <td style="
                                        text-align:right;
                                        font-weight:800;
                                        color:#5b3cff;
                                    ">
                                        {{ number_format($item->price * $item->quantity) }}đ
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4"
                                        style="
                                            padding:30px;
                                            text-align:center;
                                            color:#94a3b8;
                                        ">
                                        Không có món ăn
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                {{-- TOTAL --}}
                <div style="
                    margin-top:24px;
                    background:#f8fafc;
                    padding:22px;
                    border-radius:16px;
                ">

                    <div style="
                        display:flex;
                        justify-content:space-between;
                        margin-bottom:12px;
                    ">
                        <span>Tạm tính</span>

                        <strong>
                            {{ number_format($subtotal) }}đ
                        </strong>
                    </div>

                    <div style="
                        display:flex;
                        justify-content:space-between;
                        margin-bottom:12px;
                    ">
                        <span>VAT (10%)</span>

                        <strong>
                            {{ number_format($vat) }}đ
                        </strong>
                    </div>

                    <div style="
                        display:flex;
                        justify-content:space-between;
                        margin-top:18px;
                        padding-top:18px;
                        border-top:2px dashed #cbd5e1;
                    ">
                        <span style="
                            font-size:22px;
                            font-weight:800;
                        ">
                            💰 Tổng cộng
                        </span>

                        <span style="
                            font-size:30px;
                            font-weight:900;
                            color:#5b3cff;
                        ">
                            {{ number_format($grandTotal) }}đ
                        </span>
                    </div>

                </div>

            </div>

        </div>

        {{-- RIGHT --}}
        <div>

            <div class="panel">

                <h3 style="
                    margin-top:0;
                    margin-bottom:20px;
                    color:#0f172a;
                    font-size:22px;
                ">
                    👤 Thông Tin Khách
                </h3>

                <div style="
                    background:#f8fafc;
                    padding:18px;
                    border-radius:14px;
                    margin-bottom:24px;
                ">

                    <div style="margin-bottom:14px;">
                        <div style="
                            color:#64748b;
                            font-size:14px;
                            margin-bottom:4px;
                        ">
                            Họ tên
                        </div>

                        <strong style="font-size:18px;">
                            {{ $order->user->name ?? 'Khách lẻ' }}
                        </strong>
                    </div>

                    <div>
                        <div style="
                            color:#64748b;
                            font-size:14px;
                            margin-bottom:4px;
                        ">
                            Số điện thoại
                        </div>

                        <strong>
                            {{ $order->user->phone ?? 'Không có' }}
                        </strong>
                    </div>

                </div>

                {{-- PAYMENT --}}
                <div style="margin-bottom:24px;">

                    <label style="
                        display:block;
                        margin-bottom:14px;
                        font-weight:700;
                        color:#5b3cff;
                    ">
                        💳 Phương thức thanh toán
                    </label>

                    <div style="display:flex; gap:16px;">

                        <label>
                            <input type="radio"
                                   checked
                                   name="payment_method"
                                   value="cash"
                                   onchange="togglePaymentMethod(this.value)">

                            💵 Tiền mặt
                        </label>

                        <label>
                            <input type="radio"
                                   name="payment_method"
                                   value="transfer"
                                   onchange="togglePaymentMethod(this.value)">

                            📲 Chuyển khoản
                        </label>

                    </div>

                </div>

                {{-- CASH --}}
                <div id="cashSection"
                     style="
                        background:#fef3c7;
                        border:1px solid #fcd34d;
                        padding:18px;
                        border-radius:14px;
                        margin-bottom:20px;
                     ">

                    <label style="
                        display:block;
                        margin-bottom:10px;
                        font-weight:700;
                    ">
                        💰 Tiền khách đưa
                    </label>

                    <input type="number"
                           id="customerMoney"
                           oninput="calculateChange()"
                           placeholder="Nhập số tiền"
                           style="
                                width:100%;
                                padding:12px;
                                border-radius:10px;
                                border:1px solid #fcd34d;
                                margin-bottom:16px;
                           ">

                    <div style="
                        background:white;
                        padding:14px;
                        border-radius:12px;
                    ">

                        <div style="
                            display:flex;
                            justify-content:space-between;
                            margin-bottom:10px;
                        ">
                            <span>Cần thanh toán</span>

                            <strong>
                                {{ number_format($grandTotal) }}đ
                            </strong>
                        </div>

                        <div style="
                            display:flex;
                            justify-content:space-between;
                            border-top:1px solid #e2e8f0;
                            padding-top:10px;
                        ">
                            <span style="font-weight:700;">
                                Tiền thối lại
                            </span>

                            <strong id="changeAmount"
                                    style="
                                        font-size:22px;
                                        color:#16a34a;
                                    ">
                                0đ
                            </strong>
                        </div>

                    </div>

                </div>

                {{-- QR --}}
                <div id="qrSection"
                     style="
                        display:none;
                        text-align:center;
                        background:#ecfdf5;
                        border:1px solid #86efac;
                        padding:18px;
                        border-radius:14px;
                        margin-bottom:20px;
                     ">

                    <p style="
                        margin-top:0;
                        font-weight:700;
                        color:#15803d;
                    ">
                        📲 Quét QR để thanh toán
                    </p>

                    <img
                        src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=PAYMENT_{{ $order->id }}"
                        style="
                            width:220px;
                            border-radius:12px;
                        "
                    >

                </div>

                {{-- BUTTON --}}
                <div style="
                    display:grid;
                    grid-template-columns:1fr 1fr;
                    gap:12px;
                ">

                    <form method="POST"
                          action="{{ route('admin.orders.pay', $order->id) }}">

                        @csrf

                        <input type="hidden"
                               name="payment_method"
                               id="paymentMethodInput"
                               value="cash">

                        <input type="hidden"
                               name="amount_received"
                               id="amountReceivedInput"
                               value="0">

                        <button type="submit"
                                style="
                                    width:100%;
                                    border:none;
                                    padding:14px;
                                    border-radius:12px;
                                    background:#5b3cff;
                                    color:white;
                                    font-weight:800;
                                    cursor:pointer;
                                ">
                            ✅ Xác nhận
                        </button>

                    </form>

                    <button onclick="printBill()"
                            style="
                                border:none;
                                padding:14px;
                                border-radius:12px;
                                background:#f59e0b;
                                color:white;
                                font-weight:800;
                                cursor:pointer;
                            ">
                        🖨️ In hóa đơn
                    </button>

                </div>

                <button onclick="history.back()"
                        style="
                            width:100%;
                            margin-top:12px;
                            border:none;
                            padding:12px;
                            border-radius:12px;
                            background:#e2e8f0;
                            font-weight:700;
                            cursor:pointer;
                        ">
                    ← Quay lại
                </button>

            </div>

        </div>

    </div>

</div>

<script>

const GRAND_TOTAL = {{ $grandTotal }};

function togglePaymentMethod(method)
{
    const cash = document.getElementById('cashSection');
    const qr = document.getElementById('qrSection');

    document.getElementById('paymentMethodInput').value = method;

    if(method === 'cash'){
        cash.style.display = 'block';
        qr.style.display = 'none';
    }else{
        cash.style.display = 'none';
        qr.style.display = 'block';
    }
}

function calculateChange()
{
    const money = parseFloat(
        document.getElementById('customerMoney').value
    ) || 0;

    const change = money - GRAND_TOTAL;

    const el = document.getElementById('changeAmount');

    if(change < 0){
        el.style.color = '#dc2626';
        el.innerHTML = 'Thiếu ' +
            Math.abs(change).toLocaleString('vi-VN') + 'đ';
    }else{
        el.style.color = '#16a34a';
        el.innerHTML =
            change.toLocaleString('vi-VN') + 'đ';
    }

    document.getElementById('amountReceivedInput').value = money;
}

function printBill()
{
    window.print();
}

</script>

@endsection