@extends('shared')

@section('title', 'Thực đơn - Restaurant')

@section('head')
<style>
        body {
            background-color: #f9f9f9;
            padding-top: 100px;
        }
        .navbar-default {
            background-color: #222 !important;
            border: none !important;
        }
        .navbar-brand {
            font-family: 'Pacifico', cursive;
            font-size: 28px;
            color: #fff !important;
        }
        .navbar-nav li a {
            color: #fff !important;
        }
        .navbar-nav li a:hover {
            color: #e74c3c !important;
        }
        .navactive {
            color: #e74c3c !important;
        }
        .dropdown-menu {
            background: #222;
            border-top: 3px solid #e74c3c;
        }
        .dropdown-menu li a {
            color: #fff !important;
        }
        .dropdown-menu li a:hover {
            background: #e74c3c !important;
        }
        .section-title {
            font-family: 'Pacifico', cursive;
            font-size: 55px;
            color: #e74c3c;
            text-align: center;
            margin: 40px 0;
        }
        .menu-card {
            background: #fff;
            text-align: center;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 430px;
        }
        .menu-card:hover {
            transform: translateY(-8px);
        }
        .product-img {
            width: 220px;
            height: 220px;
            object-fit: cover;
            border-radius: 20px;
            border: 5px solid #eee;
            display: block;
            margin: 0 auto 20px auto;
        }
        .product-name {
            font-size: 20px;
            margin-top: 0;
            margin-bottom: 12px;
            min-height: 48px;
        }
        .price-text {
            color: #e74c3c;
            font-weight: bold;
            margin-bottom: 18px;
        }
        .btn-detail {
            background: #e74c3c;
            color: #fff !important;
            padding: 10px 18px;
            border-radius: 20px;
            display: inline-block;
            margin-top: auto;
        }
    </style>
@endsection

@section('content')
<!-- NAVBAR (đồng bộ layout) -->
<!-- TITLE -->
<div class="container">
    <!-- <h1 class="section-title">Khám Phá Thực Đơn</h1> -->

    <!-- CATEGORY -->
    <div class="text-center" style="margin-bottom:30px;">
        <a href="{{ url('/menu') }}" class="btn btn-default">Tất cả</a>
        <a href="{{ url('/seafood') }}" class="btn btn-default">Hải sản</a>
        <a href="{{ url('/special') }}" class="btn btn-default">Món đặc biệt</a>
        <a href="{{ url('/salad') }}" class="btn btn-default">Salad</a>
        <a href="{{ url('/vietnamese') }}" class="btn btn-default">Món Việt</a>
        <a href="{{ url('/desserts') }}" class="btn btn-default">Tráng miệng</a>
        <a href="{{ url('/drinks') }}" class="btn btn-default">Đồ uống</a>
    </div>

    <!-- MENU LIST -->
    <div class="row">
        @foreach($menus as $menu)
        <div class="col-md-3 col-sm-6">
            <div class="menu-card">

                <a href="{{ route('menu.detail', $menu->id) }}">
                    <img src="{{ asset($menu->image) }}" class="product-img">
                </a>

                <h4 class="product-name">{{ $menu->name }}</h4>

                <p class="price-text">
                    {{ number_format($menu->price, 0, ',', '.') }} VNĐ
                </p>

                <a href="{{ route('menu.detail', $menu->id) }}" class="btn-detail">
                    Xem chi tiết
                </a>

            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- FOOTER -->
<!-- JS -->

<script src="{{ asset('js/bootstrap.min.js') }}"></script>
@endsection
