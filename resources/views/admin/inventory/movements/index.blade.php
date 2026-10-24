@extends('layouts.admin')
@section('title', 'Sổ kho')
@section('page_title', 'Sổ kho')
@section('topbar_title', 'Sổ kho')

@section('admin_content')
@include('admin.inventory._style')
@php $hasFilter = request()->hasAny(['ingredient_id', 'type', 'from', 'to']); @endphp

<div class="inv-panel">
    <div class="inv-head">
        <div>
            <h2 class="inv-title">Sổ kho</h2>
            <p class="inv-sub">Lịch sử mọi lần tăng/giảm tồn kho nguyên liệu. Chỉ xem, không sửa.</p>
        </div>
        <a href="{{ route('admin.inventory.receipts.index') }}" class="btn btn-ghost">Phiếu nhập kho</a>
    </div>

    <form method="GET" class="inv-filter">
        <select name="ingredient_id" onchange="this.form.submit()">
            <option value="">Tất cả nguyên liệu</option>
            @foreach($ingredients as $i)
                <option value="{{ $i->id }}" @selected(request('ingredient_id') == $i->id)>{{ $i->name }}</option>
            @endforeach
        </select>
        <select name="type" onchange="this.form.submit()">
            <option value="">Mọi loại</option>
            @foreach(\App\Models\StockMovement::TYPES as $k => $label)
                <option value="{{ $k }}" @selected(request('type') === $k)>{{ $label }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ request('from') }}" title="Từ ngày">
        <input type="date" name="to" value="{{ request('to') }}" title="Đến ngày">
        <button type="submit" class="btn btn-ghost">Lọc</button>
        @if($hasFilter)<a href="{{ route('admin.inventory.movements.index') }}" class="btn btn-ghost">Xóa lọc</a>@endif
    </form>

    <div class="inv-wrap">
        <table class="inv-table">
            <thead>
                <tr><th>Thời gian</th><th>Nguyên liệu</th><th>Loại</th><th class="r">Thay đổi</th><th class="r">Tồn sau</th><th>Chứng từ</th><th>Người thực hiện</th></tr>
            </thead>
            <tbody>
            @forelse($movements as $m)
                @php $unit = $m->ingredient->unit ?? ''; $pos = $m->quantity > 0; @endphp
                <tr>
                    <td class="inv-muted">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td><a href="{{ route('admin.inventory.ingredients.show', $m->ingredient_id) }}"><strong>{{ $m->ingredient->name ?? '—' }}</strong></a></td>
                    <td>{{ $m->type_label }}</td>
                    <td class="r"><strong style="color:{{ $pos ? '#16A34A' : '#DC2626' }}">{{ $pos ? '+' : '' }}{{ \App\Models\Ingredient::fmt($m->quantity) }}</strong> {{ $unit }}</td>
                    <td class="r">{{ \App\Models\Ingredient::fmt($m->balance_after) }} {{ $unit }}</td>
                    <td>
                        @if($m->reference instanceof \App\Models\StockReceipt)
                            <a href="{{ route('admin.inventory.receipts.show', $m->reference) }}">{{ $m->reference->code }}</a>
                        @elseif($m->reference instanceof \App\Models\StockIssue)
                            <a href="{{ route('admin.inventory.issues.show', $m->reference) }}">{{ $m->reference->code }}</a>
                        @else<span class="inv-muted">{{ $m->note ?: '—' }}</span>@endif
                    </td>
                    <td class="inv-muted">{{ $m->user->name ?? 'Hệ thống' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="inv-empty">{{ $hasFilter ? 'Không có dòng nào khớp bộ lọc.' : 'Chưa có biến động kho nào.' }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="inv-pager">{{ $movements->links() }}</div>
</div>
@endsection