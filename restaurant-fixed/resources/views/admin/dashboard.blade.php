@extends('layouts.admin')

@section('content')
<div class="sidebar">
    <p>Hệ thống quản lý</p>
    <a href="#">Tổng quan</a>
    <a href="#">Đơn hàng</a>
    <a href="#">Món ăn</a>
</div>

<div class="main-content">
    <div class="header">
        <h3>Bảng điều khiển</h3>
    </div>
    
    <h4>Đơn hàng gần đây</h4>
    <table>
        <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Bàn</th>
                <th>Khách</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentOrders as $order)
            <tr>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->table_name }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ number_format($order->total_price) }}đ</td>
                <td><span class="status-label">Đang xử lý</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
// Nghe lắng cập nhật đơn hàng từ khách hàng
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Echo !== 'undefined') {
        Echo.channel('admin-orders-updates')
            .listen('.order.status.updated', (data) => {
                console.log('Đơn hàng cập nhật:', data);
                // Reload trang hoặc cập nhật UI
                window.location.reload();
            });
    }
});
</script>
@endsection