<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon-1.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style-portfolio.css') }}">
    <link rel="stylesheet" href="{{ asset('css/picto-foundry-food.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Playball' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
 
    </style>
    @yield('head')
</head>
<body class="@yield('body-class', '')" style="display: flex; flex-direction: column; min-height: 100vh; margin: 0;">

    <div style="flex: 1 0 auto;">
        
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="row">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="{{ url('/') }}" style="padding: 0px 0px; display: flex; align-items: center;">
                            <img src="{{ asset('images/logo.png') }}" alt="Món Việt Logo" style="height: 100px; width: auto; filter: drop-shadow(0px 0px 2px rgba(0,0,0,0.5)); margin-right: -10px;">
                        </a>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1" style="display: flex !important; justify-content: space-between; align-items: flex-start !important;">
    
    <div class="navbar-header" style="flex: 1; margin: 0; padding: 0;"></div>

    <ul class="nav navbar-nav navbar-center-custom" style="flex: 2; display: flex; justify-content: center; align-items: flex-start; float: none; margin: 0; padding: 0; gap: 20px;">
        <li style="margin: 0; padding: 0;"><a class="color_animation" href="{{ url('/') }}" style="padding: 10px;">TRANG CHỦ</a></li>
        <li style="margin: 0; padding: 0;"><a class="color_animation" href="{{ url('/reservation') }}" style="padding: 10px;">ĐẶT BÀN</a></li>
        <li style="margin: 0; padding: 0;"><a class="color_animation" href="{{ url('/menu') }}" style="padding: 10px;">THỰC ĐƠN</a></li>
        
    </ul>

    <ul class="nav navbar-nav navbar-right" style="flex: 1; display: flex; justify-content: flex-end; align-items: flex-start; float: none; margin: 0; padding: 0;">
        <li style="margin: 0; padding: 0;">
    <a class="color_animation" href="{{ url('/cart') }}" style="display: flex; align-items: center; position: relative; padding: 10px;">
        {{-- Đã đổi từ fa-shopping-cart sang fa-cutlery --}}
        <i class="fa fa-cutlery"></i> 
        
        <span style="margin-left: 5px; font-weight: 600;">MÓN ĐẶT TRƯỚC</span>
        
        @php $count = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity'); @endphp
        <span class="badge" style="background:red; color:white; margin-left: 5px;">{{ $count ?? 0 }}</span>
    </a>
</li>
        @guest
            <li class="dropdown" style="margin: 0; padding: 0;">
                <a href="#" class="dropdown-toggle color_animation" data-toggle="dropdown" style="white-space: nowrap; padding: 10px; display: flex; align-items: center;">
                    <i class="fa fa-user"></i> <span class="caret"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="{{ route('login') }}">Đăng nhập</a></li>
                    <li><a href="{{ route('register') }}">Đăng ký</a></li>
                </ul>
            </li>
        @endguest
        @auth
            <!-- <li style="margin: 0; padding: 0;">
                <a class="color_animation" href="{{ url('/order-history') }}" style="white-space: nowrap; padding: 10px; display: flex; align-items: center;">
                    <i class="fa fa-file-text"></i> ĐƠN HÀNG
                </a>
            </li> -->
          <li class="dropdown" style="margin: 0; padding: 0;">
    <a href="#" class="dropdown-toggle color_animation" data-toggle="dropdown"
       style="white-space: nowrap; padding: 10px; display: flex; align-items: center;">
        <i class="fa fa-user"></i>
        {{ Auth::user()->name }}
        <span class="caret"></span>
    </a>

  <ul class="dropdown-menu dropdown-menu-right">

    <li>
        <a href="{{ url('/profile') }}">
            <i class="fa-solid fa-user"></i>
            Hồ sơ
        </a>
    </li>

    <li>
        <a href="{{ url('/order-history') }}">
            <i class="fa-solid fa-file-lines"></i>
            Đơn hàng
        </a>
    </li>

    <li>
        <a href="#"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           style="color: red;">
            <i class="fa-solid fa-right-from-bracket"></i>
            Đăng xuất
        </a>
    </li>

</ul>
</li>
        @endauth
    </ul>
</div>
                </div>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>
        
    </div> <footer class="sub_footer" style="flex-shrink: 0;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 footer-section">
                            <img src="{{ asset('images/logo.png') }}" alt="Món Việt Logo" style="height: 115px; width: auto; filter: drop-shadow(0px 0px 2px rgba(0,0,0,0.5)); margin-right: -10px;">
                    <p class="footer-desc">
                        Khởi nguồn từ niềm đam mê ẩm thực, chúng tôi mang đến những trải nghiệm chuẩn vị và đậm đà nhất, vươn mình trở thành chuỗi nhà hàng hàng đầu.
                    </p>
                </div>
                <div class="col-md-4 footer-section text-center">
                    <h3 class="footer-title">MENU</h3>
                    <ul class="footer-links">
                        <li><a href="#">Chi nhánh</a></li>
                        <li><a class="color_animation" href="{{ url('/menu') }}">Thực đơn</a></li>
                        <li><a href="#">Khuyến mãi</a></li>
                        <li><a href="#">Thành viên</a></li>
                        <li><a href="#">Tin tức</a></li>
                    </ul>
                </div>
                <div class="col-md-4 footer-section text-right">
                    <h3 class="footer-title">LIÊN HỆ</h3>
                    <p>Hotline: 0899911390</p>
                    <p>Email: contact@restaurant.com</p>
                    <div class="footer-social">
                        <p>Theo dõi chúng tôi trên</p>
                        <a href="#"><i class="fa fa-instagram"></i></a>
                        <a href="#"><i class="fa fa-facebook"></i></a>
                        <a href="#"><i class="fa fa-tiktok"></i></a>
                        <a href="#"><i class="fa fa-youtube-play"></i></a>
                    </div>
                </div>
            </div>
            <div class="row footer-bottom">
                <div class="col-xs-12 text-center">
                    <hr>
                    <p>&copy; 2026 Restaurant. All Rights Reserved. Powered by YourGroup</p>
                </div>
            </div>
        </div>
    </footer>

    {{-- FORM ĐĂNG XUẤT (ẩn) --}}
    @auth
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endauth

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>    
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    @yield('scripts')
</body>
@include('partials.chatbox')

</html>
</html>
