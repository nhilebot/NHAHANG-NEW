<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('page_title', 'Quản trị') – Nhà hàng Aurora Garden</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('admin_assets/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/layout.css') }}?v=1">
<link rel="stylesheet" href="{{ asset('css/admin-responsive.css') }}?v=1">
{{-- CSS riêng của từng trang (@push('styles')) - nạp SAU admin-responsive để giữ đúng thứ tự cascade cũ --}}
@stack('styles')
</head>
<body>

@if(session('success'))
<div class="flash-bar flash-success" id="flash-msg"><span>✅</span> {{ session('success') }}</div>
@elseif(session('error'))
<div class="flash-bar flash-error" id="flash-msg"><span>❌</span> {{ session('error') }}</div>
@endif

<div class="app">

  <aside class="sidebar">
    <div class="sidebar-logo">
      <div class="logo-icon">
        <a href="{{ url('/') }}" style="display:flex;align-items:center;">
          {{-- Nếu logo bị lỗi nền đen, hãy dùng logo trong suốt hoặc thay bằng icon --}}
          <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width:60px; border-radius: 8px;">
        </a>
      </div>
      <div>
        <div class="logo-text">Aurora Garden </div>
        <div class="logo-sub">Quản trị hệ thống</div>
      </div>
    </div>

    @php
    // Lấy role_id từ database
    $rawRole = auth()->user()->role_id;
    
    // Ánh xạ số sang chữ để các lệnh @if bên dưới chạy đúng
    $roleMap = [
        \App\Models\Role::ADMIN => 'admin',
        \App\Models\Role::STAFF => 'staff',
        \App\Models\Role::CHEF  => 'chef'
    ];

    $role = $roleMap[$rawRole] ?? 'customer';
    
    $userName = auth()->user()->name ?? 'User';
    $initials = mb_strtoupper(mb_substr($userName, 0, 1));
    @endphp

    <div class="sidebar-section-label">Tổng quan</div>

    <nav class="sidebar-nav">

      {{-- ═══ ADMIN ═══ --}}
      @if($role === 'admin')

        <a class="nav-item {{ (Request::is('admin') && !Request::is('admin/*')) ? 'active' : '' }}" href="{{ route('admin.index') }}">
          <div class="nav-item-left"><div class="nav-icon">📊</div>Dashboard</div>
          <!-- <span class="nav-arrow">›</span> -->
        </a>
      <div class="sidebar-section-label">Quản lý thực đơn</div>
        <div>
          <a class="nav-item {{ Request::routeIs('admin.categories.*') ? 'active' : '' }}" href="#" onclick="toggleSub(event,'cat-sub',this)">
            <div class="nav-item-left"><div class="nav-icon">📁</div>Danh mục</div>
            <span class="nav-arrow {{ Request::routeIs('admin.categories.*') ? 'rotate-90':'' }}">›</span>
          </a>
          <div class="sub-menu" id="cat-sub" style="display:{{ Request::routeIs('admin.categories.*')?'flex':'none' }}">
            <a class="sub-item {{ Request::routeIs('admin.categories.index')?'active':'' }}" href="{{ route('admin.categories.index') }}">Danh sách danh mục</a>
            <a class="sub-item {{ Request::routeIs('admin.categories.create')?'active':'' }}" href="{{ route('admin.categories.create') }}">+ Thêm danh mục</a>
          </div>
        </div>

        <div>
          <a class="nav-item {{ Request::routeIs('admin.menus.*') ? 'active' : '' }}" href="#" onclick="toggleSub(event,'menu-sub',this)">
            <div class="nav-item-left"><div class="nav-icon">🍽️</div>Thực đơn</div>
            <span class="nav-arrow {{ Request::routeIs('admin.menus.*') ? 'rotate-90':'' }}">›</span>
          </a>
          <div class="sub-menu" id="menu-sub" style="display:{{ Request::routeIs('admin.menus.*')?'flex':'none' }}">
            <a class="sub-item {{ Request::routeIs('admin.menus.index')?'active':'' }}" href="{{ route('admin.menus.index') }}">Danh sách món ăn</a>
            <a class="sub-item {{ Request::routeIs('admin.menus.create')?'active':'' }}" href="{{ route('admin.menus.create') }}">+ Thêm món ăn</a>
          </div>
        </div>
      <div class="sidebar-section-label">Quản lý bàn</div>

        <div>
          <a class="nav-item {{ Request::routeIs('admin.tables.*') ? 'active' : '' }}" href="#" onclick="toggleSub(event,'table-sub',this)">
            <div class="nav-item-left"><div class="nav-icon">🪑</div>Bàn ăn</div>
            <span class="nav-arrow {{ Request::routeIs('admin.tables.*') ? 'rotate-90':'' }}">›</span>
          </a>
          <div class="sub-menu" id="table-sub" style="display:{{ Request::routeIs('admin.tables.*')?'flex':'none' }}">
            <a class="sub-item {{ Request::routeIs('admin.tables.index')?'active':'' }}" href="{{ route('admin.tables.index') }}">Danh sách bàn</a>
            <a class="sub-item {{ Request::routeIs('admin.tables.create')?'active':'' }}" href="{{ route('admin.tables.create') }}">+ Tạo đơn đặt bàn</a>
          </div>
        </div>

        <a class="nav-item {{ Request::routeIs('admin.reservations.*') ? 'active' : '' }}" href="{{ route('admin.reservations.index') }}">
          <div class="nav-item-left"><div class="nav-icon">🛎️ </div>Order & phục vụ</div>
          <!-- <span class="nav-arrow">›</span> -->
        </a>
                <div class="sidebar-section-label">Quản lý kho</div>
        <div>
          <a class="nav-item {{ Request::routeIs('admin.inventory.*') ? 'active' : '' }}" href="#" onclick="toggleSub(event,'inventory-sub',this)">
            <div class="nav-item-left"><div class="nav-icon">📦</div>Kho nguyên liệu</div>
            <span class="nav-arrow {{ Request::routeIs('admin.inventory.*') ? 'rotate-90':'' }}">›</span>
          </a>
          <div class="sub-menu" id="inventory-sub" style="display:{{ Request::routeIs('admin.inventory.*') ? 'flex':'none' }}">
            <a class="sub-item {{ Request::routeIs('admin.inventory.ingredients.*') ? 'active':'' }}" href="{{ route('admin.inventory.ingredients.index') }}">Nguyên liệu</a>
            <a class="sub-item {{ Request::routeIs('admin.inventory.recipes.*') ? 'active':'' }}" href="{{ route('admin.inventory.recipes.index') }}">Công thức món</a>
            <a class="sub-item {{ Request::routeIs('admin.inventory.categories.*') ? 'active':'' }}" href="{{ route('admin.inventory.categories.index') }}">Nhóm nguyên liệu</a>
            <a class="sub-item {{ Request::routeIs('admin.inventory.suppliers.*') ? 'active':'' }}" href="{{ route('admin.inventory.suppliers.index') }}">Nhà cung cấp</a>
             <a class="sub-item {{ Request::routeIs('admin.inventory.receipts.*') ? 'active':'' }}" href="{{ route('admin.inventory.receipts.index') }}">Phiếu nhập kho</a>
  <a class="sub-item {{ Request::routeIs('admin.inventory.issues.*') ? 'active':'' }}" href="{{ route('admin.inventory.issues.index') }}">Phiếu xuất kho</a>
  <a class="sub-item {{ Request::routeIs('admin.inventory.movements.*') ? 'active':'' }}" href="{{ route('admin.inventory.movements.index') }}">Sổ kho</a>
          </div>
        </div>
        <div class="sidebar-section-label">Thanh toán</div>
      <a class="nav-item {{ Request::routeIs('admin.bills.*') ? 'active' : '' }}"
   href="{{ route('admin.bills.index') }}">
  <div class="nav-item-left">
      <div class="nav-icon">🧾</div>Hóa đơn
  </div>
  <!-- <span class="nav-arrow">›</span> -->
