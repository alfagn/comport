{{--
  Minimal dark pagination matching the design system.
  Props: paginator
--}}
@props(['paginator'])
@if($paginator->hasPages())
<nav class="pagination" aria-label="Pagination">
  @if($paginator->onFirstPage())
    <span class="is-disabled">‹</span>
  @else
    <a href="{{ $paginator->previousPageUrl() }}" rel="prev">‹</a>
  @endif

  @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
    @if($page == $paginator->currentPage())
      <span class="is-current">{{ $page }}</span>
    @else
      <a href="{{ $url }}">{{ $page }}</a>
    @endif
  @endforeach

  @if($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" rel="next">›</a>
  @else
    <span class="is-disabled">›</span>
  @endif
</nav>
@endif
