@extends('shared')

@section('title', 'Lịch sử giao dịch')

@section('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/pages/orders-history.css') }}?v=1">
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
                    'pending_deposit' => $reservation->payment_status === 'deposit_submitted'
                        ? 'Chờ nhân viên xác nhận cọc'
                        : 'Chờ khách thanh toán cọc',
                    'pending' => 'Chờ xác nhận đặt bàn',
                    'confirmed' => 'Đã xác nhận',
                    'arrived' => 'Khách đã đến',
                    'serving' => 'Đang phục vụ',
                    'ready' => 'Món ăn đã sẵn sàng',
                    'served' => 'Đã phục vụ',
                    'completed' => 'Hoàn tất',
                    'cancelled' => $reservation->cancellation_status === 'expired'
                        ? 'Đã hủy do hết hạn cọc'
                        : 'Đã hủy',
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
                            {{ $reservation->table->name ?? $reservation->table_id }}
                        </div>

                        <div class="info-item">
                            <strong><i class="fa-solid fa-wallet"></i> Tiền cọc</strong>
                            {{ number_format($reservation->deposit_amount ?? 0, 0, ',', '.') }}đ
                        </div>

                        <div class="info-item">
                            <strong><i class="fa-solid fa-receipt"></i> Tổng tiền món</strong>
                            {{ number_format($reservation->total_price ?? 0, 0, ',', '.') }}đ
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
                        {{ number_format($reservation->total_price ?? 0, 0, ',', '.') }}đ ·
                        Cọc {{ number_format($reservation->deposit_amount ?? 0, 0, ',', '.') }}đ
                    </span>
                    <div class="transaction-actions">
                        <button class="btn-detail" type="button"
                                onclick="toggleDetail('reservation-detail-{{ $reservation->id }}')">
                            <i class="fa-solid fa-eye"></i> Xem chi tiết
                        </button>
                        @if($reservation->id === $editableReservationId)
                            <a class="btn-detail"
                               href="{{ route('cart.index', ['reservation_id' => $reservation->id]) }}">
                                <i class="fa-solid fa-credit-card"></i> Tiếp tục thanh toán cọc
                            </a>
                            <a class="btn-detail"
                               href="{{ route('reservation.index', ['reservation_id' => $reservation->id]) }}">
                                <i class="fa-solid fa-pen"></i> Chỉnh sửa
                            </a>
                        @elseif($reservation->status === 'pending_deposit' && $reservation->payment_status === 'deposit_submitted')
                            <span>Đang chờ nhân viên đối soát; không thể chỉnh sửa đơn.</span>
                        @elseif($reservation->status === 'pending_deposit' && $reservation->payment_status === 'unpaid')
                            <span>Chỉ yêu cầu chờ cọc mới nhất được tiếp tục thanh toán hoặc chỉnh sửa.</span>
                        @endif
                    </div>
                </div>

                <div id="reservation-detail-{{ $reservation->id }}" class="detail-box" style="display:none;">
                    <section class="detail-section">
                        <h3 class="detail-section-title">Thông tin khách hàng</h3>
                        <div class="transaction-info">
                            <div class="info-item">
                                <strong><i class="fa-solid fa-user"></i> Họ tên</strong>
                                {{ $reservation->full_name }}
                            </div>
                            <div class="info-item">
                                <strong><i class="fa-solid fa-phone"></i> Số điện thoại</strong>
                                {{ $reservation->phone }}
                            </div>
                            @if($reservation->email)
                                <div class="info-item">
                                    <strong><i class="fa-solid fa-envelope"></i> Email</strong>
                                    {{ $reservation->email }}
                                </div>
                            @endif
                            <div class="info-item">
                                <strong><i class="fa-solid fa-calendar-days"></i> Ngày giờ đặt</strong>
                                {{ $reservationDateTime->format('H:i - d/m/Y') }}
                            </div>
                            <div class="info-item">
                                <strong><i class="fa-solid fa-chair"></i> Bàn</strong>
                                {{ $reservation->table->name ?? ('Bàn ' . $reservation->table_id) }}
                            </div>
                        </div>
                    </section>

                    <section class="detail-section">
                        <h3 class="detail-section-title">Chi tiết đơn hàng</h3>
                        <div class="product-list">
                            @forelse($reservation->reservationItems as $line)
                                @if($line->active_quantity > 0)
                                    <div class="product-row">
                                        <div class="product-detail">
                                            <div class="product-name">{{ $line->menu_name }}</div>
                                            <div class="product-qty">Số lượng: {{ $line->active_quantity }}</div>
                                        </div>
                                        <div class="product-price">
                                            {{ number_format($line->line_total, 0, ',', '.') }}đ
                                        </div>
                                    </div>
                                @endif
                            @empty
                                @foreach($reservation->cart_data ?? [] as $line)
                                    <div class="product-row">
                                        <div class="product-detail">
                                            <div class="product-name">{{ $line['name'] ?? 'Món ăn' }}</div>
                                            <div class="product-qty">Số lượng: {{ $line['quantity'] ?? 0 }}</div>
                                        </div>
                                        <div class="product-price">
                                            {{ number_format(($line['price'] ?? 0) * ($line['quantity'] ?? 0), 0, ',', '.') }}đ
                                        </div>
                                    </div>
                                @endforeach
                            @endforelse
                        </div>
                    </section>

                    <section class="detail-section">
                        <h3 class="detail-section-title">Thông tin hóa đơn</h3>
                        <div class="transaction-info">
                            @if(($reservation->discount_amount ?? 0) > 0)
                                <div class="info-item">
                                    <strong><i class="fa-solid fa-tag"></i> Giảm giá</strong>
                                    {{ number_format($reservation->discount_amount, 0, ',', '.') }}đ
                                </div>
                            @endif
                            <div class="info-item">
                                <strong><i class="fa-solid fa-receipt"></i> Tổng tiền món</strong>
                                {{ number_format($reservation->total_price ?? 0, 0, ',', '.') }}đ
                            </div>
                        </div>
                    </section>

                    <section class="detail-section">
                        <h3 class="detail-section-title">Thông tin thanh toán</h3>
                        <div class="transaction-info">
                            <div class="info-item">
                                <strong><i class="fa-solid fa-wallet"></i> Tiền cọc</strong>
                                {{ number_format($reservation->deposit_amount ?? 0, 0, ',', '.') }}đ
                            </div>
                            <div class="info-item">
                                <strong><i class="fa-solid fa-credit-card"></i> Trạng thái thanh toán cọc</strong>
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
                            <div class="info-item">
                                <strong><i class="fa-solid fa-circle-info"></i> Trạng thái đặt bàn</strong>
                                {{ $reservationStatusText }}
                            </div>
                        </div>
                    </section>
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
                    <h3 class="detail-section-title product-list-title">Chi tiết đơn hàng</h3>
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
                    <section class="detail-section">
                        <h3 class="detail-section-title">Thông tin khách hàng</h3>
                        <div class="transaction-info">
                            <div class="info-item">
                                <strong><i class="fa-solid fa-user-tie"></i> Khách hàng</strong>
                                {{ $order->name }}
                            </div>
                            <div class="info-item">
                                <strong><i class="fa-solid fa-phone"></i> Số điện thoại</strong>
                                {{ $order->phone ?? 'Chưa cập nhật' }}
                            </div>
                            <div class="info-item">
                                <strong><i class="fa-solid fa-chair"></i> Vị trí</strong>
                                {{ $order->table->name ?? 'Chưa có bàn' }}
                                - {{ $order->table->capacity ?? 0 }} ghế
                            </div>
                            <div class="info-item">
                                <strong><i class="fa-solid fa-clock"></i> Giờ đặt</strong>
                                {{ $order->created_at?->format('H:i d/m/Y') ?? 'N/A' }}
                            </div>
                        </div>
                    </section>

                    <section class="detail-section">
                        <h3 class="detail-section-title">Thông tin hóa đơn</h3>
                        <div class="transaction-info">
                            <div class="info-item">
                                <strong><i class="fa-solid fa-receipt"></i> Tổng hóa đơn</strong>
                                {{ number_format($order->total_price, 0, ',', '.') }}đ
                            </div>
                            @if($order->notes)
                                <div class="info-item">
                                    <strong><i class="fa-solid fa-note-sticky"></i> Ghi chú</strong>
                                    {{ $order->notes }}
                                </div>
                            @endif
                        </div>
                    </section>

                    <section class="detail-section">
                        <h3 class="detail-section-title">Thông tin thanh toán</h3>
                        <div class="transaction-info">
                            <div class="info-item">
                                <strong><i class="fa-solid fa-credit-card"></i> Phương thức thanh toán</strong>
                                @if(strtolower($order->payment_method ?? '') === 'cash')
                                    Tiền mặt
                                @elseif(strtolower($order->payment_method ?? '') === 'transfer')
                                    Chuyển khoản
                                @else
                                    {{ $order->payment_method ?? 'Chưa cập nhật' }}
                                @endif
                            </div>
                            <div class="info-item">
                                <strong><i class="fa-solid fa-circle-info"></i> Trạng thái đơn hàng</strong>
                                {{ \App\Helpers\StatusHelper::getTextWithEmoji($order->status) }}
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        @empty
            {{-- Không hiển thị thông báo riêng tại đây vì danh sách đã được lọc chung. --}}
        @endforelse
    </div>

    {{-- THÔNG BÁO KHI KHÔNG CÓ GIAO DỊCH PHÙ HỢP --}}
    <div id="no-transactions" class="empty-state" style="display:none;" role="status" aria-live="polite">
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
