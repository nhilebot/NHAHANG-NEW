@extends('layouts.admin')
@section('topbar_title', 'Xem thực đơn')

@section('admin_content')
<style>
    .menu-card { border:1.5px solid #e2e8f0; border-radius:12px; overflow:hidden; background:#fff; }
    .menu-card img { width:100%; height:140px; object-fit:cover; }
    .cat-btn { padding:6px 16px; border-radius:20px; border:1.5px solid #e2e8f0;
               background:#fff; cursor:pointer; font-size:.82rem; font-weight:600; }
    .cat-btn.active { background:#5b3cff; color:#fff; border-color:#5b3cff; }
</style>

<div class="panel">
    <div class="panel-header">
        <div class="panel-title">🍽️ Thực đơn nhà hàng</div>
        <input type="text" id="search-menu" placeholder="🔍 Tìm món..." class="form-control"
            style="width:220px;" oninput="filterMenu()">
    </div>

    {{-- Lọc danh mục --}}
    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px;">
        <button class="cat-btn active" onclick="filterCat('all',this)">Tất cả</button>
        @foreach($categories as $cat)
            @if($cat)
            <button class="cat-btn" onclick="filterCat('{{ $cat }}',this)">{{ $cat }}</button>
            @endif
        @endforeach
    </div>

    <div class="row g-3" id="menu-grid">
        @foreach($menus as $menu)
        <div class="col-6 col-md-3 menu-item" data-cat="{{ $menu->category }}" data-name="{{ strtolower($menu->name) }}">
            <div class="menu-card">
                <img src="{{ asset($menu->image) }}" onerror="this.src='https://via.placeholder.com/200x140'">
                <div style="padding:12px;">
                    <div style="font-weight:700; font-size:.88rem; color:#0f172a;">{{ $menu->name }}</div>
                    @if($menu->description)
                    <div style="font-size:.78rem; color:#64748b; margin:4px 0;">{{ Str::limit($menu->description, 60) }}</div>
                    @endif
                    <div style="color:#5b3cff; font-weight:800; margin-top:6px;">{{ number_format($menu->price) }}đ</div>
                    <div style="margin-top:6px;">
                        @if($menu->status === 'available' || !isset($menu->status))
                            <span style="font-size:.75rem; background:#d1fae5; color:#065f46; padding:2px 8px; border-radius:10px;">✓ Còn món</span>
                        @else
                            <span style="font-size:.75rem; background:#fee2e2; color:#991b1b; padding:2px 8px; border-radius:10px;">✗ Hết món</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
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
