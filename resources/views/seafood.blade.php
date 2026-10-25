@extends('shared')

@section('title', 'Thực đơn Hải sản - Restaurant')

@section('head')
<link rel="stylesheet" href="{{ asset('css/pages/seafood.css') }}?v=1">
<link rel="stylesheet" href="{{ asset('css/pages/menu-mobile.css') }}?v=1">
@endsection

@section('content')
<div class="container">
    <div class="menu-banner"> <img src="{{ asset('images/bannerhaisan.jpg') }}" alt="Banner Hải Sản" class="banner-img"> 
        <div class="banner-overlay">
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