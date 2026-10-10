@extends('shared')

@section('title', 'Trang chủ')

@section('content')
   <div id="top" class="starter_container">
    <div id="homeCarousel" class="carousel slide" data-ride="carousel" data-interval="3000">
       

        <div class="carousel-inner" role="listbox">
            <div class="item active">
                <div class="fill" style="background-image:url('{{ asset('images/steak.jpg') }}');"></div>
            </div>
            <div class="item">
                <div class="fill" style="background-image:url('{{ asset('images/bannersl.jpg') }}');"></div>
            </div>
            <div class="item">
                <div class="fill" style="background-image:url('{{ asset('images/breakfast.jpg') }}');"></div>
            </div>
        </div>

        <div class="follow_container">
            <div class="col-md-6 col-md-offset-3">
                <h2 class="top-title">Nhà Hàng</h2>
                <h2 class="white">"Tốt Nhất Trong Thành Phố"</h2>
                <hr>
            </div>
        </div>
    </div>
</div>
    <section id="story" class="description_content">
        <div class="text-content container">
            <div class="col-md-6">
                <h1>Về chúng tôi</h1>
                <div class="fa fa-cutlery fa-2x"></div>
                <p class="desc-text">Nhà hàng là nơi cho sự đơn giản. Thức ăn ngon, bia ngon và dịch vụ tốt. Đơn giản là tên của trò chơi và chúng tôi rất giỏi trong việc tìm thấy nó ở tất cả những nơi phù hợp, ngay cả trong trải nghiệm ăn uống của bạn. Chúng tôi là một nhóm nhỏ đến từ Denver, Colorado, những người làm cho những món ăn đơn giản trở nên khả thi. Hãy tham gia cùng chúng tôi và xem sự đơn giản có hương vị như thế nào.</p>
            </div>
            <div class="col-md-6">
                <div class="img-section">
                    <img src="{{ asset('images/kabob.jpg') }}" width="250" height="220" alt="Kabob">
                    <img src="{{ asset('images/limes.jpg') }}" width="250" height="220" alt="Limes">
                    <div class="img-section-space"></div>
                    <img src="{{ asset('images/radish.jpg') }}" width="250" height="220" alt="Radish">
                    <img src="{{ asset('images/corn.jpg') }}" width="250" height="220" alt="Corn">
                </div>
            </div>
        </div>
    </section>

    <section id="featured" class="description_content">
        <div class="featured background_content">
            <div class="action-buttons">
                <a href="{{ url('/menu') }}" class="btn-main">Xem Menu</a>
                <a href="{{ url('/reservation') }}" class="btn-main">Đặt bàn ngay</a>
            </div>
        </div>
        <div class="text-content container">
            <div class="col-md-6">
                <h1>Hãy xem các món ăn của chúng tôi!</h1>
                <div class="icon-hotdog fa-2x"></div>
                <p class="desc-text">Mỗi món ăn đều được làm thủ công vào lúc bình minh, chỉ sử dụng những nguyên liệu đơn giản nhất để mang lại mùi và hương vị vẫy gọi cả khối. Dừng lại bất cứ lúc nào và trải nghiệm sự đơn giản ở mức tốt nhất.</p>
            </div>
            <div class="col-md-6">
                <ul class="image_box_story2">
                    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                            <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                            <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                            <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                        </ol>
                        <div class="carousel-inner">
                            <div class="item active">
                                <img src="{{ asset('images/slider1.jpg') }}" alt="Slider 1">
                                <div class="carousel-caption"></div>
                            </div>
                            <div class="item">
                                <img src="{{ asset('images/slider2.jpg') }}" alt="Slider 2">
                                <div class="carousel-caption"></div>
                            </div>
                            <div class="item">
                                <img src="{{ asset('images/slider3.JPG') }}" alt="Slider 3">
                                <div class="carousel-caption"></div>
                            </div>
                        </div>
                    </div>
                </ul>
            </div>
        </div>
    </section>
@endsection
