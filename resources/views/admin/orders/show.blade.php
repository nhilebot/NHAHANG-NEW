@extends('layouts.admin')

@section('page_title', 'Chi tiết đơn hàng')
@section('topbar_title', 'Chi tiết đơn hàng')

@section('admin_content')
<div class="card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Đơn hàng #{{ $order->code ?? $order->id }}</h2>
            <div class="text-muted">{{ $order->name ?? $order->user->name ?? 'Khách hàng' }}</div>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Quay lại</a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Món ăn</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->items->where('quantity', '>', 0) as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price) }}đ</td>
                    <td>{{ number_format($item->price * $item->quantity) }}đ</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">Đơn hàng chưa có món.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Tổng tiền</th>
                    <th>{{ number_format($order->total_price) }}đ</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection