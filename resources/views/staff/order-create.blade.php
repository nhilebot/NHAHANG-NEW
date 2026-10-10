@extends('layouts.admin')
@section('topbar_title', 'Tạo đơn hàng cho ' . ($reservation->table->name ?? 'Bàn '.$reservation->table_id))

@section('admin_content')
<style>
    .menu-card { border:1.5px solid #e2e8f0; border-radius:12px; overflow:hidden; transition:.2s; }
    .menu-card:hover { border-color:#5b3cff; box-shadow:0 4px 16px rgba(91,60,255,.1); }
    .menu-card img { width:100%; height:130px; object-fit:cover; }
    .qty-box { display:flex; align-items:center; gap:8px; justify-content:center; margin-top:8px; }
    .qty-btn { width:28px; height:28px; border-radius:50%; border:1.5px solid #e2e8f0;
               background:#f8fafc; font-size:1rem; cursor:pointer; font-weight:700; }
    .qty-input { width:40px; text-align:center; border:1.5px solid #e2e8f0; border-radius:8px;
                 padding:3px; font-weight:700; }
    .cart-sticky { position:sticky; top:80px; }
    .cart-item-row { display:flex; justify-content:space-between; align-items:center;
                     padding:8px 0; border-bottom:1px solid #f1f5f9; font-size:.88rem; }
    .cat-filter { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; }
    .cat-btn { padding:5px 14px; border-radius:20px; border:1.5px solid #e2e8f0;
               background:#fff; cursor:pointer; font-size:.82rem; font-weight:600; }
    .cat-btn.active { background:#5b3cff; color:#fff; border-color:#5b3cff; }
</style>

<div class="row g-4">
    {{-- CỘT TRÁI: Menu --}}
    <div class="col-lg-8">
        <div class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">🍽️ Chọn món cho {{ $reservation->table->name ?? 'Bàn '.$reservation->table_id }}</div>
                    <div class="small text-muted">Khách: {{ $reservation->full_name }} • {{ $reservation->phone }}</div>
                </div>
                <input type="text" id="search-menu" placeholder="🔍 Tìm món..." 
                    class="form-control" style="width:200px;" oninput="filterMenu()">
            </div>

            {{-- Lọc danh mục --}}
            <div class="cat-filter">
                <button class="cat-btn active" onclick="filterCat('all', this)">Tất cả</button>
                @foreach($categories as $cat)
                    @if($cat)
                    <button class="cat-btn" onclick="filterCat('{{ $cat }}', this)">{{ $cat }}</button>
                    @endif
                @endforeach
            </div>

            <div class="row g-3" id="menu-grid">
                @foreach($menus as $menu)
                <div class="col-6 col-md-4 menu-item" data-cat="{{ $menu->category }}" data-name="{{ strtolower($menu->name) }}">
                    <div class="menu-card">
                        <img src="{{ asset($menu->image) }}" onerror="this.src='https://via.placeholder.com/200x130'">
                        <div style="padding:10px;">
                            <div style="font-weight:700; font-size:.88rem; color:#0f172a;">{{ $menu->name }}</div>
                            <div style="color:#5b3cff; font-weight:700; font-size:.85rem;">{{ number_format($menu->price) }}đ</div>
                            <div class="qty-box">
                                <button type="button" class="qty-btn" onclick="changeQty({{ $menu->id }}, -1)">−</button>
                                <input type="number" class="qty-input" id="qty-{{ $menu->id }}" value="0" min="0"
                                    onchange="updateCart({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }})">
                                <button type="button" class="qty-btn" onclick="changeQty({{ $menu->id }}, 1)">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- CỘT PHẢI: Giỏ hàng --}}
    <div class="col-lg-4">
        <div class="panel cart-sticky">
            <div class="panel-title mb-3">🛒 Đơn hàng</div>

            <div id="cart-list">
                <div class="text-muted text-center py-3" id="empty-cart">Chưa chọn món nào</div>
            </div>

            <div style="border-top:2px solid #f1f5f9; margin:12px 0; padding-top:12px;">
                <div style="display:flex; justify-content:space-between; font-weight:700; font-size:1rem;">
                    <span>Tổng cộng:</span>
                    <span id="total-price" style="color:#5b3cff;">0đ</span>
                </div>
            </div>

            {{-- Ghi chú --}}
            <textarea id="special-note" placeholder="📝 Ghi chú đặc biệt (ít cay, không hành...)"
                style="width:100%; border:1.5px solid #e2e8f0; border-radius:9px; padding:10px;
                       font-size:.85rem; resize:none; height:70px; margin-bottom:12px;"></textarea>

            <form id="order-form" action="{{ route('staff.order.store', $reservation->id) }}" method="POST">
                @csrf
                <div id="hidden-inputs"></div>
                <input type="hidden" name="notes" id="notes-input">
                <button type="submit" onclick="prepareSubmit()"
                    class="btn btn-primary w-100 mb-2" id="save-btn" disabled>
                    💾 Lưu đơn (chưa gửi bếp)
                </button>
            </form>

            <form action="{{ route('staff.order.sendToKitchen', $reservation->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-warning w-100 text-white fw-bold"
                    {{ empty($reservation->cart_data) ? 'disabled' : '' }}>
                    🔥 Gửi bếp ngay
                </button>
            </form>

            {{-- Món hiện có trong đơn --}}
            @if(!empty($reservation->cart_data))
            <div style="margin-top:16px; padding:12px; background:#f8fafc; border-radius:9px;">
                <div style="font-size:.78rem; font-weight:700; color:#64748b; margin-bottom:8px;">ĐÃ CÓ TRONG ĐƠN:</div>
                @foreach($reservation->cart_data as $item)
                <div style="font-size:.83rem; display:flex; justify-content:space-between;">
                    <span>{{ $item['quantity'] }}x {{ $item['name'] }}</span>
                    <span style="color:#5b3cff;">{{ number_format($item['price'] * $item['quantity']) }}đ</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<script>
var cart = {};

function changeQty(id, delta) {
    var input = document.getElementById('qty-' + id);
    var val = Math.max(0, (parseInt(input.value) || 0) + delta);
    input.value = val;
    input.dispatchEvent(new Event('change'));
}

function updateCart(id, name, price) {
    var qty = parseInt(document.getElementById('qty-' + id).value) || 0;
    if (qty > 0) {
        cart[id] = { name: name, price: price, quantity: qty };
    } else {
        delete cart[id];
    }
    renderCart();
}

function renderCart() {
    var list = document.getElementById('cart-list');
    var empty = document.getElementById('empty-cart');
    var total = 0;
    var html = '';
    var hasItems = Object.keys(cart).length > 0;

    if (hasItems) {
        for (var id in cart) {
            var item = cart[id];
            var sub = item.price * item.quantity;
            total += sub;
            html += '<div class="cart-item-row">' +
                '<span>' + item.quantity + 'x ' + item.name + '</span>' +
                '<span style="color:#5b3cff;font-weight:700;">' + sub.toLocaleString('vi') + 'đ</span>' +
                '</div>';
        }
        list.innerHTML = html;
        document.getElementById('save-btn').disabled = false;
    } else {
        list.innerHTML = '<div class="text-muted text-center py-3">Chưa chọn món nào</div>';
        document.getElementById('save-btn').disabled = true;
    }
    document.getElementById('total-price').innerText = total.toLocaleString('vi') + 'đ';
}

function prepareSubmit() {
    var hidden = document.getElementById('hidden-inputs');
    hidden.innerHTML = '';
    var i = 0;
    for (var id in cart) {
        hidden.innerHTML +=
            '<input type="hidden" name="items[' + i + '][menu_id]" value="' + id + '">' +
            '<input type="hidden" name="items[' + i + '][quantity]" value="' + cart[id].quantity + '">';
        i++;
    }
    document.getElementById('notes-input').value = document.getElementById('special-note').value;
}

function filterCat(cat, btn) {
    document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.menu-item').forEach(el => {
        el.style.display = (cat === 'all' || el.dataset.cat === cat) ? '' : 'none';
    });
}

function filterMenu() {
    var q = document.getElementById('search-menu').value.toLowerCase();
    document.querySelectorAll('.menu-item').forEach(el => {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
}
</script>
@endsection
