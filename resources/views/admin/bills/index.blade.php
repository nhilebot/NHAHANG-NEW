@extends('layouts.admin')

@section('page_title', 'Quản lý hóa đơn')
@section('topbar_title', 'Quản lý hóa đơn')

@section('admin_content')

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h2>📄 Danh sách hóa đơn</h2>
    </div>

    <div style="display:flex;gap:20px;margin-bottom:20px;">
        <div class="stat-box">
            <h4>💰 Doanh thu hôm nay</h4>
            <p>{{ number_format($todayRevenue) }}đ</p>
        </div>

        <div class="stat-box">
            <h4>🏦 Tổng doanh thu</h4>
            <p>{{ number_format($totalRevenue) }}đ</p>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Mã HĐ</th>
                <th>Khách</th>
                <th>Bàn</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày</th>
                <th>Xem</th>
            </tr>
        </thead>

        <tbody>
            @forelse($bills as $bill)
            <tr>
                <td>#{{ $bill->id }}</td>
                <td>{{ $bill->user->name ?? '---' }}</td>
                <td>{{ $bill->table->name ?? '---' }}</td>
                <td>{{ number_format($bill->total_price) }}đ</td>
               <td>

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

            'paid' => 'Đã thanh toán',

            default => $bill->status
        };
    @endphp

    {{ $statusText }}

</td>
                <td>{{ $bill->updated_at->format('d/m/Y H:i') }}</td>

                <td>
                    <a href="{{ route('admin.bills.show', $bill->id) }}"
                       class="btn btn-primary btn-sm">
                        Chi tiết
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;">
                    Chưa có hóa đơn
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:20px;">
        {{ $bills->links() }}
    </div>
</div>

<style>
.card{
    background:#fff;
    padding:24px;
    border-radius:14px;
}

.table{
    width:100%;
    border-collapse:collapse;
}

.table th,
.table td{
    padding:12px;
    border-bottom:1px solid #eee;
}

.stat-box{
    background:#f8f8f8;
    padding:20px;
    border-radius:12px;
    min-width:250px;
}
</style>

@endsection