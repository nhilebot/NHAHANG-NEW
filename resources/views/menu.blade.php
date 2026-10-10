@extends('shared')

@section('title', 'Khám phá thực đơn - Restaurant')

@section('head')
<style>
    body { background-color: #f9f9f9; padding-top: 100px; }

    /* XÓA VẠCH KẺ NAVBAR */
    .navbar-default {
        background-color: #fff;
        border: none !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05) !important;
    }

    /* TIÊU ĐỀ SECTION */
    .section-title {
        font-family: 'Pacifico', cursive;
        font-size: 60px;
        color: #041526e0;
        /* margin-top: 20px; */
        margin-bottom: 40px;
        font-weight: normal;
        text-align: center;
        display: block;
        width: 100%;
        border: none !important;
    }

    @media (max-width: 768px) {
        .section-title { font-size: 40px; }
    }

    /* ĐIỀU HƯỚNG DANH MỤC */
    .category-nav { margin-bottom: 40px; text-align: center; }
    .btn-category {
        padding: 10px 22px;
        margin: 5px;
        border-radius: 30px;
        text-transform: uppercase;
        font-weight: bold;
        transition: 0.3s;
        border: 2px solid #e74c3c;
        color: #e74c3c;
        display: inline-block;
        text-decoration: none;
        font-size: 13px;
    }
    .btn-category:hover, .btn-category.active {
        background: #e74c3c;
        color: white !important;
        text-decoration: none;
    }

    /* HỆ THỐNG GRID 4 CỘT */
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
        padding: 20px 0;
    }

    @media (max-width: 1024px) { .menu-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px) { .menu-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; } }
    @media (max-width: 480px) { .menu-grid { grid-template-columns: repeat(1, 1fr); } }

    /* CARD MÓN ĂN ĐỒNG BỘ */
    .menu-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 12px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02), 0 10px 20px rgba(0,0,0,0.05);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid rgba(0,0,0,0.03);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .menu-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 5px 10px rgba(0,0,0,0.05), 0 20px 40px rgba(0,0,0,0.1);
    }

    /* KHUNG ẢNH VUÔNG 1:1 */
    .img-container {
        position: relative;
        width: 100%;
        padding-bottom: 100%; 
        margin-bottom: 15px;
        overflow: hidden;
        border-radius: 15px;
        background-color: #fcfcfc;
    }

    .product-img {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        object-fit: cover; 
        transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .menu-card:hover .product-img {
        transform: scale(1.1);
    }

    /* THÔNG TIN MÓN */
    .product-name {
        font-family: 'Playball', cursive;
        font-size: 22px;
        color: #333;
        margin: 10px 0 5px 0;
        height: 30px;
        overflow: hidden;
        /* font-weight: 500; */
    }

    .price-text {
        font-size: 18px;
        color: #e74c3c;
        font-weight: 800;
        margin-bottom: 15px;
    }

    /* NÚT BẤM */
    .card-buttons {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: auto;
    }

    .btn-action {
        padding: 8px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        border: none;
        transition: 0.3s;
        flex: 1;
    }

    .btn-detail-red { background-color: #e74c3c; color: white !important; text-decoration: none; }
    .btn-add-green {
    background-color: transparent;
    /* color: #27ae60 !important; */
    border: 1px solid #B5952F;
    color: #B5952F;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
}

    /* SEARCH BOX CẢI TIẾN */
    .search-box {
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border: 1px solid #eee;
        background: #fff;
        margin-bottom: 30px;
    }

    .search-box .form-control { border: none; height: 50px; padding-left: 25px; box-shadow: none; }
    .btn-search { background-color: #e74c3c; color: white !important; height: 50px; padding: 0 25px; font-weight: bold; border: none; }

    /* TOAST */
    .toast-notification {
        position: fixed;
        top: 20px; right: 20px;
        background: #27ae60; color: white;
        padding: 15px 20px; border-radius: 8px;
        z-index: 9999; opacity: 0; transform: translateX(100%);
        transition: all 0.3s ease;
    }
    .toast-notification.show { opacity: 1; transform: translateX(0); }
    .toast-notification.error { background: #e74c3c; }

    /* TIÊU ĐỀ DANH MỤC CON */
    .category-section-title {
        font-size: 28px;
        color: #333;
        margin: 40px 0 20px 0;
        padding-left: 15px;
        border-left: 5px solid #e74c3c;
        font-weight: bold;
    }
    /* 1. MÀU CHỮ CHỦ ĐẠO & TIÊU ĐỀ */
    .menu-hero .section-title {
        font-family: 'Pacifico', cursive;
        color: #ffffff !important; /* Trắng nổi bật trên nền ảnh tối */
        text-shadow: 2px 2px 15px rgba(0,0,0,0.6);
    }

    .category-section-title {
        font-size: 26px;
        color: #2c3e50; /* Xám đen đậm sang trọng */
        margin: 50px 0 25px 0;
        padding-left: 15px;
        border-left: 4px solid #333; /* Đổi vạch đỏ thành vạch đen trung tính */
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* 2. ĐIỀU HƯỚNG DANH MỤC (CATEGORY) */
    .btn-category {
        padding: 10px 25px;
        border-radius: 4px; /* Đổi từ bo tròn sang bo nhẹ để chuyên nghiệp hơn */
        border: 1px solid #333;
        color: #333;
        background: transparent;
        font-size: 12px;
        transition: 0.4s;
    }

    .btn-category:hover, .btn-category.active {
        background: #333; /* Màu đen giống Header trang chủ */
        color: #fff !important;
    }

    /* 3. THÔNG TIN TRÊN CARD MÓN ĂN */
    .product-name {
        font-family: 'Playball', cursive;
        color: #041526e0;
        font-size: 24px;
    }

    .price-text {
        font-size: 17px;
        color: #8a6d3b; /* Màu đồng/vàng nhẹ (thường thấy ở nhà hàng cao cấp) */
        font-weight: 700;
        margin-bottom: 20px;
    }

    /* 4. NÚT BẤM TRÊN CARD (BUTTONS) */
    .btn-detail-red { 
        background-color: #333; /* Chuyển đỏ thành đen cùng tông Navbar */
        color: white !important; 
    }

    .btn-add-green { 
        background-color: transparent; 
        /* color: #27ae60 !important;  */
        border: 1px solid #B5952F;
       color: #B5952F;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
    }
    
    .btn-add-green:hover {
        background-color: #B5952F;
        color: white !important;
    }

    /* 5. SEARCH BOX ĐỒNG BỘ */
    .btn-search { 
        background-color: #333; /* Nút tìm kiếm đồng bộ màu Header */
        color: white !important;
    }
    /* 5. SEARCH BOX ĐỒNG BỘ */
    .btn-search { 
        background-color: #333; /* Nút tìm kiếm đồng bộ màu Header */
        color: white !important;
    }
    body {
    margin: 0;
    padding: 0;
}

.menu-banner {
    width: 100vw;
    margin-left: calc(-50vw + 50%);
    /* Tăng từ 450px lên mức bạn muốn, ví dụ 600px hoặc 70vh */
    height: 600px; 
    position: relative;
    overflow: hidden;
}

.banner-img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
}

.banner-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;

    background: rgba(0,0,0,0.4);
    color: white;
}
</style>
@endsection

@section('content')
<div class="container">
    <div class="menu-banner"> <img src="{{ asset('images/monhan.jpg') }}" alt="Banner Hải Sản" class="banner-img"> 
 </div>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <form action="{{ url('/menu') }}" method="GET">
                <div class="input-group search-box">
                    <input type="text" name="search" class="form-control" placeholder="Hôm nay bạn muốn ăn gì..." value="{{ request('search') }}">
                    <span class="input-group-btn">
                        <button class="btn btn-search" type="submit">
                            <i class="fa fa-search"></i> Tìm ngay
                        </button>
                    </span>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 text-center">
            <h1 class="section-title">Khám phá thực đơn</h1>
        </div>
    </div>
    
    <div class="category-nav">
    <a href="{{ url('/menu') }}" class="btn-category {{ !request('category_id') ? 'active' : '' }}">Tất cả</a>
    
    <a href="{{ route('menu.seafood') }}" class="btn-category">Hải sản</a>

    {{-- Các nút khác vẫn giữ vòng lặp hoặc viết tay tùy bạn --}}
    @foreach($categories as $cat)
        @if($cat->id != 1) {{-- Bỏ qua ID 1 nếu 1 là Hải sản để tránh trùng nút --}}
            <a href="{{ url('/menu?category_id=' . $cat->id) }}" class="btn-category">
                {{ $cat->name }}
            </a>
        @endif
    @endforeach
</div>

    <div class="category-container">
        {{-- NHÓM MÓN ĂN TỰ ĐỘNG THEO TÊN DANH MỤC TRONG ADMIN --}}
        @php 
            $groupedMenus = $menus->groupBy(function($menu) {
                // Sử dụng relationship mà chúng ta đã làm ở Admin
                return $menu->category_relation ? $menu->category_relation->name : 'Chưa phân loại';
            }); 
        @endphp

        @if($groupedMenus->isEmpty())
            <div class="text-center" style="padding: 50px; color: #777;">
                <h3>Không tìm thấy thực đơn nào phù hợp.</h3>
            </div>
        @else
            @foreach($groupedMenus as $categoryName => $menuGroup)
                {{-- In thẳng tên danh mục từ Database ra đây --}}
                <h2 class="category-section-title">{{ $categoryName }}</h2>
                
                <div class="menu-grid">
                    @foreach($menuGroup as $menu)
                        <div class="menu-card" style="{{ $menu->stock <= 0 ? 'opacity: 0.7; position: relative;' : '' }}">
    {{-- Hiển thị nhãn HẾT MÓN đè lên ảnh --}}
    @if($menu->stock <= 0)
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10; background: rgba(231, 76, 60, 0.9); color: white; padding: 5px 15px; border-radius: 4px; font-weight: bold; width: 80%;">
            TẠM HẾT MÓN
        </div>
    @endif

    <div class="img-container">
        <a href="{{ route('menu.detail', $menu->id) }}">
            @php
                $imageUrl = str_contains($menu->image, 'images/') ? asset($menu->image) : asset('images/' . $menu->image);
            @endphp
            <img src="{{ $imageUrl }}" alt="{{ $menu->name }}" class="product-img" 
                 style="{{ $menu->stock <= 0 ? 'filter: grayscale(100%);' : '' }}" 
                 onerror="this.src='{{ asset('images/default.jpg') }}'">
        </a>
    </div>

    <h3 class="product-name">{{ $menu->name }}</h3>
    <div class="price-text">{{ number_format($menu->price, 0, ',', '.') }} VNĐ</div>

    <div class="card-buttons">
        <a href="{{ route('menu.detail', $menu->id) }}" class="btn-action btn-detail-red">Chi Tiết</a>
        
        {{-- Vô hiệu hóa nút THÊM nếu hết hàng --}}
        @if($menu->stock <= 0)
            <button class="btn-action" disabled style="background-color: #ccc; cursor: not-allowed; color: #666;">
                Hết hàng
            </button>
        @else
            <button class="btn-action btn-add-green add-to-cart-btn" data-food-id="{{ $menu->id }}">
                <i class="fa fa-shopping-cart"></i> Thêm
            </button>
        @endif
    </div>
</div>
                    @endforeach
                </div>
            @endforeach
        @endif
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $(document).on('click', '.add-to-cart-btn', function(e) {
        e.preventDefault();
        var foodId = $(this).data('food-id');
        var button = $(this);

        button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: '{{ route("reservation.addToCart") }}', 
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                food_id: foodId,
                quantity: 1
            },
            success: function(response) {
                if (response.success) {
    // Sửa chữ thành nội dung bạn muốn hiện ra
    showToast('✓ Đã thêm vào thực đơn!', 'success'); 
    $('.badge').text(response.cartCount); 
} else {
    // Sửa nội dung thông báo lỗi (ví dụ hết hàng)
    var errorMsg = response.message ? response.message : 'Không thể thêm món!';
    showToast('✗ ' + errorMsg, 'error');
}
            },
            error: function() {
                showToast('Bạn cần đăng nhập để thực hiện!', 'error');
            },
            complete: function() {
                button.prop('disabled', false).html('<i class="fa fa-shopping-cart"></i> Thêm');
            }
        });
    });

    function showToast(message, type) {
        $('.toast-notification').remove();
        var toastClass = type === 'error' ? 'toast-notification error' : 'toast-notification';
        var toast = $('<div class="' + toastClass + '">' + message + '</div>');
        $('body').append(toast);
        setTimeout(() => toast.addClass('show'), 100);
        setTimeout(() => {
            toast.removeClass('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
});
</script>
@endsection