</a>
      <a class="nav-item {{ Request::routeIs('admin.promotions.*') ? 'active' : '' }}"
         href="{{ route('admin.promotions.index') }}">
        <div class="nav-item-left"><div class="nav-icon">🎟️</div>Khuyến mãi</div>
      </a>
        <div>
          <div class="sidebar-section-label">Người dùng</div>
          <a class="nav-item {{ Request::routeIs('admin.employees.*') ? 'active' : '' }}"
             href="#" onclick="toggleSub(event,'emp-sub',this)">
            <div class="nav-item-left"><div class="nav-icon">👥</div>Quản lý nhân viên</div>
            <span class="nav-arrow {{ Request::routeIs('admin.employees.*') ? 'rotate-90':'' }}">›</span>
          </a>
          <div class="sub-menu" id="emp-sub"
               style="display:{{ Request::routeIs('admin.employees.*') ? 'flex':'none' }}">
            <a class="sub-item {{ request('role') === 'staff' ? 'active':'' }}"
               href="{{ route('admin.employees.index', ['role' => 'staff']) }}">🧑‍💼 Nhân viên phục vụ</a>
            <a class="sub-item {{ request('role') === 'chef' ? 'active':'' }}"
               href="{{ route('admin.employees.index', ['role' => 'chef']) }}">👨‍🍳 Đầu bếp</a>
          </div>
          
        </div>

      @endif
      {{-- ═══ KẾT THÚC ADMIN ═══ --}}

      {{-- ═══ STAFF ═══ --}}
    @if($role === 'staff' || auth()->user()->isStaff())

   {{-- DASHBOARD --}}
