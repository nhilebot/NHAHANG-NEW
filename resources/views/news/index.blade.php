@extends('shared')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/news.css') }}?v=1">
@endsection

@section('content')
<div class="news-page">

    {{-- Đầu trang --}}
    <header class="news-hero">
        <div class="container">
            <span class="news-hero-sub">Aurora Garden</span>
            <h1>Tin tức &amp; Ưu đãi</h1>
            <p>Món mới, sự kiện và chương trình khuyến mãi mới nhất từ nhà hàng.</p>
        </div>
    </header>

    <div class="container">

        {{-- Thanh lọc chuyên mục --}}
        {{-- Dùng <div> chứ không dùng <nav>: main.css đang tô nền đen cho MỌI thẻ <nav> --}}
        <div class="news-filter" role="navigation" aria-label="Chuyên mục">
            <a href="{{ route('news.index') }}" class="{{ $category ? '' : 'active' }}">Tất cả</a>
            @foreach ($categories as $cat)
                <a href="{{ route('news.index', ['category' => $cat]) }}"
                   class="{{ $category === $cat ? 'active' : '' }}">{{ $cat }}</a>
            @endforeach
        </div>

        @if ($news->count())
            <div class="news-grid">
                @foreach ($news as $n)
                    {{-- Bài đầu tiên của trang 1 (khi không lọc) hiển thị to hơn --}}
                    @php $featured = $loop->first && $news->currentPage() === 1 && !$category; @endphp

                    <a href="{{ route('news.show', $n->slug) }}" class="news-card {{ $featured ? 'is-featured' : '' }}">
                        @if ($n->image)
                            <div class="news-thumb" style="background-image:url('{{ asset($n->image) }}')"></div>
                        @else
                            <div class="news-thumb news-thumb--empty"><i class="fa fa-cutlery"></i></div>
                        @endif

                        <div class="news-body">
                            <div class="news-meta">
                                <span class="news-tag">{{ $n->category }}</span>
                                <span class="news-date"><i class="fa fa-calendar-o"></i> {{ $n->display_date }}</span>
                            </div>
                            <h2>{{ $n->title }}</h2>
                            <p>{{ $n->summary }}</p>
                            <span class="news-more">Đọc tiếp <i class="fa fa-long-arrow-right"></i></span>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="news-pagination">
                {{ $news->links() }}
            </div>
        @else
            <div class="news-empty">
                <i class="fa fa-newspaper-o"></i>
                <p>Chưa có bài viết nào trong mục này.</p>
                <a href="{{ route('news.index') }}" class="news-btn">Xem tất cả tin</a>
            </div>
        @endif

    </div>
</div>
@endsection
