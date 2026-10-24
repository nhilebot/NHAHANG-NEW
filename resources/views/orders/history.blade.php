@extends('shared')

@section('title', 'Lịch sử giao dịch')

@section('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

    body {
        background: #F9F8F6;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .history-container {
        max-width: 900px;
        margin: 60px auto;
        padding: 0 20px 50px;
    }

    .title-page {
        font-family: 'Playfair Display', serif;
        color: #1A2228;
        font-size: 32px;
        text-align: center;
        margin: 100px 0 15px;
        font-weight: 700;
        letter-spacing: 1.5px;
    }

    .title-page::after {
        content: '';
        display: block;
        width: 60px;
        height: 3px;
        background: #D4AF37;
        margin: 15px auto 0;
    }

    .history-subtitle {
        text-align: center;
        color: #718096;
        font-size: 14px;
        margin-bottom: 30px;
    }

    .flash-message {
        padding: 12px 16px;
        margin: 15px 0;
        border-radius: 8px;
    }

    .flash-success {
        background: #e8f7ed;
        color: #216e39;
    }

    .flash-error {
        background: #fdecec;
        color: #a12626;
    }

    .order-tabs {
        display: flex;
        gap: 6px;
        background: #fff;
        border-radius: 12px;
        padding: 8px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
        border: 1px solid #EAEAEA;
    }

    .tab-item {
        padding: 13px 8px;
        cursor: pointer;
        font-weight: 600;
        color: #718096;
        transition: all 0.3s ease;
        flex: 1;
        text-align: center;
        border-radius: 8px;
        font-size: 14px;
    }

    .tab-item:hover {
        color: #1A2228;
        background: #F7FAFC;
    }

    .tab-item.active {
        background: #1A2228;
        color: #D4AF37;
        box-shadow: 0 4px 15px rgba(26, 34, 40, 0.2);
    }

    .transaction-list {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .order-block {
        background: #fff;
        border-radius: 12px;
        margin-bottom: 0;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.04);
        border: 1px solid #EAEAEA;
        overflow: hidden;
        transition: box-shadow 0.3s, border-color 0.3s;
    }

    .order-block:hover {
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
        border-color: #D4AF37;
    }

    .order-header {
        padding: 18px 22px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        background: #FCFBF8;
        border-bottom: 1px solid #EAEAEA;
    }

    .order-id {
        font-weight: 700;
        color: #1A2228;
        font-size: 15px;
        letter-spacing: 0.4px;
    }

    .order-id i {
        color: #D4AF37;
        margin-right: 7px;
    }

    .transaction-type {
        display: inline-block;
        margin-right: 8px;
        color: #8A6D16;
        background: #FFF4D6;
        border-radius: 20px;
        padding: 4px 9px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.4px;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .status-pending,
    .status-pending_deposit {
        background: #fff4e5;
        color: #664d03;
        border: 1px solid #ffecb5;
    }

    .status-confirmed {
        background: #e7f6f8;
        color: #087990;
        border: 1px solid #cff4fc;
    }

    .status-arrived,
    .status-serving,
    .status-served,
    .status-ready {
        background: #fff9db;
        color: #856404;
        border: 1px solid #ffeeba;
    }

    .status-paid_cash,
    .status-paid_transfer,
    .status-completed {
        background: #e9f7ef;
        color: #198754;
        border: 1px solid #badbcc;
    }

    .status-cancelled {
        background: #fdf2f2;
        color: #dc3545;
        border: 1px solid #f8d7da;
    }

    .transaction-content {
        padding: 20px 22px;
    }

    .transaction-info {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px 20px;
    }

    .info-item {
        color: #718096;
        font-size: 13px;
        line-height: 1.7;
        overflow-wrap: anywhere;
    }

    .info-item strong {
        color: #1A2228;
        display: block;
        margin-bottom: 3px;
        font-size: 13px;
    }

    .info-item strong i {
        color: #D4AF37;
        width: 18px;
        margin-right: 4px;
    }

    .product-list {
        padding: 8px 0;
    }

    .product-row {
        display: flex;
        padding: 14px 22px;
        align-items: center;
        border-bottom: 1px dashed #F0F0F0;
        gap: 14px;
    }

    .product-row:last-child {
        border-bottom: none;
    }

    .product-img {
        width: 64px;
        height: 64px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
        flex-shrink: 0;
    }

    .product-detail {
        flex: 1;
        min-width: 0;
    }

    .product-name {
        font-weight: 700;
        font-size: 14px;
        color: #1A2228;
        margin-bottom: 6px;
        overflow-wrap: anywhere;
    }

    .product-qty {
        color: #718096;
        font-size: 12px;
        font-weight: 500;
        background: #F7FAFC;
        padding: 4px 9px;
        border-radius: 20px;
        display: inline-block;
    }

    .product-price {
        font-weight: 700;
        color: #1A2228;
        font-size: 14px;
        white-space: nowrap;
    }

    .order-footer {
        padding: 18px 22px;
        border-top: 1px solid #EAEAEA;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        background: #fff;
    }

    .total-label {
        color: #718096;
        font-size: 12px;
        font-weight: 600;
        margin-right: 8px;
    }

    .total-amount {
        font-size: 20px;
        font-weight: 700;
        color: #B28B20;
        font-family: 'Playfair Display', serif;
    }

    .transaction-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-detail,
    .btn-cancel,
    .btn-request-cancel {
        padding: 9px 14px;
        border-radius: 7px;
        font-weight: 600;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-detail {
        background: transparent;
        color: #1A2228;
        border: 1px solid #D8D8D8;
    }

    .btn-detail:hover {
        background: #1A2228;
        color: #D4AF37;
        border-color: #1A2228;
    }

    .btn-cancel {
        background: #dc3545;
        color: #fff;
        border: 1px solid #dc3545;
    }

    .btn-cancel:hover {
        background: #c82333;
    }

    .btn-request-cancel {
        background: #1A2228;
        color: #D4AF37;
        border: 1px solid #1A2228;
    }

    .btn-request-cancel:hover {
        background: #303B43;
    }

    .detail-box {
        padding: 22px;
        background: #FCFBF8;
        border-top: 1px solid #EAEAEA;
    }

    .detail-box p {
        margin-bottom: 12px;
        font-size: 13px;
        color: #4A5568;
        overflow-wrap: anywhere;
    }

    .detail-box strong {
        color: #1A2228;
        font-weight: 600;
    }

    .detail-box i {
        color: #D4AF37;
        width: 20px;
        text-align: center;
        margin-right: 5px;
    }

    .cancellation-message {
        margin-top: 15px;
        padding: 11px 13px;
        border-radius: 8px;
        font-size: 13px;
        line-height: 1.6;
    }

    .cancellation-pending {
        color: #856404;
        background: #fff9db;
    }

    .cancellation-approved {
        color: #216e39;
        background: #e8f7ed;
    }

    .cancellation-rejected {
        color: #a12626;
        background: #fdecec;
    }

    .cancel-form {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px dashed #EAEAEA;
    }

    .cancel-form label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #1A2228;
    }

    .cancel-form textarea {
        display: block;
        width: 100%;
        box-sizing: border-box;
        margin-bottom: 12px;
        padding: 11px;
        border: 1px solid #ddd;
        border-radius: 7px;
        font-family: inherit;
        resize: vertical;
    }

    .cancel-note {
        font-size: 12px;
        line-height: 1.6;
        color: #718096;
        margin-top: 10px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border: 1px solid #EAEAEA;
        border-radius: 12px;
    }

    .empty-state i {
        font-size: 45px;
        color: #D4AF37;
        margin-bottom: 18px;
        opacity: 0.7;
    }

    .empty-state p {
        color: #718096;
        font-weight: 500;
        font-size: 14px;
        margin: 0;
    }

    @media (max-width: 600px) {
        .history-container {
            margin-top: 25px;
            padding: 0 12px 35px;
        }

        .title-page {
            font-size: 26px;
            margin-top: 85px;
            margin-bottom: 12px;
        }

        .history-subtitle {
            margin-bottom: 22px;
        }

        .order-tabs {
            gap: 4px;
            padding: 5px;
            margin-bottom: 18px;
        }

        .tab-item {
            padding: 11px 4px;
            font-size: 12px;
        }

        .order-header,
        .transaction-content,
        .order-footer {
            padding: 15px;
        }

        .transaction-info {
            grid-template-columns: 1fr;
            gap: 11px;
        }

        .product-row {
            padding: 12px 15px;
            gap: 10px;
        }

        .product-img {
            width: 54px;
            height: 54px;
        }

        .product-name {
            font-size: 13px;
        }

        .product-price {
            font-size: 12px;
        }

        .order-footer {
            align-items: flex-start;
            flex-direction: column;
        }

        .transaction-actions {
            width: 100%;
        }

        .transaction-actions button {
            flex: 1;
        }

        .total-amount {
            font-size: 18px;
        }
    }
</style>
@endsection

@section('content')
<div class="history-container">
    <h2 class="title-page">Lịch sử giao dịch</h2>

    <p class="history-subtitle">
        Theo dõi các lượt đặt bàn và đơn hàng của bạn tại Nhà hàng Việt.
    </p>

    {{-- THÔNG BÁO --}}
    @if(session('success'))
        <div class="flash-message flash-success">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="flash-message flash-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- BỘ LỌC CHUNG --}}
    <div class="order-tabs">
        <div class="tab-item active" onclick="filterTransactions('all', this)">
            <i class="fa-solid fa-layer-group"></i> Tất cả
        </div>
        <div class="tab-item" onclick="filterTransactions('reservation', this)">
            <i class="fa-solid fa-calendar-check"></i> Đặt bàn
        </div>
        <div class="tab-item" onclick="filterTransactions('order', this)">
            <i class="fa-solid fa-receipt"></i> Đơn hàng
        </div>
    </div>

    <div id="transaction-wrapper" class="transaction-list">

        {{-- LỊCH SỬ ĐẶT BÀN --}}
        @forelse($reservations as $reservation)
            @php
                $reservationDateTime = \Carbon\Carbon::parse(
                    $reservation->reservation_date->format('Y-m-d') . ' ' .
                    $reservation->reservation_time
                );

                $canRequestCancellation =
                    in_array($reservation->status, ['pending', 'confirmed'], true)
                    && $reservation->payment_status === 'deposit_paid'
                    && !empty($reservation->deposit_paid_at)
                    && now()->lt($reservationDateTime->copy()->subHours(2))
                    && !in_array($reservation->cancellation_status, ['pending', 'approved'], true);

                $reservationStatusText = match ($reservation->status) {
                    'pending_deposit' => 'Chờ xác nhận cọc',
                    'pending' => 'Chờ xác nhận đặt bàn',
                    'confirmed' => 'Đã xác nhận',
                    'arrived' => 'Khách đã đến',
                    'serving' => 'Đang phục vụ',
                    'ready' => 'Món ăn đã sẵn sàng',
                    'served' => 'Đã phục vụ',
                    'completed' => 'Hoàn tất',
                    'cancelled' => 'Đã hủy',
                    default => $reservation->status,
                };

                $reservationStatusClass = match ($reservation->status) {
                    'pending_deposit', 'pending' => 'status-pending',
                    'confirmed' => 'status-confirmed',
                    'arrived', 'serving', 'ready', 'served' => 'status-serving',
                    'completed' => 'status-completed',
                    'cancelled' => 'status-cancelled',
                    default => 'status-pending',
                };
            @endphp

            <div class="order-block transaction-item" data-type="reservation">
                <div class="order-header">
                    <span class="order-id">
                        <i class="fa-solid fa-calendar-check"></i>
                        ĐẶT BÀN {{ $reservation->code ?? ('#' . $reservation->id) }}
                    </span>

                    <span class="status-badge {{ $reservationStatusClass }}">
                        {{ $reservationStatusText }}
                    </span>
                </div>

                <div class="transaction-content">
                    <div class="transaction-info">
                        <div class="info-item">
                            <strong><i class="fa-solid fa-calendar-days"></i> Ngày giờ đặt bàn</strong>
                            {{ $reservationDateTime->format('H:i - d/m/Y') }}
                        </div>

                        <div class="info-item">
                            <strong><i class="fa-solid fa-chair"></i> Bàn số</strong>
                            {{ $reservation->table_id }}
                        </div>

                        <div class="info-item">
                            <strong><i class="fa-solid fa-wallet"></i> Tiền cọc</strong>
                            {{ number_format($reservation->deposit_amount ?? 0, 0, ',', '.') }}đ
                        </div>

                        <div class="info-item">
                            <strong><i class="fa-solid fa-credit-card"></i> Thanh toán cọc</strong>
                            @if($reservation->payment_status === 'deposit_paid')
                                Đã xác nhận cọc
                            @elseif($reservation->payment_status === 'deposit_submitted')
                                Đã gửi thông tin cọc, chờ xác nhận
                            @elseif($reservation->payment_status === 'unpaid')
                                Chưa thanh toán cọc
                            @else
                                {{ $reservation->payment_status ?? 'Chưa cập nhật' }}
                            @endif
                        </div>
                    </div>

                    @if($reservation->cancellation_status === 'pending')
                        <div class="cancellation-message cancellation-pending">
                            <i class="fa-solid fa-clock"></i>
                            Yêu cầu hủy đang chờ nhân viên xử lý.
                        </div>
                    @elseif($reservation->cancellation_status === 'approved')
                        <div class="cancellation-message cancellation-approved">
                            <i class="fa-solid fa-circle-check"></i>
                            Yêu cầu hủy đã được duyệt.
                        </div>
                    @elseif($reservation->cancellation_status === 'rejected')
                        <div class="cancellation-message cancellation-rejected">
                            <i class="fa-solid fa-circle-xmark"></i>
                            Yêu cầu hủy đã bị từ chối.
                        </div>
                    @endif

                    @if($canRequestCancellation)
                        <form class="cancel-form"
                              action="{{ route('reservations.requestCancellation', $reservation->id) }}"
                              method="POST">
                            @csrf

                            <label for="cancel-reason-{{ $reservation->id }}">
                                Lý do hủy (không bắt buộc)
                            </label>

                            <textarea
                                id="cancel-reason-{{ $reservation->id }}"
                                name="cancellation_reason"
                                rows="2"
                                maxlength="1000"
                                placeholder="Nhập lý do nếu có..."
                            ></textarea>

                            <button type="submit"
                                    class="btn-request-cancel"
                                    onclick="return confirm('Bạn muốn gửi yêu cầu hủy đặt bàn? Tiền cọc không tự động hoàn lại.')">
                                <i class="fa-solid fa-ban"></i>
                                Gửi yêu cầu hủy
                            </button>

                            <p class="cancel-note">
                                Chỉ được gửi yêu cầu khi đã xác nhận cọc và còn ít nhất 2 tiếng trước giờ đặt bàn.
                                Tiền cọc không tự động hoàn lại.
                            </p>
                        </form>
                    @endif
                </div>

                <div class="order-footer">
                    <span class="total-label">
                        <i class="fa-solid fa-circle-info"></i>
                        Thông tin đặt bàn
                    </span>
                </div>
            </div>
        @empty
            {{-- Không hiển thị thông báo riêng tại đây vì danh sách đã được lọc chung. --}}
        @endforelse

        {{-- LỊCH SỬ ĐƠN HÀNG --}}
        @forelse($orders as $order)
            <div class="order-block transaction-item" data-type="order">
                <div class="order-header">
                    <span class="order-id">
                        <i class="fa-solid fa-gem"></i>
                        ĐƠN HÀNG #{{ $order->id }}
                    </span>

                    @php
                        $bgClass = \App\Helpers\StatusHelper::getBgClass($order->status);
                    @endphp

                    <div class="status-badge {{ $bgClass }}">
                        {{ \App\Helpers\StatusHelper::getTextWithEmoji($order->status) }}
                    </div>
                </div>

                <div class="product-list">
                    @foreach($order->items as $item)
                        @if($item->quantity > 0)
                            @php
                                $imageName = ($item->menu && $item->menu->image)
                                    ? $item->menu->image
                                    : 'default.jpg';

                                $imageUrl = str_contains($imageName, 'images/')
                                    ? asset($imageName)
                                    : asset('images/' . $imageName);
                            @endphp

                            <div class="product-row">
                                <img
                                    src="{{ $imageUrl }}"
                                    class="product-img"
                                    alt="{{ $item->product_name ?? 'Món ăn' }}"
                                    onerror="this.onerror=null;this.src='{{ asset('images/default.jpg') }}'">

                                <div class="product-detail">
                                    <div class="product-name">
                                        {{ $item->product_name ?? 'Món ăn' }}
                                    </div>

                                    <div class="product-qty">
                                        Số lượng: {{ $item->quantity }}
                                    </div>

                                    @if($item->is_extra)
                                        <span style="display:inline-block;margin-top:6px;padding:3px 8px;border-radius:10px;background:#FFF4D6;color:#B7791F;font-size:11px;font-weight:600;">
                                            Gọi thêm tại bàn · {{ $item->created_at?->format('H:i') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="product-price">
                                    {{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="order-footer">
                    <div>
                        <span class="total-label">Tổng hóa đơn</span>
                        <span class="total-amount">
                            {{ number_format($order->total_price, 0, ',', '.') }}đ
                        </span>
                    </div>

                    <div class="transaction-actions">
                        @if($order->status === 'pending')
                            <button class="btn-cancel"
                                    onclick="cancelOrder({{ $order->id }}, this)">
                                <i class="fa-solid fa-ban"></i>
                                Hủy đơn
                            </button>
                        @endif

                        <button class="btn-detail"
                                onclick="toggleDetail('detail-{{ $order->id }}')">
                            <i class="fa-solid fa-eye"></i>
                            Xem chi tiết
                        </button>
                    </div>
                </div>

                <div id="detail-{{ $order->id }}" class="detail-box" style="display:none;">
                    <div class="transaction-info">
                        <div class="info-item">
                            <strong><i class="fa-solid fa-user-tie"></i> Khách hàng</strong>
                            {{ $order->name }}
                        </div>

                        <div class="info-item">
                            <strong><i class="fa-solid fa-chair"></i> Vị trí</strong>
                            {{ $order->table->name ?? 'Chưa có bàn' }}
                            - {{ $order->table->capacity ?? 0 }} ghế
                        </div>

                        <div class="info-item">
                            <strong><i class="fa-solid fa-note-sticky"></i> Ghi chú</strong>
                            {{ $order->notes ?: 'Không có ghi chú' }}
                        </div>

                        <div class="info-item">
                            <strong><i class="fa-solid fa-wallet"></i> Thanh toán</strong>
                            @if(strtolower($order->payment_method ?? '') === 'cash')
                                Tiền mặt
                            @elseif(strtolower($order->payment_method ?? '') === 'transfer')
                                Chuyển khoản
                            @else
                                {{ $order->payment_method ?? 'Chưa cập nhật' }}
                            @endif
                        </div>

                        <div class="info-item">
                            <strong><i class="fa-solid fa-clock"></i> Giờ đặt</strong>
                            {{ $order->created_at?->format('H:i d/m/Y') ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Không hiển thị thông báo riêng tại đây vì danh sách đã được lọc chung. --}}
        @endforelse
    </div>

    {{-- THÔNG BÁO KHI KHÔNG CÓ GIAO DỊCH PHÙ HỢP --}}
    <div id="no-transactions" class="empty-state" style="display:none;">
        <i class="fa-solid fa-receipt"></i>
        <p>Không tìm thấy giao dịch nào.</p>
    </div>
</div>

<script>
    function filterTransactions(type, element) {
        document.querySelectorAll('.tab-item').forEach(tab => {
            tab.classList.remove('active');
        });

        element.classList.add('active');

        const items = document.querySelectorAll('.transaction-item');
        let visibleCount = 0;

        items.forEach(item => {
            const show = type === 'all' || item.dataset.type === type;

            item.style.display = show ? '' : 'none';

            if (show) {
                visibleCount++;
            }
        });

        const emptyMessage = document.getElementById('no-transactions');

        if (emptyMessage) {
            emptyMessage.style.display = visibleCount === 0 ? 'block' : 'none';

            const message = type === 'reservation'
                ? 'Bạn chưa có lịch sử đặt bàn.'
                : type === 'order'
                    ? 'Bạn chưa có đơn hàng nào.'
                    : 'Bạn chưa có giao dịch nào.';

            emptyMessage.querySelector('p').textContent = message;
        }
    }

    function toggleDetail(id) {
        const box = document.getElementById(id);

        if (box) {
            box.style.display =
                box.style.display === 'none' ? 'block' : 'none';
        }
    }

    function cancelOrder(orderId, btnElement) {
        if (!confirm('Bạn có chắc muốn hủy đơn hàng này? Hành động này không thể hoàn tác.')) {
            return;
        }

        btnElement.disabled = true;
        btnElement.innerHTML =
            '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';

        fetch(`/order/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN':
                    document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(async response => {
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Không thể hủy đơn hàng.');
            }

            return data;
        })
        .then(data => {
            if (data.success) {
                alert('Đơn hàng đã bị hủy thành công!');
                window.location.reload();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể hủy đơn hàng'));

                btnElement.disabled = false;
                btnElement.innerHTML =
                    '<i class="fa-solid fa-ban"></i> Hủy đơn';
            }
        })
        .catch(error => {
            console.error('Lỗi khi hủy đơn:', error);
            alert('Không thể hủy đơn hàng: ' + error.message);

            btnElement.disabled = false;
            btnElement.innerHTML =
                '<i class="fa-solid fa-ban"></i> Hủy đơn';
        });
    }

    // CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG REALTIME
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Echo !== 'undefined') {
            @foreach($orders as $order)
                Echo.channel('orders.{{ $order->id }}')
                    .listen('.status.updated', function () {
                        window.location.reload();
                    });
            @endforeach
        }

        // Hiển thị toàn bộ giao dịch khi vừa mở trang.
        const allTab = document.querySelector('.tab-item.active');

        if (allTab) {
            filterTransactions('all', allTab);
        }
    });
</script>
@endsection
