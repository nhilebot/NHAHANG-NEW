@extends('shared') {{-- Chỉ giữ lại 1 extends chuẩn nhất --}}

@section('title', 'Lịch sử đơn hàng')

@section('head')
<style>
    body { background-color: #f4f6f9; font-family: 'Times New Roman', serif; } /* Đổi sang font dễ đọc theo style bạn thích ở trang đăng nhập */
    .history-container { max-width: 1100px; margin: 30px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .title-page { text-align: center; font-weight: bold; margin-bottom: 25px; font-size: 24px; color: #d9534f; }
    .order-tabs { display: flex; justify-content: space-around; border-bottom: 2px solid #eee; margin-bottom: 20px; background: #fff; position: sticky; top: 0; z-index: 10; }
    .tab-item { padding: 15px 10px; cursor: pointer; font-weight: 600; color: #666; transition: 0.3s; border-bottom: 3px solid transparent; flex: 1; text-align: center; }
    .tab-item:hover { color: #d9534f; }
    .tab-item.active { color: #d9534f; border-bottom-color: #d9534f; }

    .order-block { border: 1px solid #ebebeb; margin-bottom: 25px; border-radius: 4px; overflow: hidden; }
    .order-info-header { background: #fafafa; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; }
    
    .badge-status { padding: 6px 15px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-block; }

    .product-row { display: flex; padding: 15px 20px; align-items: center; border-bottom: 1px solid #f1f1f1; background: #fff; }
    .product-img { width: 80px; height: 80px; object-fit: cover; border: 1px solid #eee; border-radius: 4px; background: #f9f9f9; }
    .product-detail { flex: 1; padding-left: 15px; }
    .product-name { font-weight: bold; font-size: 16px; color: #333; }
    .product-qty { color: #888; font-size: 14px; }

    .order-footer { padding: 15px 20px; background: #fff; display: flex; justify-content: flex-end; align-items: center; gap: 15px; border-top: 1px solid #f1f1f1; }
    .total-price-all { font-size: 20px; font-weight: bold; color: #d9534f; }
    
    .btn-action { padding: 8px 20px; border-radius: 4px; font-size: 14px; font-weight: 600; text-decoration: none; border: none; transition: 0.3s; }
    .btn-view { background-color: #d9534f; color: white; }
    .btn-view:hover { background-color: #c9302c; color: #fff; }

    .empty-state { text-align: center; padding: 50px; display: none; }
</style>
@endsection

@section('content')
<div class="history-container">
    <!-- <h3 class="title-page" >Lịch sử đơn hàng</h3> -->

    @php
        $justPaidId = session('just_paid_id');
        $displayOrders = $justPaidId ? $orders->where('id', $justPaidId) : $orders;
    @endphp

    @if($justPaidId)
        <div class="alert alert-success text-center mb-4">
            🎉 Thanh toán thành công! Đây là đơn hàng bạn vừa đặt. 
            <a href="{{ route('orders.history') }}" class="ml-2" style="text-decoration: underline;">Xem tất cả lịch sử</a>
        </div>
    @endif

    <div class="order-tabs">
    <div class="tab-item {{ !$justPaidId ? 'active' : '' }}" onclick="filterByStatus('all', this)">Tất cả</div>
    <div class="tab-item" onclick="filterByStatus('pending', this)">Chờ duyệt</div>
    {{-- Thêm tab này --}}
    <div class="tab-item" onclick="filterByStatus('serving_group', this)">Đang phục vụ</div>
    {{-- Đổi tab này --}}
    <div class="tab-item" onclick="filterByStatus('completed', this)">Hoàn tất</div>
    <div class="tab-item" onclick="filterByStatus('cancelled', this)">Đã hủy</div>
</div>

    <div id="order-wrapper">
        @forelse($displayOrders as $order)
        <div class="order-block" data-status="{{ $order->status }}">
            <div class="order-info-header">
                <div style="font-weight: bold; color: #555;">
                <i class="far fa-calendar-alt"></i> Ngày đặt: 
                {{-- Dùng dấu ?-> để tránh lỗi khi dữ liệu trống --}}
                {{ $order->created_at?->format('H:i d/m/Y') ?? 'Đang cập nhật' }} 
                </div>             
                <div class="status-badge">
                    <span class="badge {{ \App\Helpers\StatusHelper::getBadgeClass($order->status) }} badge-status">
                        {{ \App\Helpers\StatusHelper::getTextWithEmoji($order->status) }}
                    </span>
                </div>
            </div>

            {{-- HIỂN THỊ DANH SÁCH MÓN ĂN TRONG ĐƠN HÀNG --}}
            @if(isset($order->items) && $order->items->count() > 0)
                @foreach($order->items as $item)
                <div class="product-row">
                    {{-- Giả định bạn có bảng menus liên kết, nếu lỗi ở đây hãy báo mình nhé --}}
                    <img src="{{ asset('images/hs1.jpg') }}" 
                         class="product-img" 
                         alt="Hình món ăn"
                         onerror="this.src='{{ asset('images/default.jpg') }}'">
                    
                    <div class="product-detail">
                        <div class="product-name">{{ $item->menu->name ?? 'Tên món ăn' }}</div>
                        <div class="product-qty">Số lượng: {{ $item->quantity }}</div>
                            @if($item->is_extra)
                                <span style="display:inline-block;margin-left:6px;padding:2px 8px;border-radius:10px;background:#FFF4D6;color:#B7791F;font-size:11px;font-weight:600;">
                                    Gọi thêm tại bàn · {{ $item->created_at?->format('H:i') }}
                                </span>
                            @endif

                    </div>
                    
                    <div class="font-weight-bold">
                        {{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ
                    </div>
                </div>
                @endforeach
            @else
                <div class="p-3 text-center text-muted">
                    (Đơn hàng này chưa có chi tiết món ăn)
                </div>
            @endif
            {{-- KẾT THÚC MÓN ĂN --}}

            <div class="order-footer">
                <div class="total-info">
                    <span class="text-muted">Tổng thanh toán:</span>
                    <span class="total-price-all">{{ number_format($order->total_price, 0, ',', '.') }}đ</span>
                </div>
                <button class="btn-action btn-view" onclick="toggleDetail('detail-{{ $order->id }}')">
                    Xem chi tiết đơn
                </button>
            </div>

            <div id="detail-{{ $order->id }}" style="display: none; padding: 20px; border-top: 1px dashed #eee; background: #fdfdfd;">
                <div class="row">
                    <div class="col-md-6">
                        <h5 style="font-weight: bold; color: #d9534f;">Thông tin khách hàng</h5>
                        <p><strong>Họ tên:</strong> {{ $order->name ?? 'Khách hàng' }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $order->phone ?? 'Không có' }}</p>
                        <p><strong>Bàn số:</strong> {{ $order->table_number ?? 'Mang về' }}</p>
                        <p><strong>Ghi chú:</strong> {{ $order->notes ?? 'Không có' }}</p>
                    </div>
                    <div class="col-md-6 text-right">
                        <h5 style="font-weight: bold; color: #d9534f;">Thanh toán & Trạng thái</h5>
                        
                        <p><strong>Phương thức:</strong> 
                            @if(isset($order->payment_method) && strtoupper($order->payment_method) == 'BANK')
                                <span class="badge" style="background-color: #3498db; color: white;">Chuyển khoản QR</span>
                            @else
                                <span class="badge" style="background-color: #95a5a6; color: white;">Tiền mặt</span>
                            @endif
                        </p>

                        <p><strong>Trạng thái đơn:</strong> 
                            @if($order->status == 'paid')
                                <span class="text-success" style="font-weight: bold;">Đã thanh toán</span>
                            @elseif($order->status == 'pending')
                                <span class="text-warning" style="font-weight: bold;">Chờ xử lý</span>
                            @else
                                <span class="text-danger" style="font-weight: bold;">{{ $order->status }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <p>Bạn chưa có đơn hàng nào.</p>
        </div>
        @endforelse
    </div>

    <div id="no-orders" class="empty-state">
        <p>Không có đơn hàng nào ở trạng thái này.</p>
    </div>
</div>

<script>
function filterByStatus(status, element) {
    // 1. Đổi active tab
    document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
    element.classList.add('active');

    let orders = document.querySelectorAll('.order-block');
    let visibleCount = 0;

    // 2. Lọc đơn hàng
    orders.forEach(order => {
        let orderStatus = order.getAttribute('data-status').toLowerCase();
        
        let show = (status === 'all') || 
                   // Nhóm đang phục vụ: gồm khách đã vào, đang nấu, nấu xong, đã bưng
                   (status === 'serving_group' && ['arrived', 'serving', 'ready', 'served'].includes(orderStatus)) ||
                   // Nhóm hoàn tất: gồm các loại thanh toán và chữ completed
                   (status === 'completed' && ['paid', 'paid_cash', 'paid_transfer', 'completed'].includes(orderStatus)) ||
                   // Lọc đơn lẻ (pending, cancelled...)
                   (orderStatus === status);

        if (show) {
            order.style.display = 'block';
            visibleCount++;
        } else {
            order.style.display = 'none';
        }
    });

    // 3. Hiển thị thông báo nếu không có đơn nào
    document.getElementById('no-orders').style.display = (visibleCount === 0) ? 'block' : 'none';
}

function toggleDetail(id) {
    let detailBox = document.getElementById(id);
    if (detailBox.style.display === "none" || detailBox.style.display === "") {
        detailBox.style.display = "block";
    } else {
        detailBox.style.display = "none";
    }
}
// DÁN ĐOẠN MÃ MỚI VÀO NGAY DƯỚI ĐÂY
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Echo !== 'undefined') {
            @foreach($orders as $order)
                Echo.channel('orders.{{ $order->id }}')
                    .listen('.status.updated', (data) => {
                        console.log("--- TÍN HIỆU TỪ STAFF ---");
                        console.log("Đơn hàng ID:", data.reservation.id);
                        console.log("Trạng thái mới:", data.reservation.status);
                        
                        window.location.reload();
                    });
            @endforeach
        } else {
            console.warn("Cảnh báo: Laravel Echo chưa được khởi tạo. Kiểm tra lại app.js");
        }
    });
</script>
@endsection