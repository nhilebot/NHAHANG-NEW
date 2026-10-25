@extends('layouts.admin')
@section('topbar_title', 'Sửa đơn — ' . ($reservation->table->name ?? 'Bàn '.$reservation->table_id))

@section('admin_content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/staff/order-edit.css') }}?v=1">
@endpush

<div class="row g-4">
    <div class="col-lg-8">
        <div class="panel">
            <div class="panel-title mb-3">✏️ Sửa đơn — {{ $reservation->table->name ?? 'Bàn '.$reservation->table_id }}</div>

            <form action="{{ route('staff.order.update', $reservation->id) }}" method="POST" id="edit-form">
                @csrf
                <div id="hidden-inputs"></div>
                @foreach($menus as $menu)
                @php
                    $existing = collect($reservation->cart_data ?? [])->firstWhere('id', $menu->id);
                    $qty = $existing['quantity'] ?? 0;
                @endphp
                <div class="menu-row">
                    <div>
                        <div style="font-weight:600;">{{ $menu->name }}</div>
                        <div style="color:#5b3cff; font-size:.85rem;">{{ number_format($menu->price) }}đ</div>
                    </div>
                    <div class="qty-box">
                        <button type="button" class="qty-btn" onclick="changeQty({{ $menu->id }}, -1)">−</button>
                        <input type="number" class="qty-inp" id="qty-{{ $menu->id }}"
                            value="{{ $qty }}" min="0"
                            data-id="{{ $menu->id }}" data-name="{{ $menu->name }}" data-price="{{ $menu->price }}"
                            onchange="renderTotal()">
                        <button type="button" class="qty-btn" onclick="changeQty({{ $menu->id }}, 1)">+</button>
                    </div>
                </div>
                @endforeach

                <div style="margin-top:16px; text-align:right;">
                    <strong>Tổng: <span id="total" style="color:#5b3cff;">0đ</span></strong>
                </div>

                <button type="submit" onclick="prepareSubmit()" class="btn btn-primary w-100 mt-3">
                    💾 Cập nhật đơn
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function changeQty(id, d) {
    var inp = document.getElementById('qty-'+id);
    inp.value = Math.max(0, (parseInt(inp.value)||0) + d);
    renderTotal();
}
function renderTotal() {
    var total = 0;
    document.querySelectorAll('.qty-inp').forEach(function(inp) {
        total += (parseInt(inp.value)||0) * parseFloat(inp.dataset.price);
    });
    document.getElementById('total').innerText = total.toLocaleString('vi') + 'đ';
}
function prepareSubmit() {
    var hidden = document.getElementById('hidden-inputs');
    hidden.innerHTML = '';
    var i = 0;
    document.querySelectorAll('.qty-inp').forEach(function(inp) {
        var qty = parseInt(inp.value)||0;
        if (qty > 0) {
            hidden.innerHTML +=
                '<input type="hidden" name="items['+i+'][menu_id]" value="'+inp.dataset.id+'">' +
                '<input type="hidden" name="items['+i+'][quantity]" value="'+qty+'">';
            i++;
        }
    });
}
renderTotal();
</script>
@endsection
