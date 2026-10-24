@extends('layouts.admin')
@section('title', 'Phiếu nhập ' . $receipt->code)
@section('page_title', 'Chi tiết phiếu nhập')
@section('topbar_title', 'Chi tiết phiếu nhập')

@section('admin_content')
@include('admin.inventory._style')

<div class="inv-panel">

    <div class="inv-head">
        <div>
            <h2 class="inv-title">Phiếu nhập {{ $receipt->code }}</h2>
            <p class="inv-sub">Ngày nhập {{ $receipt->received_at->format('d/m/Y') }}</p>
        </div>
        <div class="inv-actions">
            <a href="{{ route('admin.inventory.receipts.create') }}" class="btn btn-primary">+ Phiếu mới</a>
            <a href="{{ route('admin.inventory.receipts.index') }}" class="btn btn-ghost">Quay lại</a>
        </div>
    </div>

    <dl class="inv-dl">
        <dt>Nhà cung cấp</dt><dd>{{ $receipt->supplier->name ?? '—' }}</dd>
        <dt>Người lập</dt><dd>{{ $receipt->creator->name ?? '—' }}</dd>
        <dt>Ghi chú</dt><dd>{{ $receipt->note ?: '—' }}</dd>
        <dt>Tổng tiền</dt><dd><strong>{{ number_format($receipt->total_amount, 0, ',', '.') }}đ</strong></dd>
    </dl>

    <div class="inv-wrap" style="margin-top:14px">
        <table class="inv-table">
            <thead><tr><th>Mã</th><th>Nguyên liệu</th><th class="r">Số lượng</th><th class="r">Đơn giá</th><th class="r">Thành tiền</th></tr></thead>
            <tbody>
            @foreach($receipt->items as $it)
                <tr>
                    <td class="inv-code">{{ $it->ingredient->code }}</td>
                    <td><a href="{{ route('admin.inventory.ingredients.show', $it->ingredient) }}"><strong>{{ $it->ingredient->name }}</strong></a></td>
                    <td class="r">{{ \App\Models\Ingredient::fmt($it->quantity) }} {{ $it->ingredient->unit }}</td>
                    <td class="r">{{ number_format($it->unit_cost, 0, ',', '.') }}đ</td>
                    <td class="r"><strong>{{ number_format($it->line_total, 0, ',', '.') }}đ</strong></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection