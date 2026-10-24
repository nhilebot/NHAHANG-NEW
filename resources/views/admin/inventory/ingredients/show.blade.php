@extends('layouts.admin')
@section('title', 'Chi tiết nguyên liệu')
@section('page_title', 'Chi tiết nguyên liệu')
@section('topbar_title', 'Chi tiết nguyên liệu')

@section('admin_content')
@include('admin.inventory._style')

<div class="inv-panel">
    <div class="inv-head">
        <div>
            <h2 class="inv-title">{{ $ingredient->name }}</h2>
            <p class="inv-sub">Mã {{ $ingredient->code }}</p>
        </div>
        <div class="inv-actions">
            <a href="{{ route('admin.inventory.ingredients.edit', $ingredient) }}" class="btn btn-primary">Sửa</a>
            <a href="{{ route('admin.inventory.ingredients.index') }}" class="btn btn-ghost">Quay lại</a>
        </div>
    </div>

    <dl class="inv-dl">
        <dt>Nhóm</dt><dd>{{ $ingredient->category->name ?? '—' }}</dd>
        <dt>Đơn vị tính</dt><dd>{{ $ingredient->unit }}</dd>
        <dt>Tồn kho hiện tại</dt>
        <dd>
            <strong>{{ \App\Models\Ingredient::fmt($ingredient->current_stock) }} {{ $ingredient->unit }}</strong>
            @if(!$ingredient->is_active)<span class="inv-badge off">Ngừng sử dụng</span>
            @elseif($ingredient->is_out)<span class="inv-badge out">Hết hàng</span>
            @elseif($ingredient->is_low)<span class="inv-badge low">Sắp hết</span>
            @else<span class="inv-badge ok">Đủ hàng</span>@endif
        </dd>
        <dt>Mức tồn tối thiểu</dt>
        <dd>{{ $ingredient->min_stock > 0 ? \App\Models\Ingredient::fmt($ingredient->min_stock) . ' ' . $ingredient->unit : 'Không đặt' }}</dd>
        <dt>Giá nhập tham khảo</dt>
        <dd>{{ $ingredient->reference_price !== null ? number_format($ingredient->reference_price, 0, ',', '.') . 'đ / ' . $ingredient->unit : '—' }}</dd>
        <dt>Ghi chú</dt><dd>{{ $ingredient->note ?: '—' }}</dd>
    </dl>
    <h3 class="inv-title" style="font-size:16px;margin-top:22px">Lịch sử tồn kho gần đây</h3>
<div class="inv-wrap">
    <table class="inv-table">
        <thead><tr><th>Thời gian</th><th>Loại</th><th class="r">Thay đổi</th><th class="r">Tồn sau</th><th>Chứng từ</th></tr></thead>
        <tbody>
        @forelse($movements as $m)
            @php $pos = $m->quantity > 0; @endphp
            <tr>
                <td class="inv-muted">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $m->type_label }}</td>
                <td class="r"><strong style="color:{{ $pos ? '#16A34A' : '#DC2626' }}">{{ $pos ? '+' : '' }}{{ \App\Models\Ingredient::fmt($m->quantity) }}</strong> {{ $ingredient->unit }}</td>
                <td class="r">{{ \App\Models\Ingredient::fmt($m->balance_after) }} {{ $ingredient->unit }}</td>
                <td>
                    @if($m->reference instanceof \App\Models\StockReceipt)
                        <a href="{{ route('admin.inventory.receipts.show', $m->reference) }}">{{ $m->reference->code }}</a>
                    @elseif($m->reference instanceof \App\Models\StockIssue)
                        <a href="{{ route('admin.inventory.issues.show', $m->reference) }}">{{ $m->reference->code }}</a>
                    @else<span class="inv-muted">{{ $m->note ?: '—' }}</span>@endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="inv-empty">Chưa có biến động nào.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<a href="{{ route('admin.inventory.movements.index', ['ingredient_id' => $ingredient->id]) }}" class="btn btn-sm btn-ghost" style="margin-top:8px">Xem toàn bộ sổ kho</a>
</div>
@endsection
