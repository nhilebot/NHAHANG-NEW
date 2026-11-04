{{-- Giao diện phân trang cho trang Thực đơn của khách (dùng: $menus->links('partials.menu-pagination')) --}}
@if ($paginator->hasPages())
{{-- Dùng <div role="navigation"> thay <nav>: CSS chung của thanh menu đang tô nền đen cho mọi thẻ <nav> --}}
<div class="menu-pager" role="navigation" aria-label="Phân trang thực đơn">
    <ul class="menu-pager-list">

        {{-- Trang trước --}}
        @if ($paginator->onFirstPage())
            <li class="is-disabled" aria-disabled="true"><span aria-hidden="true">‹<em> </em></span></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Trang trước">‹<em> </em></a></li>
        @endif

        {{-- Các số trang --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="is-dots" aria-hidden="true"><span>…</span></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="is-active" aria-current="page"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}" aria-label="Trang {{ $page }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Trang sau --}}
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Trang sau"><em> </em>›</a></li>
        @else
            <li class="is-disabled" aria-disabled="true"><span aria-hidden="true"><em> </em>›</span></li>
        @endif

    </ul>
</div>
@endif
