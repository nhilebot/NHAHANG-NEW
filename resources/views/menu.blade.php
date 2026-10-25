@extends('shared')

@section('title', 'Khám phá thực đơn - Restaurant')

@section('head')
<link rel="stylesheet" href="{{ asset('css/pages/menu.css') }}?v=1">
<link rel="stylesheet" href="{{ asset('css/pages/menu-mobile.css') }}?v=1">
@endsection

@section('content')
<div class="container">
    <div class="menu-banner"> <img src="{{ asset('images/monhan.jpg') }}" alt="Banner Hải Sản" class="banner-img"> 
        <div class="banner-overlay">
            <h1 class="section-title">Khám phá thực đơn</h1>
        </div>
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