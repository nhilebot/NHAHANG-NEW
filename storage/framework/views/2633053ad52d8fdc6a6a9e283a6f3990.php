<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $__env->yieldContent('page_title', 'Quản trị'); ?> – Nhà hàng Việt</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo e(asset('admin_assets/style.css')); ?>">
<style>
  
    /* CSS Variables cho Theme Sang Trọng */
    :root {
        --sidebar-bg: #1A2228;
        --sidebar-bg-hover: #2C3A47;
        --sidebar-text: #A0AEC0;
        --sidebar-text-active: #FFFFFF;
        --gold-accent: #D4AF37;
        --gold-glow: rgba(212, 175, 55, 0.15);
        --bg-main: #F4F7FE;
        --card-bg: #FFFFFF;
        --text-dark: #1A2228;
        --text-muted: #718096;
        --border-color: #E2E8F0;
        --shadow-sm: 0 2px 10px rgba(0,0,0,0.02);
        --shadow-md: 0 10px 30px rgba(0,0,0,0.04);
        --transition: all 0.3s ease;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: 'Be Vietnam Pro', sans-serif;
        background-color: var(--bg-main);
        color: var(--text-dark);
        overflow: hidden;
    }

    .app {
        display: flex;
        height: 100vh;
        width: 100vw;
    }

    /* --- SIDEBAR --- */
    .sidebar {
        width: 280px;
        background-color: var(--sidebar-bg);
        display: flex;
        flex-direction: column;
        color: var(--sidebar-text);
        box-shadow: 4px 0 20px rgba(0,0,0,0.05);
        z-index: 100;
    }

    .sidebar-logo {
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .logo-text {
        font-family: 'Playfair Display', serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gold-accent);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .logo-sub {
        font-size: 0.75rem;
        color: #718096;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-top: 2px;
    }

    .sidebar-section-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #4A5568;
        padding: 24px 24px 10px;
    }

    .sidebar-nav {
        flex: 1;
        overflow-y: auto;
        padding: 0 16px;
    }

    .sidebar-nav::-webkit-scrollbar { width: 4px; }
    .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

    .nav-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        margin-bottom: 6px;
        border-radius: 12px;
        color: var(--sidebar-text);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        transition: var(--transition);
        border: 1px solid transparent;
    }

    .nav-item-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .nav-icon {
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        transition: transform 0.3s ease;
    }

    .nav-item:hover {
        background-color: var(--sidebar-bg-hover);
        color: var(--sidebar-text-active);
    }

    .nav-item:hover .nav-icon {
        transform: scale(1.15);
    }

    .nav-item.active {
        background-color: var(--gold-glow);
        color: var(--gold-accent);
        border: 1px solid rgba(212, 175, 55, 0.2);
        font-weight: 600;
    }

    .sub-menu {
        display: none;
        flex-direction: column;
        padding-left: 44px;
        margin: 0 0 10px 0;
        position: relative;
    }

    .sub-menu::before {
        content: '';
        position: absolute;
        left: 27px;
        top: 0;
        bottom: 10px;
        width: 1px;
        background: rgba(255,255,255,0.1);
    }

    .sub-item {
        color: #718096;
        text-decoration: none;
        font-size: 0.85rem;
        padding: 10px 16px;
        border-radius: 8px;
        transition: var(--transition);
        margin-bottom: 2px;
        position: relative;
    }

    .sub-item::before {
        content: '';
        position: absolute;
        left: -17px;
        top: 50%;
        width: 10px;
        height: 1px;
        background: rgba(255,255,255,0.1);
    }

    .sub-item:hover, .sub-item.active {
        color: var(--gold-accent);
        background: rgba(255,255,255,0.03);
    }

    .nav-arrow {
        font-size: 1.1rem;
        transition: transform 0.3s ease;
        opacity: 0.5;
    }
    .rotate-90 { transform: rotate(90deg); opacity: 1; color: var(--gold-accent); }

    /* --- NÚT THAO TÁC NGHIỆP VỤ (BUTTONS) --- */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        text-decoration: none;
        transition: all 0.3s ease;
        font-family: inherit;
    }

    .btn:active {
        transform: translateY(1px);
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.78rem;
        border-radius: 8px;
        gap: 4px;
    }

    /* Nút chính (Thêm mới, Lưu) -> Màu Vàng Gold */
    .btn-primary {
        background: var(--gold-accent);
        color: #fff;
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
    }
    .btn-primary:hover {
        background: #B58500;
        box-shadow: 0 6px 20px rgba(212, 175, 55, 0.35);
        color: #fff;
    }

    /* Nút thành công (Xác nhận, Phục vụ) -> Màu Xanh lá */
    .btn-success {
        background: #10B981;
        color: #fff;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
    }
    .btn-success:hover {
        background: #059669;
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
        color: #fff;
    }

    /* Nút cảnh báo (Tạm ngưng, Sửa) -> Màu Cam */
    .btn-warning {
        background: #F59E0B;
        color: #fff;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.2);
    }
    .btn-warning:hover {
        background: #D97706;
        box-shadow: 0 6px 20px rgba(245, 158, 11, 0.3);
        color: #fff;
    }

    /* Nút nguy hiểm (Xóa, Hủy) -> Màu Đỏ */
    .btn-danger {
        background: #EF4444;
        color: #fff;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
    }
    .btn-danger:hover {
        background: #DC2626;
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
        color: #fff;
    }

    /* Nút phụ / Quay lại -> Màu trong suốt viền xám */
    .btn-ghost {
        background: transparent;
        color: var(--text-muted);
        border: 1px solid var(--border-color);
    }
    .btn-ghost:hover {
        background: #fff;
        color: var(--text-dark);
        border-color: #CBD5E1;
        box-shadow: var(--shadow-sm);
    }

    /* --- SIDEBAR FOOTER & USER --- */
    .sidebar-footer {
        padding: 20px;
        border-top: 1px solid rgba(255,255,255,0.05);
        background: rgba(0,0,0,0.1);
    }

    .user-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        padding: 10px;
        background: var(--sidebar-bg-hover);
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.02);
    }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--gold-accent), #B58500);
        display: grid;
        place-items: center;
        font-weight: 800;
        font-size: 1rem;
        color: #fff;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3);
    }

    .user-name { font-size: 0.9rem; font-weight: 700; color: #fff; }
    
    .role-badge {
        display: inline-block;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 6px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-top: 4px;
    }
    .role-admin { background: rgba(239,68,68,0.15); color: #FCA5A5; border: 1px solid rgba(239,68,68,0.2); }
    .role-staff { background: rgba(59,130,246,0.15); color: #93C5FD; border: 1px solid rgba(59,130,246,0.2); }
    .role-chef { background: rgba(245,158,11,0.15); color: #FCD34D; border: 1px solid rgba(245,158,11,0.2); }
    .role-customer { background: rgba(148,163,184,0.1); color: #CBD5E1; }

    .btn-logout {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        background: transparent;
        color: #FCA5A5;
        border: 1px solid rgba(239,68,68,0.3);
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: var(--transition);
        font-family: inherit;
    }
    .btn-logout:hover {
        background: rgba(239,68,68,0.15);
        border-color: rgba(239,68,68,0.5);
    }

    /* --- MAIN LAYOUT & TOPBAR --- */
    .main {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
    }

    .topbar {
        background: var(--card-bg);
        padding: 16px 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 50;
        box-shadow: var(--shadow-sm);
    }

    .topbar-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .menu-btn {
        background: none;
        border: none;
        font-size: 1.4rem;
        cursor: pointer;
        color: var(--text-muted);
        padding: 0;
        transition: color 0.2s;
    }
    .menu-btn:hover { color: var(--gold-accent); }

    .topbar-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-dark);
        font-family: 'Playfair Display', serif;
    }

    .search-box {
        display: flex;
        align-items: center;
        gap: 10px;
        background: var(--bg-main);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 10px 16px;
        transition: var(--transition);
    }
    .search-box:focus-within {
        border-color: var(--gold-accent);
        box-shadow: 0 0 0 3px var(--gold-glow);
        background: #fff;
    }

    .search-box input {
        background: none;
        border: none;
        outline: none;
        font-size: 0.9rem;
        width: 200px;
        font-family: inherit;
        color: var(--text-dark);
    }
    .search-box input::placeholder { color: #A0AEC0; }

    .content {
        padding: 32px;
        flex: 1;
    }

    /* --- ALERTS / FLASH MESSAGES --- */
    .flash-bar {
        position: fixed;
        top: 20px;
        right: 32px;
        left: auto;
        z-index: 9999;
        padding: 16px 24px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        animation: slideInRight 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .flash-success { background: #10B981; color: #fff; border-left: 4px solid #047857; }
    .flash-error { background: #EF4444; color: #fff; border-left: 4px solid #B91C1C; }

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
</style>
</head>
<body>

<?php if(session('success')): ?>
<div class="flash-bar flash-success" id="flash-msg"><span>✅</span> <?php echo e(session('success')); ?></div>
<?php elseif(session('error')): ?>
<div class="flash-bar flash-error" id="flash-msg"><span>❌</span> <?php echo e(session('error')); ?></div>
<?php endif; ?>

<div class="app">

  <aside class="sidebar">
    <div class="sidebar-logo">
      <div class="logo-icon">
        <a href="<?php echo e(url('/')); ?>" style="display:flex;align-items:center;">
          
          <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo" style="width:60px; border-radius: 8px;">
        </a>
      </div>
      <div>
        <div class="logo-text">Nhà hàng Việt</div>
        <div class="logo-sub">Quản trị hệ thống</div>
      </div>
    </div>

    <?php
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
    ?>

    <div class="sidebar-section-label">Tổng quan</div>

    <nav class="sidebar-nav">

      
      <?php if($role === 'admin'): ?>

        <a class="nav-item <?php echo e((Request::is('admin') && !Request::is('admin/*')) ? 'active' : ''); ?>" href="<?php echo e(route('admin.index')); ?>">
          <div class="nav-item-left"><div class="nav-icon">📊</div>Dashboard</div>
          <!-- <span class="nav-arrow">›</span> -->
        </a>
      <div class="sidebar-section-label">Quản lý thực đơn</div>
        <div>
          <a class="nav-item <?php echo e(Request::routeIs('admin.categories.*') ? 'active' : ''); ?>" href="#" onclick="toggleSub(event,'cat-sub',this)">
            <div class="nav-item-left"><div class="nav-icon">📁</div>Danh mục</div>
            <span class="nav-arrow <?php echo e(Request::routeIs('admin.categories.*') ? 'rotate-90':''); ?>">›</span>
          </a>
          <div class="sub-menu" id="cat-sub" style="display:<?php echo e(Request::routeIs('admin.categories.*')?'flex':'none'); ?>">
            <a class="sub-item <?php echo e(Request::routeIs('admin.categories.index')?'active':''); ?>" href="<?php echo e(route('admin.categories.index')); ?>">Danh sách danh mục</a>
            <a class="sub-item <?php echo e(Request::routeIs('admin.categories.create')?'active':''); ?>" href="<?php echo e(route('admin.categories.create')); ?>">+ Thêm danh mục</a>
          </div>
        </div>

        <div>
          <a class="nav-item <?php echo e(Request::routeIs('admin.menus.*') ? 'active' : ''); ?>" href="#" onclick="toggleSub(event,'menu-sub',this)">
            <div class="nav-item-left"><div class="nav-icon">🍽️</div>Thực đơn</div>
            <span class="nav-arrow <?php echo e(Request::routeIs('admin.menus.*') ? 'rotate-90':''); ?>">›</span>
          </a>
          <div class="sub-menu" id="menu-sub" style="display:<?php echo e(Request::routeIs('admin.menus.*')?'flex':'none'); ?>">
            <a class="sub-item <?php echo e(Request::routeIs('admin.menus.index')?'active':''); ?>" href="<?php echo e(route('admin.menus.index')); ?>">Danh sách món ăn</a>
            <a class="sub-item <?php echo e(Request::routeIs('admin.menus.create')?'active':''); ?>" href="<?php echo e(route('admin.menus.create')); ?>">+ Thêm món ăn</a>
          </div>
        </div>
      <div class="sidebar-section-label">Quản lý bàn</div>

        <div>
          <a class="nav-item <?php echo e(Request::routeIs('admin.tables.*') ? 'active' : ''); ?>" href="#" onclick="toggleSub(event,'table-sub',this)">
            <div class="nav-item-left"><div class="nav-icon">🪑</div>Bàn ăn</div>
            <span class="nav-arrow <?php echo e(Request::routeIs('admin.tables.*') ? 'rotate-90':''); ?>">›</span>
          </a>
          <div class="sub-menu" id="table-sub" style="display:<?php echo e(Request::routeIs('admin.tables.*')?'flex':'none'); ?>">
            <a class="sub-item <?php echo e(Request::routeIs('admin.tables.index')?'active':''); ?>" href="<?php echo e(route('admin.tables.index')); ?>">Danh sách bàn</a>
            <a class="sub-item <?php echo e(Request::routeIs('admin.tables.create')?'active':''); ?>" href="<?php echo e(route('admin.tables.create')); ?>">+ Tạo đơn đặt bàn</a>
          </div>
        </div>

        <a class="nav-item <?php echo e(Request::routeIs('admin.reservations.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.reservations.index')); ?>">
          <div class="nav-item-left"><div class="nav-icon">🛎️ </div>Order & phục vụ</div>
          <!-- <span class="nav-arrow">›</span> -->
        </a>
        <div class="sidebar-section-label">Thanh toán</div>
      <a class="nav-item <?php echo e(Request::routeIs('admin.bills.*') ? 'active' : ''); ?>"
   href="<?php echo e(route('admin.bills.index')); ?>">
  <div class="nav-item-left">
      <div class="nav-icon">🧾</div>Hóa đơn
  </div>
  <!-- <span class="nav-arrow">›</span> -->
</a>
        <div>
          <div class="sidebar-section-label">Người dùng</div>
          <a class="nav-item <?php echo e(Request::routeIs('admin.employees.*') ? 'active' : ''); ?>"
             href="#" onclick="toggleSub(event,'emp-sub',this)">
            <div class="nav-item-left"><div class="nav-icon">👥</div>Quản lý nhân viên</div>
            <span class="nav-arrow <?php echo e(Request::routeIs('admin.employees.*') ? 'rotate-90':''); ?>">›</span>
          </a>
          <div class="sub-menu" id="emp-sub"
               style="display:<?php echo e(Request::routeIs('admin.employees.*') ? 'flex':'none'); ?>">
            <a class="sub-item <?php echo e(request('role') === 'staff' ? 'active':''); ?>"
               href="<?php echo e(route('admin.employees.index', ['role' => 'staff'])); ?>">🧑‍💼 Nhân viên phục vụ</a>
            <a class="sub-item <?php echo e(request('role') === 'chef' ? 'active':''); ?>"
               href="<?php echo e(route('admin.employees.index', ['role' => 'chef'])); ?>">👨‍🍳 Đầu bếp</a>
          </div>
          
        </div>

      <?php endif; ?>
      

      
    <?php if($role === 'staff' || auth()->user()->isStaff()): ?>

    <div class="sidebar-section-label">Phục vụ nhà hàng</div>

    
    <div>
        <a class="nav-item <?php echo e(Request::routeIs('staff.tables.*') ? 'active' : ''); ?>"
           href="#"
           onclick="toggleSub(event,'staff-table-sub',this)">

            <div class="nav-item-left">
                <div class="nav-icon">🪑</div>
                Bàn ăn
            </div>

            <span class="nav-arrow <?php echo e(Request::routeIs('staff.tables.*') ? 'rotate-90':''); ?>">
                ›
            </span>
        </a>

        <div class="sub-menu"
             id="staff-table-sub"
             style="display:<?php echo e(Request::routeIs('staff.tables.*') ? 'flex':'none'); ?>">

            <a class="sub-item <?php echo e(Request::routeIs('staff.tables.index') ? 'active':''); ?>"
               href="<?php echo e(route('staff.tables.index')); ?>">
                Danh sách bàn
            </a>
        </div>
    </div>


    
    <div>
        <a class="nav-item <?php echo e(Request::routeIs('staff.reservations.*') ? 'active' : ''); ?>"
           href="#"
           onclick="toggleSub(event,'staff-reservation-sub',this)">

            <div class="nav-item-left">
                <div class="nav-icon">📅</div>
                Đặt bàn
            </div>

            <span class="nav-arrow <?php echo e(Request::routeIs('staff.reservations.*') ? 'rotate-90':''); ?>">
                ›
            </span>
        </a>

        <div class="sub-menu"
             id="staff-reservation-sub"
             style="display:<?php echo e(Request::routeIs('staff.reservations.*') ? 'flex':'none'); ?>">

            <a class="sub-item <?php echo e(Request::routeIs('staff.reservations.index') ? 'active':''); ?>"
               href="<?php echo e(route('staff.reservations.index')); ?>">
                Danh sách phục vụ
            </a>

            <a class="sub-item <?php echo e(Request::routeIs('staff.reservations.create') ? 'active':''); ?>"
               href="<?php echo e(route('staff.reservations.create')); ?>">
                + Tạo đặt bàn
            </a>
        </div>

    </div>

<?php endif; ?>

      
      <?php if($role === 'chef'): ?>
        <a class="nav-item <?php echo e(Request::routeIs('chef.index') ? 'active' : ''); ?>" href="<?php echo e(route('chef.index')); ?>">
          <div class="nav-item-left"><div class="nav-icon">🔥</div>Màn hình bếp</div>
          <span class="nav-arrow">›</span>
        </a>
      <?php endif; ?>



    </nav>

    <div class="sidebar-footer">
      <div class="user-row">
        <div class="avatar"><?php echo e($initials); ?></div>
        <div style="overflow: hidden;">
          <div class="user-name" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($userName); ?></div>
          <div class="user-role">
            <?php $roleLabels=['admin'=>'Quản trị viên','staff'=>'Nhân viên phục vụ','chef'=>'Đầu bếp','customer'=>'Khách hàng']; ?>
            <span class="role-badge role-<?php echo e($role); ?>"><?php echo e($roleLabels[$role] ?? $role); ?></span>
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
        <span class="topbar-title"><?php echo $__env->yieldContent('topbar_title','Bảng điều khiển'); ?></span>
      </div>
      <div style="display:flex;align-items:center;gap:20px;">
        <form action="<?php echo e(url()->current()); ?>" method="GET" class="search-box">
            <span style="opacity: 0.5;">🔍</span>
            <!-- name="search" là tên biến sẽ gửi lên server -->
            <!-- value="<?php echo e(request('search')); ?>" giúp giữ lại từ khóa vừa nhập -->
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Tìm kiếm...">
            
            <!-- Thêm các input hidden để giữ lại các tham số URL khác nếu có (ví dụ: role=staff) -->
            <?php $__currentLoopData = request()->except('search', 'page'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </form>
        <div style="font-size:0.85rem; color:#718096; font-weight: 500; background: var(--bg-main); padding: 8px 14px; border-radius: 8px; border: 1px solid var(--border-color);">
            📅 <?php echo e(now()->format('d/m/Y - H:i')); ?>

        </div>
      </div>
    </header>

    <div class="content">
      <?php echo $__env->yieldContent('admin_content'); ?>
    </div>
  </div>

</div>

<?php if(auth()->guard()->check()): ?>
<form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display:none;"><?php echo csrf_field(); ?></form>
<?php endif; ?>

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
<!-- <?php echo $__env->make('partials.chatbox', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> -->
</body>
<!-- <?php echo $__env->make('partials.chatbox', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> -->
</html><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/layouts/admin.blade.php ENDPATH**/ ?>