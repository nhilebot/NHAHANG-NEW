@extends('layouts.admin')
@section('title', 'Chỉnh sửa Món - Staff')

@section('admin_content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap');

    * { font-family: 'Be Vietnam Pro', sans-serif; }

    :root {
        --amber: #f59e0b;
        --amber-light: #fef3c7;
        --amber-dark: #d97706;
        --red: #ef4444;
        --green: #10b981;
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-400: #94a3b8;
        --slate-600: #475569;
        --slate-800: #1e293b;
        --slate-900: #0f172a;
        --white: #ffffff;
        --shadow-sm: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.05);
        --shadow-md: 0 4px 16px rgba(0,0,0,.10);
        --shadow-lg: 0 10px 40px rgba(0,0,0,.14);
    }

    .ei-wrapper {
        background: var(--slate-50);
        min-height: 100vh;
        padding: 28px 24px;
    }

    /* ── Header ── */
    .ei-header {
        background: var(--white);
        border-radius: 18px;
        padding: 22px 28px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        border-left: 5px solid var(--amber);
    }
    .ei-header-left h4 {
        font-size: 1.15rem; font-weight: 800;
        color: var(--slate-900); margin: 0 0 4px;
    }
    .ei-header-left p { margin: 0; color: var(--slate-400); font-size: .82rem; }
    .ei-meta-pill {
        display: inline-flex; align-items: center; gap: 6px;
        background: var(--amber-light); color: var(--amber-dark);
        font-weight: 700; font-size: .78rem;
        padding: 5px 12px; border-radius: 50px;
    }
    .ei-total-pill {
        display: inline-flex; align-items: center; gap: 6px;
        background: #fef2f2; color: var(--red);
        font-weight: 800; font-size: .9rem;
        padding: 6px 16px; border-radius: 50px;
    }

    /* ── Layout ── */
    .ei-grid { display: grid; grid-template-columns: 1fr 340px; gap: 20px; }
    @media(max-width:992px){ .ei-grid{ grid-template-columns:1fr; } }

    .ei-panel {
        background: var(--white);
        border-radius: 18px;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .ei-panel-header {
        padding: 16px 22px;
        border-bottom: 1px solid var(--slate-100);
        font-weight: 700; font-size: .9rem;
        color: var(--slate-800);
        display: flex; align-items: center; gap: 8px;
    }

    /* ── Item Card ── */
    .item-card {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 20px;
        border-bottom: 1px solid var(--slate-100);
        transition: background .15s;
        animation: slideIn .25s ease;
    }
    .item-card:hover { background: var(--slate-50); }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .item-img {
        width: 72px; height: 72px;
        border-radius: 12px; object-fit: cover;
        flex-shrink: 0;
        box-shadow: var(--shadow-sm);
        background: var(--slate-100);
    }
    .item-img-placeholder {
        width: 72px; height: 72px;
        border-radius: 12px;
        background: var(--slate-100);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem; flex-shrink: 0;
    }

    .item-info { flex: 1; min-width: 0; }
    .item-name { font-weight: 700; font-size: .9rem; color: var(--slate-900); margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .item-price { font-size: .78rem; color: var(--amber-dark); font-weight: 600; }
    .item-subtotal { font-size: .85rem; font-weight: 800; color: var(--red); white-space: nowrap; }

    /* Status Badge cho từng món phục vụ KDS */
    .chef-status-badge {
        font-size: 0.72rem;
        padding: 2px 8px;
        border-radius: 20px;
        font-weight: 600;
        display: inline-block;
        margin-top: 4px;
    }
    .status-pending { background-color: #f3f4f6; color: #4b5563; }
    .status-cooking { background-color: #fef3c7; color: #d97706; }
    .status-done { background-color: #d1fae5; color: #065f46; }
    .status-new { background-color: #e0f2fe; color: #0369a1; }

    /* ── Qty Control ── */
    .qty-control {
        display: flex; align-items: center; gap: 0;
        border: 1.5px solid var(--slate-200);
        border-radius: 10px; overflow: hidden;
        flex-shrink: 0;
    }
    .qty-control button {
        width: 32px; height: 32px;
        background: var(--slate-100); border: none;
        font-size: 1rem; font-weight: 700;
        color: var(--slate-600); cursor: pointer;
        transition: background .15s;
    }
    .qty-control button:hover { background: var(--slate-200); }
    .qty-control button:disabled { background: var(--slate-100); color: var(--slate-400); cursor: not-allowed; }
    .qty-control input {
        width: 42px; height: 32px;
        border: none; border-left: 1.5px solid var(--slate-200); border-right: 1.5px solid var(--slate-200);
        text-align: center; font-weight: 700; font-size: .88rem;
        color: var(--slate-900); outline: none;
        background: var(--white);
    }
    .qty-control input:readonly { background: var(--slate-50); color: var(--slate-600); }
    .qty-control input::-webkit-inner-spin-button,
    .qty-control input::-webkit-outer-spin-button { -webkit-appearance: none; }

    .btn-remove {
        width: 32px; height: 32px; border-radius: 8px;
        border: none; background: #fef2f2; color: var(--red);
        font-size: .9rem; cursor: pointer; flex-shrink: 0;
        transition: background .15s, transform .15s;
        display: flex; align-items: center; justify-content: center;
    }
    .btn-remove:hover:not(:disabled) { background: #fee2e2; transform: scale(1.1); }
    .btn-remove:disabled { background: var(--slate-100); color: var(--slate-400); cursor: not-allowed; transform: none; }

    /* ── Empty state ── */
    .empty-state { padding: 48px 20px; text-align: center; color: var(--slate-400); }
    .empty-state .icon { font-size: 3rem; margin-bottom: 12px; }
    .empty-state p { font-size: .88rem; }

    /* ── Right Panel: Add menu ── */
    .add-panel { position: sticky; top: 80px; }
    .menu-select-wrap { padding: 18px 20px; }
    .search-input {
        width: 100%; border: 1.5px solid var(--slate-200);
        border-radius: 10px; padding: 9px 14px;
        font-size: .85rem; outline: none;
        transition: border-color .2s;
        margin-bottom: 10px;
    }
    .search-input:focus { border-color: var(--amber); }

    .menu-scroll {
        max-height: 320px; overflow-y: auto;
        border: 1.5px solid var(--slate-200);
        border-radius: 12px;
    }
    .menu-scroll::-webkit-scrollbar { width: 4px; }
    .menu-scroll::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 4px; }

    .menu-option {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 14px; cursor: pointer;
        border-bottom: 1px solid var(--slate-100);
        transition: background .15s;
    }
    .menu-option:last-child { border-bottom: none; }
    .menu-option:hover { background: var(--amber-light); }
    .menu-option.selected { background: var(--amber-light); }
    .menu-opt-img {
        width: 44px; height: 44px; border-radius: 8px;
        object-fit: cover; flex-shrink: 0;
        background: var(--slate-100);
    }
    .menu-opt-img-placeholder {
        width: 44px; height: 44px; border-radius: 8px;
        background: var(--slate-100); display: flex;
        align-items: center; justify-content: center; font-size: 1.2rem;
    }
    .menu-opt-name { font-weight: 600; font-size: .83rem; color: var(--slate-900); }
    .menu-opt-price { font-size: .75rem; color: var(--amber-dark); font-weight: 700; }

    .add-qty-row {
        display: flex; align-items: center; gap: 10px;
        padding: 14px 20px; border-top: 1px solid var(--slate-100);
    }
    .add-qty-row label { font-size: .8rem; color: var(--slate-600); font-weight: 600; flex-shrink: 0; }

    .btn-add-item {
        width: 100%; padding: 12px;
        background: linear-gradient(135deg, var(--amber), var(--amber-dark));
        color: var(--white); border: none; border-radius: 12px;
        font-weight: 700; font-size: .88rem; cursor: pointer;
        margin: 0 20px 16px; width: calc(100% - 40px);
        transition: opacity .2s, transform .15s;
        box-shadow: 0 4px 12px rgba(245,158,11,.35);
    }
    .btn-add-item:hover { opacity: .92; transform: translateY(-1px); }

    /* ── Summary ── */
    .summary-box {
        margin: 0 20px 20px;
        background: var(--slate-50);
        border-radius: 14px; padding: 16px;
        border: 1.5px solid var(--slate-200);
    }
    .summary-row {
        display: flex; justify-content: space-between;
        font-size: .82rem; color: var(--slate-600);
        margin-bottom: 8px;
    }
    .summary-row.total {
        font-size: .95rem; font-weight: 800;
        color: var(--slate-900); border-top: 1.5px solid var(--slate-200);
        padding-top: 10px; margin-top: 4px;
    }
    .summary-row.total span:last-child { color: var(--red); }

    /* ── Footer buttons ── */
    .ei-footer {
        background: var(--white); border-radius: 18px;
        padding: 18px 24px; margin-top: 20px;
        box-shadow: var(--shadow-sm);
        display: flex; gap: 12px;
    }
    .btn-save {
        flex: 1; padding: 13px 20px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: var(--white); border: none; border-radius: 12px;
        font-weight: 700; font-size: .9rem; cursor: pointer;
        box-shadow: 0 4px 14px rgba(16,185,129,.3);
        transition: opacity .2s, transform .15s;
    }
    .btn-save:hover { opacity: .9; transform: translateY(-1px); }
    .btn-cancel {
        padding: 13px 24px;
        background: var(--slate-100); color: var(--slate-600);
        border: none; border-radius: 12px;
        font-weight: 600; font-size: .9rem; cursor: pointer;
        text-decoration: none; display: flex; align-items: center;
        transition: background .15s;
    }
    .btn-cancel:hover { background: var(--slate-200); color: var(--slate-800); }

    .ei-alert {
        padding: 12px 18px; border-radius: 12px;
        font-size: .84rem; font-weight: 600;
        margin-bottom: 16px;
        display: flex; align-items: center; gap: 8px;
    }
    .ei-alert.success { background: #ecfdf5; color: #065f46; }
    .ei-alert.error   { background: #fef2f2; color: #991b1b; }
</style>

<form action="{{ route('staff.reservations.update_items', $reservation->id) }}" method="POST" id="main-form">
    @csrf

    <div class="ei-wrapper">

        {{-- ── HEADER ── --}}
        <div class="ei-header">
            <div class="ei-header-left">
                <h4>✏️ Chỉnh sửa đơn món & Gọi thêm món mới</h4>
                <p>
                    <span class="ei-meta-pill">🪑 {{ $reservation->table->name ?? 'Bàn '.$reservation->table_id }}</span>
                    &nbsp;
                    <span class="ei-meta-pill">👤 {{ $reservation->full_name }}</span>
                    &nbsp;
                    <span class="ei-meta-pill">🕐 {{ $reservation->reservation_time }}</span>
                </p>
            </div>
            <div>
                <div style="font-size:.72rem; color:var(--slate-400); margin-bottom:4px; text-align:right;">Tổng tiền hóa đơn</div>
                <div class="ei-total-pill">💰 <span id="header-total">{{ number_format($reservation->total_price, 0) }}</span> đ</div>
            </div>
        </div>

        @if(session('success'))
            <div class="ei-alert success">✅ {{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="ei-alert error">❌ {{ $errors->first() }}</div>
        @endif

        {{-- ── MAIN GRID ── --}}
        <div class="ei-grid">

            {{-- LEFT: Danh sách món ăn tại bàn --}}
            <div class="ei-panel">
                <div class="ei-panel-header">
                    <td>🍽️ Danh sách món ăn tại bàn</td>
                    <span style="margin-left:auto; font-size:.75rem; color:var(--slate-400);">
                        <span id="item-count">{{ count($cartItems) }}</span> món •
                        tổng SL: <span id="total-qty">{{ array_sum(array_column($cartItems, 'quantity')) }}</span>
                    </span>
                </div>

                <div id="items-container">
                    @forelse($cartItems as $index => $item)
                    @php
                        // Đọc trạng thái làm món từ bếp thông qua mảng trạng thái hệ thống
                        $chefStatus = $reservation->chef_statuses[$item['id']] ?? 'pending';
                        // Nếu bếp đang nấu hoặc đã nấu xong thì KHÔNG cho phép sửa giảm hoặc xóa món
                        $isLocked = in_array($chefStatus, ['cooking', 'cooking_done', 'done', 'ready', 'served']);
                    @endphp
                    <div class="item-card" data-item-id="{{ $item['id'] }}" data-price="{{ $item['price'] }}" data-locked="{{ $isLocked ? 'true' : 'false' }}">

                        {{-- Ảnh món ăn --}}
                        @if(!empty($item['image']))
                            <img class="item-img"
                                 src="{{ Str::startsWith($item['image'], 'http') ? $item['image'] : asset($item['image']) }}"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                 alt="{{ $item['name'] }}">
                            <div class="item-img-placeholder" style="display:none;">🍜</div>
                        @else
                            <div class="item-img-placeholder">🍜</div>
                        @endif

                      {{-- Thông tin món ăn & Trạng thái làm món --}}
<div class="item-info">
    <div class="item-name" title="{{ $item['name'] }}">{{ $item['name'] }}</div>
    <div class="item-price">{{ number_format($item['price'], 0) }}đ / phần</div>
    
    @php
        // Đọc trạng thái tổng của hóa đơn/bàn ăn
        $orderStatus = $reservation->status;
    @endphp

    {{-- Ưu tiên 1: Nếu trạng thái tổng của bàn là đã phục vụ xong tất cả --}}
    @if($orderStatus === 'served' || in_array($chefStatus, ['ready', 'done', 'cooking_done', 'served']))
        <span class="chef-status-badge status-done" style="background-color: #d1fae5 !important; color: #065f46 !important;">✅ Đã hoàn thành </span>
    
    {{-- Ưu tiên 2: Nếu bếp đang nấu hoặc trạng thái tổng của bàn là đang nấu --}}
    @elseif($orderStatus === 'serving' || $chefStatus === 'cooking')
        <span class="chef-status-badge status-cooking" style="background-color: #fef3c7 !important; color: #d97706 !important;">⏳ Bếp đang chế biến</span>
    
    {{-- Ưu tiên 3: Các trường hợp còn lại (món mới gọi thêm hoặc đang chờ xử lý) --}}
    @else
        <span class="chef-status-badge status-pending" style="background-color: #f3f4f6 !important; color: #4b5563 !important;">⏳ Chờ xử lý</span>
    @endif
</div>

                        {{-- Hidden inputs gửi lên Controller --}}
                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item['id'] }}">
                        <input type="hidden" name="items[{{ $index }}][quantity]" class="qty-hidden" value="{{ $item['quantity'] }}">
                        {{-- Input lưu số lượng gốc ban đầu để Controller tính toán lượng tăng thêm --}}
                        <input type="hidden" name="items[{{ $index }}][old_quantity]" value="{{ $item['quantity'] }}">

                        {{-- Điều khiển số lượng --}}
                        <div class="qty-control">
                            <button type="button" class="btn-minus" {{ $isLocked ? 'disabled' : '' }}>−</button>
                            <input type="number" class="qty-display" value="{{ $item['quantity'] }}" min="{{ $isLocked ? $item['quantity'] : 1 }}" {{ $isLocked ? 'readonly' : '' }}>
                            <button type="button" class="btn-plus">+</button>
                        </div>

                        {{-- Thành tiền từng món --}}
                        <div class="item-subtotal">{{ number_format($item['price'] * $item['quantity'], 0) }}đ</div>

                        {{-- Nút xóa món ăn --}}
                        <button type="button" class="btn-remove" title="{{ $isLocked ? 'Không thể xóa món ăn đã chế biến' : 'Xóa món' }}" {{ $isLocked ? 'disabled' : '' }}>
                            {{ $isLocked ? '🔒' : '🗑' }}
                        </button>
                    </div>
                    @empty
                    <div class="empty-state">
                        <div class="icon">🛒</div>
                        <p>Bàn ăn chưa có món nào.<br>Thêm món gọi từ danh sách bên phải.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- RIGHT: Menu chọn gọi thêm món mới --}}
            <div class="add-panel">
                <div class="ei-panel">
                    <div class="ei-panel-header">➕ Chọn món gọi thêm</div>

                    <div class="menu-select-wrap">
                        <input type="text" class="search-input" id="menu-search" placeholder="🔍 Tìm tên món...">
                        <div class="menu-scroll" id="menu-list">
                            @foreach($menus as $menu)
                            <div class="menu-option"
                                 data-id="{{ $menu->id }}"
                                 data-name="{{ $menu->name }}"
                                 data-price="{{ $menu->price }}"
                                 data-image="{{ $menu->image ?? '' }}"
                                 data-search="{{ strtolower($menu->name) }}">
                                @if(!empty($menu->image))
                                    <img class="menu-opt-img"
                                         src="{{ Str::startsWith($menu->image, 'http') ? $menu->image : asset($menu->image) }}"
                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                                         alt="{{ $menu->name }}">
                                    <div class="menu-opt-img-placeholder" style="display:none;">🍜</div>
                                @else
                                    <div class="menu-opt-img-placeholder">🍜</div>
                                @endif
                                <div>
                                    <div class="menu-opt-name">{{ $menu->name }}</div>
                                    <div class="menu-opt-price">{{ number_format($menu->price, 0) }}đ</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="add-qty-row">
                        <label>Số lượng gọi thêm:</label>
                        <div class="qty-control" style="flex:1;">
                            <button type="button" id="add-minus">−</button>
                            <input type="number" id="add-qty" value="1" min="1">
                            <button type="button" id="add-plus">+</button>
                        </div>
                    </div>

                    <button type="button" class="btn-add-item" id="btn-add">
                        ➕ Thêm vào đơn của bàn
                    </button>

                    <div class="summary-box">
                        <div class="summary-row">
                            <span>Số loại món</span>
                            <strong id="s-count">{{ count($cartItems) }}</strong>
                        </div>
                        <div class="summary-row">
                            <span>Tổng số lượng</span>
                            <strong id="s-qty">{{ array_sum(array_column($cartItems, 'quantity')) }}</strong>
                        </div>
                        <div class="summary-row total">
                            <span>Tổng cộng</span>
                            <span id="s-total">{{ number_format($reservation->total_price, 0) }}đ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── FOOTER HÀNH ĐỘNG ── --}}
        <div class="ei-footer">
            <a href="{{ route('admin.index') }}" class="btn-cancel"> quay lại</a>
            <button type="submit" class="btn-save">
                ✅ Xác nhận lưu và chuyển xuống bếp
            </button>
        </div>
    </div>
</form>

<script>
(function() {
    let nextIndex = {{ count($cartItems) }};
    let selectedMenu = null;

    function formatVND(n) {
        return n.toLocaleString('vi-VN') + 'đ';
    }

    function recalc() {
        const cards = document.querySelectorAll('.item-card');
        let total = 0, count = 0, qty = 0;
        cards.forEach(card => {
            const price = parseInt(card.dataset.price);
            const q     = parseInt(card.querySelector('.qty-display').value) || 0;
            const sub   = price * q;
            total += sub; count++; qty += q;
            card.querySelector('.item-subtotal').textContent = formatVND(sub);
            card.querySelector('.qty-hidden').value = q;
        });
        document.getElementById('item-count').textContent  = count;
        document.getElementById('total-qty').textContent   = qty;
        document.getElementById('header-total').textContent = total.toLocaleString('vi-VN');
        document.getElementById('s-count').textContent = count;
        document.getElementById('s-qty').textContent   = qty;
        document.getElementById('s-total').textContent = formatVND(total);
    }

    // Xử lý sự kiện tăng giảm số lượng giỏ hàng bàn ăn
    document.getElementById('items-container').addEventListener('click', function(e) {
        const card = e.target.closest('.item-card');
        if (!card) return;

        const isLocked = card.dataset.locked === 'true';
        const inp = card.querySelector('.qty-display');

        if (e.target.classList.contains('btn-plus')) {
            inp.value = parseInt(inp.value) + 1;
            
            // Nếu món đã từng gửi bếp, đổi badge hoặc thêm ghi chú nhỏ để nhắc nhân viên biết đây là lượng gọi thêm
            if (isLocked && !card.querySelector('.status-addon')) {
                const infoDiv = card.querySelector('.item-info');
                const badge = document.createElement('span');
                badge.className = 'chef-status-badge status-new status-addon';
                badge.style.marginLeft = '4px';
                badge.textContent = '➕ Có gọi thêm';
                infoDiv.appendChild(badge);
            }
            recalc();
        } else if (e.target.classList.contains('btn-minus')) {
            const minVal = isLocked ? parseInt(inp.getAttribute('min')) : 1;
            
            if (parseInt(inp.value) > minVal) { 
                inp.value = parseInt(inp.value) - 1; 
                
                // Nếu giảm về bằng mức cũ thì xóa badge gọi thêm đi
                if (isLocked && parseInt(inp.value) === minVal) {
                    const addonBadge = card.querySelector('.status-addon');
                    if (addonBadge) addonBadge.remove();
                }
                recalc(); 
            } else if (isLocked) {
                alert('Món ăn này đã được nhà bếp xử lý/phục vụ. Bạn chỉ có thể tăng (gọi thêm món), không thể giảm số lượng xuống thấp hơn mức ban đầu!');
            }
        } else if (e.target.classList.contains('btn-remove')) {
            if (isLocked) {
                alert('Không thể xóa bỏ món ăn đang nấu hoặc đã phục vụ!');
                return;
            }
            if (confirm('Xóa món này khỏi đơn?')) { card.remove(); recalc(); checkEmpty(); }
        }
    });

    document.getElementById('items-container').addEventListener('change', function(e) {
        if (e.target.classList.contains('qty-display')) {
            const card = e.target.closest('.item-card');
            if (card && card.dataset.locked === 'true') {
                const minVal = parseInt(e.target.getAttribute('min'));
                if (parseInt(e.target.value) < minVal) {
                    alert('Không thể hạ số lượng thấp hơn mức nhà bếp đã xác nhận.');
                    e.target.value = minVal;
                }
            }
            recalc();
        }
    });

    function checkEmpty() {
        const container = document.getElementById('items-container');
        if (!container.querySelector('.item-card')) {
            container.innerHTML = `
                <div class="empty-state">
                    <div class="icon">🛒</div>
                    <p>Bàn ăn chưa có món nào.<br>Thêm món gọi từ danh sách bên phải.</p>
                </div>`;
        }
    }

    document.getElementById('menu-search').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.menu-option').forEach(opt => {
            opt.style.display = opt.dataset.search.includes(q) ? '' : 'none';
        });
    });

    document.getElementById('menu-list').addEventListener('click', function(e) {
        const opt = e.target.closest('.menu-option');
        if (!opt) return;
        document.querySelectorAll('.menu-option').forEach(o => o.classList.remove('selected'));
        opt.classList.add('selected');
        selectedMenu = {
            id:    opt.dataset.id,
            name:  opt.dataset.name,
            price: parseInt(opt.dataset.price),
            image: opt.dataset.image,
        };
    });

    document.getElementById('add-plus').addEventListener('click', function() {
        const inp = document.getElementById('add-qty');
        inp.value = parseInt(inp.value) + 1;
    });
    document.getElementById('add-minus').addEventListener('click', function() {
        const inp = document.getElementById('add-qty');
        if (parseInt(inp.value) > 1) inp.value = parseInt(inp.value) - 1;
    });

    // Bấm nút thêm món gọi thêm vào bàn
    document.getElementById('btn-add').addEventListener('click', function() {
        if (!selectedMenu) { alert('Vui lòng chọn một món từ danh sách!'); return; }
        const qty = parseInt(document.getElementById('add-qty').value) || 1;

        const existing = document.querySelector(`.item-card[data-item-id="${selectedMenu.id}"]`);
        if (existing) {
            // Nếu món đã có sẵn tại bàn, tăng số lượng lên giống như bấm nút "+"
            const inp = existing.querySelector('.qty-display');
            inp.value = parseInt(inp.value) + qty;
            
            const isLocked = existing.dataset.locked === 'true';
            if (isLocked && !existing.querySelector('.status-addon')) {
                const infoDiv = existing.querySelector('.item-info');
                const badge = document.createElement('span');
                badge.className = 'chef-status-badge status-new status-addon';
                badge.style.marginLeft = '4px';
                badge.textContent = '➕ Có gọi thêm';
                infoDiv.appendChild(badge);
            }
            
            recalc();
            existing.style.animation = 'none';
            existing.offsetHeight;
            existing.style.animation = 'slideIn .3s ease';
        } else {
            const basePath = "{{ asset('/') }}";
            const imgHtml = selectedMenu.image
                ? `<img class="item-img" src="${selectedMenu.image.startsWith('http') ? selectedMenu.image : basePath + selectedMenu.image}" 
                        onerror="this.style.display='none';this.nextElementSibling.style.display='flex';" 
                        alt="${selectedMenu.name}">
                   <div class="item-img-placeholder" style="display:none;">🍜</div>`
                : `<div class="item-img-placeholder">🍜</div>`;

            const html = `
            <div class="item-card" data-item-id="${selectedMenu.id}" data-price="${selectedMenu.price}" data-locked="false">
                ${imgHtml}
                <div class="item-info">
                    <div class="item-name" title="${selectedMenu.name}">${selectedMenu.name}</div>
                    <div class="item-price">${selectedMenu.price.toLocaleString('vi-VN')}đ / phần</div>
                    <span class="chef-status-badge status-new">✨ Món gọi thêm mới</span>
                </div>
                <input type="hidden" name="items[${nextIndex}][id]" value="${selectedMenu.id}">
                <input type="hidden" name="items[${nextIndex}][quantity]" class="qty-hidden" value="${qty}">
                <input type="hidden" name="items[${nextIndex}][old_quantity]" value="0">
                <div class="qty-control">
                    <button type="button" class="btn-minus">−</button>
                    <input type="number" class="qty-display" value="${qty}" min="1">
                    <button type="button" class="btn-plus">+</button>
                </div>
                <div class="item-subtotal">${(selectedMenu.price * qty).toLocaleString('vi-VN')}đ</div>
                <button type="button" class="btn-remove" title="Xóa món">🗑</button>
            </div>`;

            const emptyState = document.querySelector('.empty-state');
            if (emptyState) emptyState.remove();

            document.getElementById('items-container').insertAdjacentHTML('beforeend', html);
            nextIndex++;
        }

        selectedMenu = null;
        document.querySelectorAll('.menu-option').forEach(o => o.classList.remove('selected'));
        document.getElementById('add-qty').value = 1;
        recalc();
    });

    recalc();
})();
</script>
@endsection