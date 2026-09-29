@if ($paginator->total() > 0)
@php $rtl = app()->getLocale() === 'ar'; $prev = $rtl ? '→' : '←'; $next = $rtl ? '←' : '→'; @endphp
<nav class="pager" aria-label="{{ __('التنقل بين الصفحات') }}">
    <span class="num">{{ __('عرض :from–:to من :total', ['from' => $paginator->firstItem(), 'to' => $paginator->lastItem(), 'total' => $paginator->total()]) }}</span>
    @if ($paginator->hasPages())
    <div class="pages">
        @if ($paginator->onFirstPage())
            <span class="off" aria-hidden="true">{{ $prev }}</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('الصفحة السابقة') }}">{{ $prev }}</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="off">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="on" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('الصفحة التالية') }}">{{ $next }}</a>
        @else
            <span class="off" aria-hidden="true">{{ $next }}</span>
        @endif
    </div>
    @endif
</nav>
@endif
