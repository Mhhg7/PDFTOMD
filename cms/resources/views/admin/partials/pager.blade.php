@if ($paginator->hasPages())
<nav class="adm-pager" aria-label="Pages">
  @if ($paginator->onFirstPage())<span class="adm-muted">Newer</span>@else<a href="{{ $paginator->previousPageUrl() }}">Newer</a>@endif
  <span>Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>
  @if ($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}">Older</a>@else<span class="adm-muted">Older</span>@endif
</nav>
@endif
