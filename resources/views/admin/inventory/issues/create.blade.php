@extends('layouts.admin')
@section('title', 'Lập phiếu xuất kho')
@section('page_title', 'Lập phiếu xuất kho')
@section('topbar_title', 'Lập phiếu xuất kho')

@section('admin_content')
@include('admin.inventory._style')

@php
    $oldItems = old('items', [['ingredient_id' => '', 'quantity' => '']]);
    $catalog  = $ingredients->mapWithKeys(fn ($i) => [$i->id => [
        'unit'  => $i->unit,
        'stock' => (float) $i->current_stock,
    ]]);
@endphp

<div class="inv-panel inv-form" style="max-width:900px">
    <h2 class="inv-title">Lập phiếu xuất kho</h2>

    @error('items')<div class="inv-err" style="margin-top:10px">{{ $message }}</div>@enderror

    <form method="POST" action="{{ route('admin.inventory.issues.store') }}">
        @csrf

        <div class="row2">
            <div>
                <label for="reason">Lý do xuất</label>
                <select id="reason" name="reason" required>
                    @foreach(\App\Models\StockIssue::REASONS as $k => $label)
                        <option value="{{ $k }}" @selected(old('reason', 'kitchen') === $k)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('reason')<div class="inv-err">{{ $message }}</div>@enderror
            </div>
            <div>
                <label for="issued_at">Ngày xuất</label>
                <input type="date" id="issued_at" name="issued_at" value="{{ old('issued_at', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required>
                @error('issued_at')<div class="inv-err">{{ $message }}</div>@enderror
            </div>
        </div>

        <label>Nguyên liệu xuất</label>
        <div class="inv-wrap">
            <table class="inv-table">
                <thead>
                    <tr><th style="min-width:240px">Nguyên liệu</th><th class="r" style="width:130px">Đang tồn</th><th class="r" style="width:150px">Số lượng xuất</th><th style="width:60px">ĐVT</th><th style="width:50px"></th></tr>
                </thead>
                <tbody id="itemsBody"></tbody>
            </table>
        </div>
        <button type="button" class="btn btn-ghost" id="addRow" style="margin-top:8px">+ Thêm dòng</button>

        <label for="note" style="margin-top:14px">Ghi chú <span class="inv-muted" id="noteHint">(bắt buộc nếu không phải "Dùng trong bếp")</span></label>
        <input type="text" id="note" name="note" value="{{ old('note') }}" maxlength="255" placeholder="VD: Cua chết do mất điện tủ đông">
        @error('note')<div class="inv-err">{{ $message }}</div>@enderror

        <div class="inv-form-actions">
            <button type="submit" class="btn btn-primary">Lưu phiếu & trừ kho</button>
            <a href="{{ route('admin.inventory.issues.index') }}" class="btn btn-ghost">Hủy</a>
        </div>
        <div class="inv-hint">Sau khi lưu, phiếu không sửa/xóa được. Xuất nhầm thì dùng chức năng kiểm kê để chỉnh lại.</div>
    </form>
</div>

<script>
(function () {
    const catalog = @json($catalog);
    const options = @json($ingredients->map(fn ($i) => ['id' => $i->id, 'label' => $i->code . ' - ' . $i->name]));
    const initial = @json(array_values($oldItems));
    const body = document.getElementById('itemsBody');
    const fmt = n => (Math.round(n * 1000) / 1000).toLocaleString('vi-VN');
    let idx = 0;

    function row(data) {
        const i = idx++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><select name="items[${i}][ingredient_id]" required>
                <option value="">Chọn nguyên liệu</option>
                ${options.map(o => `<option value="${o.id}">${o.label}</option>`).join('')}
            </select></td>
            <td class="r stock inv-muted">—</td>
            <td><input type="number" name="items[${i}][quantity]" step="0.001" min="0.001" required style="text-align:right"></td>
            <td class="inv-muted unit">—</td>
            <td><button type="button" class="btn btn-sm btn-ghost rm" style="color:#DC2626" title="Xóa dòng">✕</button></td>`;
        const sel = tr.querySelector('select'), qty = tr.querySelector('[name$="[quantity]"]');

        const check = () => {
            const c = catalog[sel.value];
            // cảnh báo ngay trên form; server vẫn kiểm tra lại khi lưu
            qty.style.borderColor = (c && parseFloat(qty.value) > c.stock) ? '#DC2626' : '';
            qty.title = (c && parseFloat(qty.value) > c.stock) ? 'Vượt quá số đang tồn' : '';
        };
        sel.addEventListener('change', () => {
            const c = catalog[sel.value];
            tr.querySelector('.stock').textContent = c ? fmt(c.stock) : '—';
            tr.querySelector('.unit').textContent  = c ? c.unit : '—';
            if (c) qty.max = c.stock; else qty.removeAttribute('max');
            check();
        });
        qty.addEventListener('input', check);
        tr.querySelector('.rm').addEventListener('click', () => { if (body.children.length > 1) tr.remove(); });

        if (data) { sel.value = data.ingredient_id || ''; qty.value = data.quantity || ''; sel.dispatchEvent(new Event('change')); }
        body.appendChild(tr);
    }

    document.getElementById('addRow').addEventListener('click', () => row());
    initial.forEach(d => row(d));
})();
</script>
@endsection
