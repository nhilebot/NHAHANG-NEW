@extends('layouts.admin')
@php $title = $recipe ? 'Sửa công thức' : 'Thêm công thức'; @endphp
@section('title', $title)
@section('page_title', $title)
@section('topbar_title', $title)

@section('admin_content')
@include('admin.inventory._style')
@php
    $initial = old('items');
    if ($initial === null) {
        $initial = $recipe
            ? $recipe->items->map(fn ($i) => ['ingredient_id' => $i->ingredient_id, 'quantity' => rtrim(rtrim(number_format((float) $i->quantity, 3, '.', ''), '0'), '.'), 'unit' => $i->unit])->values()->all()
            : [];
    }
    $initial = array_values($initial);
@endphp

<div class="inv-panel inv-form" style="max-width:860px">
    <h2 class="inv-title">{{ $title }}</h2>
    <p class="inv-sub">Nhập lượng nguyên liệu cho <strong>MỘT phần</strong> món.</p>

    @if($errors->any())
        <div class="inv-err" style="margin:12px 0">Vui lòng kiểm tra lại các ô bị báo lỗi bên dưới.</div>
    @endif

    <form method="POST" action="{{ $recipe ? route('admin.inventory.recipes.update', $recipe) : route('admin.inventory.recipes.store') }}">
        @csrf
        @if($recipe) @method('PUT') @endif

        <label for="menu_id">Món ăn</label>
        @if($recipe)
            <div class="inv-readonly"><strong>{{ $recipe->menu->name ?? 'Món đã bị xóa' }}</strong></div>
        @else
            <select id="menu_id" name="menu_id" required>
                <option value="">Chọn món (chỉ hiện món chưa có công thức)</option>
                @foreach($menusAvailable as $m)
                    <option value="{{ $m->id }}" @selected(old('menu_id', $selectedMenuId) == $m->id)>{{ $m->name }}</option>
                @endforeach
            </select>
            @error('menu_id')<div class="inv-err">{{ $message }}</div>@enderror
        @endif

        <label style="margin-top:18px">Nguyên liệu</label>
        @error('items')<div class="inv-err">{{ $message }}</div>@enderror
        <div id="rows"></div>
        <button type="button" id="addRow" class="btn btn-ghost" style="margin-top:8px">+ Thêm nguyên liệu</button>

        <label for="note" style="margin-top:18px">Ghi chú</label>
        <input type="text" id="note" name="note" maxlength="255" value="{{ old('note', $recipe->note ?? '') }}">
        @error('note')<div class="inv-err">{{ $message }}</div>@enderror

        <label style="margin-top:14px;display:flex;gap:8px;align-items:center;font-weight:500">
            <input type="checkbox" name="is_sample" value="1" @checked(old('is_sample', $recipe ? $recipe->is_sample : false))>
            Số liệu minh họa, cần nhà hàng xác nhận
        </label>
        <div class="inv-hint">Bỏ tick khi định lượng đã được bếp trưởng/quản lý xác nhận.</div>

        <div style="margin-top:20px;display:flex;gap:10px">
            <button type="submit" class="btn btn-primary">Lưu công thức</button>
            <a href="{{ $recipe ? route('admin.inventory.recipes.show', $recipe) : route('admin.inventory.recipes.index') }}" class="btn btn-ghost">Hủy</a>
        </div>
    </form>
</div>

<script>
(function () {
    const ING = @json($ingredientData);
    const INITIAL = @json($initial);
    const ERR = @json($errors->getMessages());
    const wrap = document.getElementById('rows');
    let idx = 0;

    function esc(s) { return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

    function unitOptions(id, selected) {
        const a = ING[id] ? ING[id].allowed : [];
        if (!a.length) return '<option value="">—</option>';
        return a.map(u => `<option value="${esc(u)}" ${u === selected ? 'selected' : ''}>${esc(u)}</option>`).join('');
    }
    function chosenIds(except) {
        return [...wrap.querySelectorAll('.ing-sel')].filter(s => s !== except).map(s => s.value).filter(Boolean);
    }
    function refreshDisabled() {
        wrap.querySelectorAll('.ing-sel').forEach(sel => {
            const others = chosenIds(sel);
            sel.querySelectorAll('option').forEach(o => { o.disabled = o.value !== '' && others.includes(o.value); });
        });
    }
    function addRow(data) {
        data = data || {};
        const i = idx++;
        const div = document.createElement('div');
        div.style.cssText = 'display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:8px;margin-bottom:8px;align-items:start';
        const opts = Object.entries(ING).map(([id, v]) =>
            `<option value="${id}" ${String(data.ingredient_id) === id ? 'selected' : ''}>${esc(v.name)} (${esc(v.unit)})${v.active ? '' : ' - ngừng dùng'}</option>`).join('');
        const e = k => (ERR[`items.${i}.${k}`] || []).map(m => `<div class="inv-err">${esc(m)}</div>`).join('');
        div.innerHTML = `
            <div><select class="ing-sel" name="items[${i}][ingredient_id]" required>
                <option value="">Chọn nguyên liệu</option>${opts}</select>${e('ingredient_id')}</div>
            <div><input type="number" name="items[${i}][quantity]" step="0.001" min="0.001" required placeholder="Định lượng" value="${esc(data.quantity)}">${e('quantity')}</div>
            <div><select class="unit-sel" name="items[${i}][unit]" required>${unitOptions(data.ingredient_id, data.unit)}</select>${e('unit')}</div>
            <button type="button" class="btn btn-sm btn-ghost rm" style="color:#DC2626">Xóa</button>`;
        wrap.appendChild(div);
        div.querySelector('.ing-sel').addEventListener('change', function () {
            div.querySelector('.unit-sel').innerHTML = unitOptions(this.value, '');
            refreshDisabled();
        });
        div.querySelector('.rm').addEventListener('click', () => { div.remove(); refreshDisabled(); });
        refreshDisabled();
    }
    document.getElementById('addRow').addEventListener('click', () => addRow());
    (INITIAL.length ? INITIAL : [{}]).forEach(addRow);
})();
</script>
@endsection
