@extends('layouts.admin')
@section('title', 'Phiếu xuất ' . $issue->code)
@section('page_title', 'Chi tiết phiếu xuất')
@section('topbar_title', 'Chi tiết phiếu xuất')

@section('admin_content')
@include('admin.inventory._style')

<div class="inv-panel">
    <div class="inv-head">
        <div>
            <h2 class="inv-title">Phiếu xuất {{ $issue->code }}</h2>
            <p class="inv-sub">Ngày xuất {{ $issue->issued_at->format('d/m/Y') }}</p>
        </div>
        <div class="inv-actions">
            <a href="{{ route('admin.inventory.issues.create') }}" class="btn btn-primary">+ Phiếu mới</a>
            <a href="{{ route('admin.inventory.issues.index') }}" class="btn btn-ghost">Quay lại</a>
        </div>
    </div>

    <dl class="inv-dl">
        <dt>Lý do</dt><dd>{{ $issue->reason_label }}</dd>
        <dt>Người lập</dt><dd>{{ $issue->creator->name ?? '—' }}</dd>
        <dt>Ghi chú</dt><dd>{{ $issue->note ?: '—' }}</dd>
        <dt>Giá trị (theo giá tham khảo)</dt><dd><strong>{{ number_format($issue->total_amount, 0, ',', '.') }}đ</strong></dd>
    </dl>

    <div class="inv-wrap" style="margin-top:14px">
        <table class="inv-table">
            <thead><tr><th>Mã</th><th>Nguyên liệu</th><th class="r">Số lượng xuất</th><th class="r">Đơn giá</th><th class="r">Giá trị</th></tr></thead>
            <tbody>
            @foreach($issue->items as $it)
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
