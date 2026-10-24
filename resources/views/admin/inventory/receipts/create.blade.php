@extends('layouts.admin')
@section('title', 'Lập phiếu nhập kho')
@section('page_title', 'Lập phiếu nhập kho')
@section('topbar_title', 'Lập phiếu nhập kho')

@section('admin_content')
@include('admin.inventory._style')

@php
    $oldItems = old('items', [['ingredient_id' => '', 'quantity' => '', 'unit_cost' => '']]);
    $catalog  = $ingredients->mapWithKeys(fn ($i) => [$i->id => [
        'unit'  => $i->unit,
        'price' => $i->reference_price !== null ? (int) $i->reference_price : 0,
    ]]);
@endphp

<div class="inv-panel inv-form" style="max-width:980px">
    <h2 class="inv-title">Lập phiếu nhập kho</h2>

    @error('items')<div class="inv-err" style="margin-top:10px">{{ $message }}</div>@enderror

    <form method="POST" action="{{ route('admin.inventory.receipts.store') }}" id="receiptForm">
        @csrf

        <div class="row2">
            <div>
                <label for="supplier_id">Nhà cung cấp</label>
                <select id="supplier_id" name="supplier_id">
                    <option value="">Không chọn / mua lẻ</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" @selected(old('supplier_id') == $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
                @error('supplier_id')<div class="inv-err">{{ $message }}</div>@enderror
            </div>
            <div>
                <label for="received_at">Ngày nhập</label>
                <input type="date" id="received_at" name="received_at" value="{{ old('received_at', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required>
                @error('received_at')<div class="inv-err">{{ $message }}</div>@enderror
            </div>
        </div>

        <label>Nguyên liệu nhập</label>
        <div class="inv-wrap">
            <table class="inv-table" id="itemsTable">
                <thead>
                    <tr><th style="min-width:240px">Nguyên liệu</th><th class="r" style="width:130px">Số lượng</th><th style="width:60px">ĐVT</th><th class="r" style="width:150px">Đơn giá nhập (đ)</th><th class="r" style="width:140px">Thành tiền</th><th style="width:50px"></th></tr>
                </thead>
                <tbody id="itemsBody"></tbody>
                <tfoot>
                    <tr><td colspan="4" class="r"><strong>Tổng cộng</strong></td><td class="r"><strong id="grandTotal">0đ</strong></td><td></td></tr>
                </tfoot>
            </table>
        </div>
        <button type="button" class="btn btn-ghost" id="addRow" style="margin-top:8px">+ Thêm dòng</button>

        <label for="note" style="margin-top:14px">Ghi chú</label>
        <input type="text" id="note" name="note" value="{{ old('note') }}" maxlength="255">

        <label class="inv-check">
            <input type="checkbox" name="update_price" value="1" @checked(old('update_price', true))>
            Cập nhật "Giá nhập tham khảo" của nguyên liệu theo đơn giá trong phiếu này
        </label>

        <div class="inv-form-actions">
            <button type="submit" class="btn btn-primary">Lưu phiếu & nhập kho</button>
            <a href="{{ route('admin.inventory.receipts.index') }}" class="btn btn-ghost">Hủy</a>
        </div>
        <div class="inv-hint">Sau khi lưu, phiếu không sửa/xóa được để lịch sử kho luôn đúng. Nhập sai thì lập phiếu điều chỉnh (kiểm kê).</div>
    </form>
</div>

<script>
(function () {
    const catalog = @json($catalog);
    const options = @json($ingredients->map(fn ($i) => ['id' => $i->id, 'label' => $i->code . ' - ' . $i->name]));
    const initial = @json(array_values($oldItems));
    const body = document.getElementById('itemsBody');
    const fmt = n => Math.round(n).toLocaleString('vi-VN') + 'đ';
    let idx = 0;

    function row(data) {
        const i = idx++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><select name="items[${i}][ingredient_id]" required>
                <option value="">Chọn nguyên liệu</option>
                ${options.map(o => `<option value="${o.id}">${o.label}</option>`).join('')}
            </select></td>
            <td><input type="number" name="items[${i}][quantity]" step="0.001" min="0.001" required style="text-align:right"></td>
            <td class="inv-muted unit">—</td>
            <td><input type="number" name="items[${i}][unit_cost]" step="1" min="0" required style="text-align:right"></td>
            <td class="r line">0đ</td>
            <td><button type="button" class="btn btn-sm btn-ghost rm" style="color:#DC2626" title="Xóa dòng">✕</button></td>`;
        const sel = tr.querySelector('select'), qty = tr.querySelector('[name$="[quantity]"]'), cost = tr.querySelector('[name$="[unit_cost]"]');

        const recalc = () => {
            tr.querySelector('.line').textContent = fmt((parseFloat(qty.value) || 0) * (parseFloat(cost.value) || 0));
            total();
        };
        sel.addEventListener('change', () => {
            const c = catalog[sel.value];
            tr.querySelector('.unit').textContent = c ? c.unit : '—';
            if (c && !cost.value) cost.value = c.price || '';   // gợi ý giá tham khảo
            recalc();
        });
        qty.addEventListener('input', recalc);
        cost.addEventListener('input', recalc);
        tr.querySelector('.rm').addEventListener('click', () => {
            if (body.children.length > 1) { tr.remove(); total(); }
        });

        if (data) { sel.value = data.ingredient_id || ''; qty.value = data.quantity || ''; cost.value = data.unit_cost || ''; sel.dispatchEvent(new Event('change')); }
        body.appendChild(tr);
    }

    function total() {
        let t = 0;
        body.querySelectorAll('tr').forEach(tr => {
            t += (parseFloat(tr.querySelector('[name$="[quantity]"]').value) || 0) * (parseFloat(tr.querySelector('[name$="[unit_cost]"]').value) || 0);
        });
        document.getElementById('grandTotal').textContent = fmt(t);
    }

    document.getElementById('addRow').addEventListener('click', () => row());
    initial.forEach(d => row(d));
    total();
})();
</script>
@endsection
