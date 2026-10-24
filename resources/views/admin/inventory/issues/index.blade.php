@extends('layouts.admin')
@section('title', 'Phiếu xuất kho')
@section('page_title', 'Phiếu xuất kho')
@section('topbar_title', 'Phiếu xuất kho')

@section('admin_content')
@include('admin.inventory._style')
@php $hasFilter = request()->hasAny(['search', 'reason', 'from', 'to']); @endphp

<div class="inv-panel">
    <div class="inv-head">
        <div>
            <h2 class="inv-title">Phiếu xuất kho</h2>
            <p class="inv-sub">Ghi nhận nguyên liệu dùng trong bếp, hao hụt, hỏng hoặc hủy. Mỗi phiếu trừ tồn kho và ghi vào sổ kho.</p>
        </div>
        <div class="inv-actions">
            <a href="{{ route('admin.inventory.movements.index') }}" class="btn btn-ghost">Sổ kho</a>
            <a href="{{ route('admin.inventory.issues.create') }}" class="btn btn-primary">+ Lập phiếu xuất</a>
        </div>
    </div>

    <form method="GET" class="inv-filter">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Mã phiếu">
        <select name="reason" onchange="this.form.submit()">
            <option value="">Mọi lý do</option>
            @foreach(\App\Models\StockIssue::REASONS as $k => $label)
                <option value="{{ $k }}" @selected(request('reason') === $k)>{{ $label }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ request('from') }}" title="Từ ngày">
        <input type="date" name="to" value="{{ request('to') }}" title="Đến ngày">
        <button type="submit" class="btn btn-ghost">Lọc</button>
        @if($hasFilter)<a href="{{ route('admin.inventory.issues.index') }}" class="btn btn-ghost">Xóa lọc</a>@endif
    </form>

    <div class="inv-wrap">
        <table class="inv-table">
            <thead>
                <tr><th>Mã phiếu</th><th>Ngày xuất</th><th>Lý do</th><th class="r">Số mặt hàng</th><th class="r">Giá trị</th><th></th></tr>
            </thead>
            <tbody>
            @forelse($issues as $i)
                <tr>
                    <td class="inv-code"><a href="{{ route('admin.inventory.issues.show', $i) }}"><strong>{{ $i->code }}</strong></a></td>
                    <td>{{ $i->issued_at->format('d/m/Y') }}</td>
                    <td>{{ $i->reason_label }}</td>
                    <td class="r">{{ $i->items_count }}</td>
                    <td class="r"><strong>{{ number_format($i->total_amount, 0, ',', '.') }}đ</strong></td>
                    <td><div class="inv-actions"><a href="{{ route('admin.inventory.issues.show', $i) }}" class="btn btn-sm btn-ghost">Xem</a></div></td>
                </tr>
            @empty
                <tr><td colspan="6" class="inv-empty">{{ $hasFilter ? 'Không có phiếu nào khớp bộ lọc.' : 'Chưa có phiếu xuất nào.' }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="inv-pager">{{ $issues->links() }}</div>
</div>
@endsection
