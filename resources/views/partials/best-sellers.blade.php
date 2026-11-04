{{-- Khu "Món bán chạy" cho trang khách. Biến vào: $bestSellers (Collection<Menu> có ->sold_qty).
     Dùng lại class .menu-card / .add-to-cart-btn nên nút "Thêm" chạy bằng JS sẵn có của trang. --}}
@php $bestSellers = $bestSellers ?? collect(); @endphp

@if($bestSellers->isNotEmpty())
<section class="best-sellers" aria-label="Món bán chạy nhất">
    <div class="best-head">
        <h2 class="best-title">🔥 Món bán chạy nhất</h2>
        <p class="best-sub">Những món được khách hàng chọn nhiều nhất</p>
    </div>

    <div class="best-grid">
        @foreach($bestSellers as $i => $menu)
            @php
                $imageUrl = str_contains($menu->image, 'images/') ? asset($menu->image) : asset('images/' . $menu->image);
            @endphp
            <div class="menu-card best-card {{ $menu->stock <= 0 ? 'is-soldout' : '' }}">
                <span class="rank-badge rank-{{ $i + 1 }}">Top {{ $i + 1 }}</span>

                <div class="img-container">
                    <a href="{{ route('menu.detail', $menu->id) }}">
                        <img src="{{ $imageUrl }}" alt="{{ $menu->name }}" class="product-img"
                             loading="lazy" onerror="this.src='{{ asset('images/default.jpg') }}'">
                    </a>
                </div>

                <h3 class="product-name">{{ $menu->name }}</h3>
                <div class="price-text">{{ number_format($menu->price, 0, ',', '.') }} VNĐ</div>
                <div class="sold-text">🔥 Đã bán {{ number_format($menu->sold_qty) }}</div>

                <div class="card-buttons">
                    <a href="{{ route('menu.detail', $menu->id) }}" class="btn-action btn-detail-red">Chi Tiết</a>
                    @if($menu->stock <= 0)
                        <button class="btn-action" disabled>Hết hàng</button>
                    @else
                        <button class="btn-action btn-add-green add-to-cart-btn" data-food-id="{{ $menu->id }}">
                            <i class="fa fa-shopping-cart"></i> Thêm
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif
