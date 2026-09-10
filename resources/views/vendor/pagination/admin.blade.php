@if ($paginator->hasPages())
  <nav class="admin-pagination" aria-label="Pagination booking">
    @if ($paginator->onFirstPage())
      <span class="admin-pagination-button is-disabled">Sebelumnya</span>
    @else
      <a class="admin-pagination-button" href="{{ $paginator->previousPageUrl() }}" rel="prev">Sebelumnya</a>
    @endif

    <div class="admin-pagination-pages">
      @foreach ($elements as $element)
        @if (is_string($element))
          <span class="admin-pagination-ellipsis">{{ $element }}</span>
        @endif

        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <span class="admin-pagination-page is-current" aria-current="page">{{ $page }}</span>
            @else
              <a class="admin-pagination-page" href="{{ $url }}">{{ $page }}</a>
            @endif
          @endforeach
        @endif
      @endforeach
    </div>

    @if ($paginator->hasMorePages())
      <a class="admin-pagination-button" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya</a>
    @else
      <span class="admin-pagination-button is-disabled">Berikutnya</span>
    @endif
  </nav>
@endif
