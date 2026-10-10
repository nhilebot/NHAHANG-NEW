{{-- Thay 'layouts.admin' bằng tên file layout thực tế của bạn --}}
@extends('layouts.admin') 

@section('page_title', 'Danh sách hóa đơn')
@section('topbar_title', 'Quản lý Hóa Đơn')

@section('admin_content')
    <!-- Bọc toàn bộ trong class 'panel' của layout -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Danh sách hóa đơn</h2>
            <!-- Nút thêm mới (Đã dùng class .btn .btn-primary của layout) -->
            <!-- <button class="btn btn-primary">Tạo hóa đơn</button> -->
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Mã HĐ</th>
                        <th>Số Bàn</th>
                        <th>Khách Hàng</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th style="text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    {{ dd($orders) }}
                    <tr>
                        <!-- ID -->
                        <td>
                            <strong>#{{ $order->id }}</strong>
                        </td>
                        
                        <!-- Số bàn -->
                        <td>
                            <span class="badge" style="background: #f1f5f9; color: #475569;">
                                 Bàn: {{ $order->table_number }}
                            </span>
                        </td>
                        
                        <!-- Tên khách -->
                        <td style="font-weight: 600; color: #374151;">
                            {{ $order->name }}
                        </td>
                        
                        <!-- Tổng tiền -->
                        <td style="font-weight: 700; color: #dc2626;">
                            {{ number_format($order->total_price) }} đ
                        </td>
                        
                        <!-- Trạng thái (Sử dụng các class badge có sẵn trong layout của bạn) -->
                       <!-- Trạng thái (Sử dụng StatusHelper để đồng bộ tiếng Việt và Emoji) -->
<td>
    @php 
        // Lấy class CSS tương ứng từ Helper (status-pending, status-serving, v.v.)
        $bgClass = \App\Helpers\StatusHelper::getBgClass($order->status); 
    @endphp

    <span class="badge {{ $bgClass }}" style="padding: 6px 12px; border-radius: 20px; font-weight: 600;">
        {{-- Hiển thị tiếng Việt kèm Emoji (ví dụ: ⏳ Chờ duyệt, 👨‍🍳 Đang phục vụ) --}}
        {{ \App\Helpers\StatusHelper::getTextWithEmoji($order->status) }}
    </span>
</td>
                        
                        <!-- Nút Xem chi tiết -->
                        <td style="text-align: center; display:flex; gap:6px; justify-content:center;">

    {{-- Nút xem --}}
    <a href="{{ route('admin.bills.show', $order->id) }}" class="btn btn-sm btn-primary">
        Xem
    </a>

    {{-- Nút hủy --}}
    @if(in_array($order->status, ['pending','confirmed']))
    <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
        @csrf
        @method('PUT')
        <button class="btn btn-sm btn-danger" onclick="return confirm('Bạn chắc chắn muốn hủy đơn này?')">
            Hủy
        </button>
    </form>
    @endif

    {{-- Nếu đã hủy --}}
    @if($order->status == 'cancelled')
        <button class="btn btn-sm btn-ghost" disabled>Đã hủy</button>
    @endif

</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Khu vực phân trang (Pagination) có thể đặt ở đây -->
        <!-- <div style="margin-top: 20px;"> {{-- $orders->links() --}} </div> -->
    </div>
@endsection