<a class="nav-item {{ Request::routeIs('staff.dashboard') ? 'active' : '' }}"
   href="{{ route('staff.dashboard') }}">
    <div class="nav-item-left">
        <div class="nav-icon">🏠</div>
        Dashboard
    </div>
</a>
<a class="nav-item {{ Request::routeIs('staff.schedule') ? 'active' : '' }}"
   href="{{ route('staff.schedule') }}">
    <div class="nav-item-left">
        <div class="nav-icon">🗓️</div>
        Lịch làm việc
    </div>
</a>
<div class="sidebar-section-label">Phục vụ nhà hàng</div>

    {{-- BÀN ĂN --}}
    <div>
        <a class="nav-item {{ Request::routeIs('staff.tables.*') ? 'active' : '' }}"
           href="#"
           onclick="toggleSub(event,'staff-table-sub',this)">

            <div class="nav-item-left">
                <div class="nav-icon">🪑</div>
                Bàn ăn
            </div>

            <span class="nav-arrow {{ Request::routeIs('staff.tables.*') ? 'rotate-90':'' }}">
                ›
            </span>
        </a>

        <div class="sub-menu"
             id="staff-table-sub"
             style="display:{{ Request::routeIs('staff.tables.*') ? 'flex':'none' }}">

            <a class="sub-item {{ Request::routeIs('staff.tables.index') ? 'active':'' }}"
               href="{{ route('staff.tables.index') }}">
                Danh sách bàn
            </a>
        </div>
    </div>


    {{-- ĐẶT BÀN --}}
    <div>
        <a class="nav-item {{ Request::routeIs('staff.reservations.*') ? 'active' : '' }}"
           href="#"
           onclick="toggleSub(event,'staff-reservation-sub',this)">

            <div class="nav-item-left">
                <div class="nav-icon">📅</div>
                Đặt bàn
            </div>

            <span class="nav-arrow {{ Request::routeIs('staff.reservations.*') ? 'rotate-90':'' }}">
                ›
            </span>
        </a>

        <div class="sub-menu"
             id="staff-reservation-sub"
             style="display:{{ Request::routeIs('staff.reservations.*') ? 'flex':'none' }}">

            <a class="sub-item {{ Request::routeIs('staff.reservations.index') ? 'active':'' }}"
               href="{{ route('staff.reservations.index') }}">
                Danh sách phục vụ
            </a>

            <a class="sub-item {{ Request::routeIs('staff.reservations.create') ? 'active':'' }}"
               href="{{ route('staff.reservations.create') }}">
                + Tạo đặt bàn
            </a>
        </div>

    </div>
        {{-- HÓA ĐƠN --}}
        <a class="nav-item {{ Request::routeIs('staff.bills.*') ? 'active' : '' }}"
           href="{{ route('staff.bills.index') }}">
            <div class="nav-item-left">
                <div class="nav-icon">🧾</div>
                Hóa đơn
            </div>
        </a>
