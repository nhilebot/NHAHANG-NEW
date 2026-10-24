<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon-1.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style-portfolio.css') }}">
    <link rel="stylesheet" href="{{ asset('css/picto-foundry-food.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
             <link rel="stylesheet" href="{{ asset('css/footer.css') }}">

    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Playball' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    {{-- Responsive: luôn đặt SAU các file css khác để ghi đè --}}
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}?v=1">
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

<img class="nav-logo" src="{{ asset('images/logo.png') }}" alt="Món Việt Logo" style="height: 60px; width: auto; filter: drop-shadow(0px 0px 2px rgba(0,0,0,0.5)); margin-right: -10px; margin-left: 100px;">
                        </a>
                    </div>
                    <div class="collapse navbar-collapse nav-flex" id="bs-example-navbar-collapse-1">
    
    <div class="navbar-header nav-spacer" style="flex: 1; margin: 0; padding: 0;"></div>

    <ul class="nav navbar-nav navbar-center-custom" style="flex: 2; display: flex; justify-content: center; align-items: flex-start; float: none; margin: 0; padding: 0; gap: 20px;">
        <li style="margin: 0; padding: 0;"><a class="color_animation" href="{{ url('/') }}" style="padding: 10px;">TRANG CHỦ</a></li>
        <!-- <li style="margin: 0; padding: 0;"><a class="color_animation" href="{{ url('/news') }}" style="padding: 10px;">TIN TỨC</a></li> -->
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
        
    </div> 

    <footer class="site-footer">

        {{-- Dải kêu gọi đặt bàn --}}
        <div class="ft-cta">
            <div class="container">
                
            </div>
        </div>

        {{-- Nội dung chính: 4 cột --}}
        <div class="ft-main">
            <div class="container">
                <div class="ft-grid">

                    {{-- Cột 1: Thương hiệu --}}
                    <div class="ft-brand">
                        <img class="ft-logo" src="{{ asset('images/logo.png') }}" alt="Aurora Garden - Fine Dining">
                        <p class="ft-desc">
                            Khởi nguồn từ niềm đam mê ẩm thực, chúng tôi mang đến những trải nghiệm chuẩn vị và đậm đà nhất, vươn mình trở thành chuỗi nhà hàng hàng đầu.
                        </p>
                        <div class="ft-social">
                            <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                            <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                            <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                        </div>
                    </div>

                    {{-- Cột 2: Khám phá --}}
                    <div>
                        <h4 class="ft-title">Khám phá</h4>
                        <ul class="ft-links">
                            <li><a href="{{ url('/menu') }}">Thực đơn</a></li>
                            <li><a href="{{ url('/reservation') }}">Đặt bàn</a></li>
                            <li><a href="#">Chi nhánh</a></li>
                            <li><a href="#">Khuyến mãi</a></li>
                            <li><a href="#">Thành viên</a></li>
                            <li><a href="{{ url('/news') }}">Tin tức</a></li>
                        </ul>
                    </div>

                    {{-- Cột 3: Giờ mở cửa --}}
                    <div>
                        <h4 class="ft-title">Giờ mở cửa</h4>
                        <ul class="ft-info">
                            <li>
                                <span class="ft-ico"><i class="fa-regular fa-clock"></i></span>
                                <span>
                                    <small>Thứ Hai – Chủ Nhật</small>
                                    <strong>06:00 – 22:00</strong>
                                </span>
                            </li>
                            <li>
                                <span class="ft-ico"><i class="fa-solid fa-utensils"></i></span>
                                <span>
                                    <small>Phục vụ</small>
                                    <strong>Tại chỗ &amp; đặt trước</strong>
                                </span>
                            </li>
                        </ul>
                    </div>

                    {{-- Cột 4: Liên hệ --}}
                    <div>
                        <h4 class="ft-title">Liên hệ</h4>
                        <ul class="ft-info">
                            <li>
                                <span class="ft-ico"><i class="fa-solid fa-phone"></i></span>
                                <span>
                                    <small>Hotline</small>
                                    <a href="tel:0899911390">0899 911 390</a>
                                </span>
                            </li>
                            <li>
                                <span class="ft-ico"><i class="fa-regular fa-envelope"></i></span>
                                <span>
                                    <small>Email</small>
                                    <a href="mailto:contact@AuroraGarden.com">contact@AuroraGarden.com</a>
                                </span>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        {{-- Thanh bản quyền --}}
        <div class="ft-bottom">
            <div class="container">
                <div class="ft-bottom-inner">
                    <span>&copy; {{ date('Y') }} Restaurant. All Rights Reserved.</span>
                    <span>Powered by YourGroup</span>
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
    <script src="{{ asset('js/responsive.js') }}?v=1"></script>
    @yield('scripts')
    @include('partials.chatbox')
</body>
</html>
