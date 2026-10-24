@extends('shared') {{-- đổi 'layouts.app' thành tên file layout của bạn --}}

@section('head')
    <link rel="stylesheet" href="{{ asset('css/news.css') }}?v=1">
@endsection

@section('content')
<div class="news-page">

    <header class="news-hero news-hero--article">
        <div class="container">
            <span class="news-tag news-tag--light">{{ $news->category }}</span>
            <h1>{{ $news->title }}</h1>
            <p class="news-hero-date"><i class="fa fa-calendar-o"></i> {{ $news->display_date }}</p>
        </div>
    </header>

    <div class="container">
        <article class="news-article">

            @if ($news->image)
                <img class="news-cover" src="{{ asset($news->image) }}" alt="{{ $news->title }}">
            @endif

            <div class="news-content">
                {{-- Tách đoạn theo dòng trống; {{ }} tự lọc mã độc nên an toàn --}}
                @foreach (preg_split('/\R{2,}/', trim($news->content)) as $para)
                    <p>{!! nl2br(e($para)) !!}</p>
                @endforeach
            </div>

            <div class="news-article-actions">
                <a href="{{ route('news.index') }}" class="news-btn news-btn--ghost">
                    <i class="fa fa-long-arrow-left"></i> Quay lại tin tức
                </a>
                <a href="{{ url('/reservation') }}" class="news-btn">Đặt bàn ngay</a>
            </div>
        </article>

        @if ($related->count())
            <section class="news-related">
                <h3>Bài viết khác</h3>
                <div class="news-grid news-grid--related">
                    @foreach ($related as $n)
                        <a href="{{ route('news.show', $n->slug) }}" class="news-card">
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
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</div>
@endsection
