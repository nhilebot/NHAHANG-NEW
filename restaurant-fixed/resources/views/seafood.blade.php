@extends('shared')

@section('title', 'Thực đơn Hải sản - Restaurant')

@section('head')
<style>
    body { background-color: #f9f9f9; padding-top: 100px; }
    
    .navbar-default {
        background-color: #fff;
        border: none !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05) !important;
    }

    .section-title {
        font-family: 'Pacifico', cursive;
        font-size: 60px;
        color: #041526e0;
        /* margin-top: 20px; */
        margin-bottom: 10px;
        font-weight: normal;
        text-align: center; 
        display: block;
        width: 100%;
        border: none !important;
        outline: none !important;
        text-transform: none;
    }

    @media (max-width: 768px) {
        .section-title { font-size: 40px; }
    }

    .section-subtitle {
        text-align: center;
        color: #777;
        max-width: 700px;
        margin: 0 auto 40px auto;
        font-size: 16px;
        line-height: 1.6;
    }

    /* ĐỒNG BỘ CATEGORY NAV */
    .category-nav { margin-bottom: 50px; text-align: center; }
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

    /* ĐỒNG BỘ GRID 4 CỘT */
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
        padding: 20px 0;
    }

    @media (max-width: 1024px) { .menu-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px) { .menu-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; } }
    @media (max-width: 480px) { .menu-grid { grid-template-columns: repeat(1, 1fr); } }

    /* ĐỒNG BỘ CARD MÓN ĂN */
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

    /* ĐỒNG BỘ KHUNG HÌNH VUÔNG 1:1 */
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
        object-fit: cover; /* Để ảnh lấp đầy khung vuông */
        border-radius: 15px;
        transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    /* HIỆU ỨNG PHÓNG ẢNH KHI HOVER */
    .menu-card:hover .product-img {
        transform: scale(1.1);
        filter: brightness(1.05);
    }

    .product-name {
        font-family: 'Playball', cursive;
        font-size: 22px;
        color: #333;
        margin: 10px 0 5px 0;
        /* font-weight: 500; */
        height: 30px;
        overflow: hidden;
    }

    .price-text {
        font-size: 18px;
        color: #e74c3c;
        font-weight: 800;
        margin-bottom: 15px;
    }

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
        text-decoration: none !important;
        transition: all 0.3s ease;
        border: none;
        text-transform: uppercase;
        flex: 1;
    }

    .btn-detail-red { background-color: #e74c3c; color: white !important; }
    .btn-add-green {
    background-color: transparent;
    /* color: #27ae60 !important; */
    border: 1px solid #B5952F;
    color: #B5952F;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
}

    /* NHÃN TRANG TRÍ ĐỒNG BỘ */
    .badge-creative {
        position: absolute;
        top: 10px;
        right: 10px;
        background: linear-gradient(135deg, #e74c3c, #ff5e57);
        color: white;
        padding: 8px 6px;
        border-radius: 50%;
        font-weight: bold;
        font-size: 10px;
        z-index: 2;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        border: 2px solid #fff;
        line-height: 1.2;
    }

    /* TOAST NOTIFICATION */
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #27ae60;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        z-index: 9999;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
    }
    .toast-notification.show { opacity: 1; transform: translateX(0); }
    .toast-notification.error { background: #e74c3c; }
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
    /* color: #27ae60 !important; */
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
    <div class="menu-banner"> <img src="{{ asset('images/bannerhaisan.jpg') }}" alt="Banner Hải Sản" class="banner-img"> 
 </div>
    <div class="row">
        <div class="col-xs-12">
            <h1 class="section-title">Tinh hoa hải sản</h1>
            <p class="section-subtitle">
                Đại tiệc hải sản tươi ngon được đánh bắt trong ngày và chế biến bởi những đầu bếp hàng đầu.
            </p>
        </div>
    </div>
    
    <div class="category-nav">
    <a href="{{ route('menu.index') }}" class="btn-category">Tất cả</a>
    <a href="{{ route('menu.seafood') }}" class="btn-category active">Hải sản</a>
    <a href="{{ route('menu.special') }}" class="btn-category">Món đặc biệt</a>
    <a href="{{ route('menu.salad') }}" class="btn-category">Salad</a>
    <a href="{{ route('menu.vietnamese') }}" class="btn-category">Món Việt</a>
    <a href="{{ route('menu.desserts') }}" class="btn-category">Tráng miệng</a>
    <a href="{{ route('menu.drinks') }}" class="btn-category">Đồ uống</a>
</div>

    <div class="menu-grid">
        @foreach($menus as $menu)
        <div class="menu-card">
            
            
            <div class="img-container">
                <a href="{{ route('menu.detail', $menu->id) }}">
                    <img src="{{ asset($menu->image) }}" class="product-img" alt="{{ $menu->name }}">
                </a>
            </div>

            <h4 class="product-name">{{ $menu->name }}</h4>
            <p class="price-text">{{ number_format($menu->price, 0, ',', '.') }} VNĐ</p>
            
            <div class="card-buttons">
                <a href="{{ route('menu.detail', $menu->id) }}" class="btn-action btn-detail-red">Chi Tiết</a>
                <button class="btn-action btn-add-green add-to-cart-btn" data-food-id="{{ $menu->id }}">
                    <i class="fa fa-shopping-cart"></i> Thêm
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('.add-to-cart-btn').on('click', function(e) {
        e.preventDefault();
        var foodId = $(this).data('food-id');
        var button = $(this);
        
        button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '{{ route("cart.add") }}',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { menu_id: foodId, quantity: 1 },
            success: function(response) {
                showToast('✓ ' + response.message, 'success');
                if (response.cart_count) {
                    $('.navbar .badge, #cart-count').text(response.cart_count);
                }
                button.prop('disabled', false).html('<i class="fa fa-shopping-cart"></i> Thêm');
            },
            error: function(xhr) {
                var msg = xhr.status === 401 ? 'Cần đăng nhập!' : 'Lỗi xảy ra!';
                showToast('✗ ' + msg, 'error');
                button.prop('disabled', false).html('<i class="fa fa-shopping-cart"></i> Thêm');
            }
        });
    });

    function showToast(message, type) {
        $('.toast-notification').remove();
        var toastClass = type === 'error' ? 'toast-notification error' : 'toast-notification';
        var toast = $('<div class="' + toastClass + '">' + message + '</div>');
        $('body').append(toast);
        setTimeout(function() { toast.addClass('show'); }, 100);
        setTimeout(function() {
            toast.removeClass('show');
            setTimeout(function() { toast.remove(); }, 300);
        }, 3000);
    }
});
</script>
@endsection