@endif

      {{-- ═══ CHEF ═══ --}}
      @if($role === 'chef')
        <a class="nav-item {{ Request::routeIs('chef.index') ? 'active' : '' }}" href="{{ route('chef.index') }}">
          <div class="nav-item-left"><div class="nav-icon">🔥</div>Màn hình bếp</div>
          <span class="nav-arrow">›</span>
        </a>
        <a class="nav-item {{ Request::routeIs('chef.schedule') ? 'active' : '' }}" href="{{ route('chef.schedule') }}">
          <div class="nav-item-left"><div class="nav-icon">🗓️</div>Lịch làm việc</div>
          <span class="nav-arrow">›</span>
        </a>
      @endif



    </nav>

    <div class="sidebar-footer">
      <div class="user-row">
        <div class="avatar">{{ $initials }}</div>
        <div style="overflow: hidden;">
          <div class="user-name" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $userName }}</div>
          <div class="user-role">
            @php $roleLabels=['admin'=>'Quản trị viên','staff'=>'Nhân viên phục vụ','chef'=>'Đầu bếp','customer'=>'Khách hàng']; @endphp
            <span class="role-badge role-{{ $role }}">{{ $roleLabels[$role] ?? $role }}</span>
          </div>
        </div>
      </div>
      <button class="btn-logout" onclick="document.getElementById('logout-form').submit();">🚪 Đăng xuất hệ thống</button>
    </div>
  </aside>

  <div class="main">
    <header class="topbar">
      <div class="topbar-left">
        <button class="menu-btn" title="Thu gọn menu">☰</button>
        <span class="topbar-title">@yield('topbar_title','Bảng điều khiển')</span>
      </div>
      <div style="display:flex;align-items:center;gap:20px;">
        <form action="{{ url()->current() }}" method="GET" class="search-box">
            <span style="opacity: 0.5;">🔍</span>
            <!-- name="search" là tên biến sẽ gửi lên server -->
            <!-- value="{{ request('search') }}" giúp giữ lại từ khóa vừa nhập -->
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm...">
            
            <!-- Thêm các input hidden để giữ lại các tham số URL khác nếu có (ví dụ: role=staff) -->
            @foreach(request()->except('search', 'page') as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
        </form>
        <div style="font-size:0.85rem; color:#718096; font-weight: 500; background: var(--bg-main); padding: 8px 14px; border-radius: 8px; border: 1px solid var(--border-color);">
            📅 {{ now()->format('d/m/Y - H:i') }}
        </div>
      </div>
    </header>

    <div class="content">
      @yield('admin_content')
    </div>
  </div>

</div>

@auth
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
@endauth

<script src="{{ asset('js/responsive.js') }}?v=1"></script>
<script>
function toggleSub(e,id,el){
  e.preventDefault();
  var s = document.getElementById(id);
  var a = el.querySelector('.nav-arrow');
  var o = s.style.display === 'flex';
  s.style.display = o ? 'none' : 'flex';
  a.classList.toggle('rotate-90', !o);
}

// Cải tiến Flash Message: Trượt ra tự nhiên
var f = document.getElementById('flash-msg');
if(f) {
    setTimeout(function(){
        f.style.transform = 'translateX(120%)';
        f.style.transition = 'transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        setTimeout(function(){ f.remove() }, 500);
    }, 4000);
}
</script>
<!-- @include('partials.chatbox') -->
</body>
<!-- @include('partials.chatbox') -->
</html>