@extends('layouts.admin')
@section('title', 'Phiếu nhập kho')
@section('page_title', 'Phiếu nhập kho')
@section('topbar_title', 'Phiếu nhập kho')

@section('admin_content')
@include('admin.inventory._style')
@php $hasFilter = request()->hasAny(['search', 'supplier_id', 'from', 'to']); @endphp

<div class="inv-panel">

    <div class="inv-head">
        <div>
            <h2 class="inv-title">Phiếu nhập kho</h2>
            <p class="inv-sub">Mỗi phiếu nhập làm tăng tồn kho và ghi một dòng vào sổ kho.</p>
        </div>
        <div class="inv-actions">
            <a href="{{ route('admin.inventory.movements.index') }}" class="btn btn-ghost">Sổ kho</a>
            <a href="{{ route('admin.inventory.receipts.create') }}" class="btn btn-primary">+ Lập phiếu nhập</a>
        </div>
    </div>

    <form method="GET" class="inv-filter">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Mã phiếu">
        <select name="supplier_id" onchange="this.form.submit()">
            <option value="">Tất cả nhà cung cấp</option>
            @foreach($suppliers as $s)
                <option value="{{ $s->id }}" @selected(request('supplier_id') == $s->id)>{{ $s->name }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ request('from') }}" title="Từ ngày">
        <input type="date" name="to" value="{{ request('to') }}" title="Đến ngày">
        <button type="submit" class="btn btn-ghost">Lọc</button>
        @if($hasFilter)<a href="{{ route('admin.inventory.receipts.index') }}" class="btn btn-ghost">Xóa lọc</a>@endif
    </form>

    <div class="inv-wrap">
        <table class="inv-table">
            <thead>
                <tr><th>Mã phiếu</th><th>Ngày nhập</th><th>Nhà cung cấp</th><th class="r">Số mặt hàng</th><th class="r">Tổng tiền</th><th></th></tr>
            </thead>
            <tbody>
            @forelse($receipts as $r)
                <tr>
                    <td class="inv-code"><a href="{{ route('admin.inventory.receipts.show', $r) }}"><strong>{{ $r->code }}</strong></a></td>
                    <td>{{ $r->received_at->format('d/m/Y') }}</td>
                    <td class="inv-muted">{{ $r->supplier->name ?? '—' }}</td>
                    <td class="r">{{ $r->items_count }}</td>
                    <td class="r"><strong>{{ number_format($r->total_amount, 0, ',', '.') }}đ</strong></td>
                    <td><div class="inv-actions"><a href="{{ route('admin.inventory.receipts.show', $r) }}" class="btn btn-sm btn-ghost">Xem</a></div></td>
                </tr>
            @empty
                <tr><td colspan="6" class="inv-empty">{{ $hasFilter ? 'Không có phiếu nào khớp bộ lọc.' : 'Chưa có phiếu nhập nào.' }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="inv-pager">{{ $receipts->links() }}</div>
</div>
@endsection