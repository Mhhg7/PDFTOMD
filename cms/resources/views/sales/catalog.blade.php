@extends('sales.layout', ['title' => $company?->name ?? ($q !== '' ? 'Search' : 'Catalog')])
@php use App\Admin\Catalog; use App\Http\Controllers\Sales\SalesController as S; @endphp
@section('content')
@if ($companies->isEmpty())
  <div class="adm-card adm-empty">
    <p><strong>No companies yet.</strong></p>
    @can('sales.edit')<p><a class="qs-btn qs-btn--primary" href="{{ route('sales.companies.create') }}">{!! Catalog::svg('plus') !!}<span>Add the first company</span></a></p>@endcan
  </div>
@else
<div class="adm-head">
  <div class="sl-title">
    @if ($company && $company->logo)<img class="sl-title__logo" src="{{ route('sales.file', $company->logo) }}" alt="">@endif
    <div>
      <h1>{{ $company?->name ?? 'Search results' }}</h1>
      <p class="adm-muted">{{ $products->count() }} {{ $products->count() === 1 ? 'product' : 'products' }}@if ($q !== '') matching “{{ $q }}” @endif @if ($company && ! $company->active) · <strong>switched off</strong>, hidden from sheets @endif</p>
    </div>
  </div>
  <div class="adm-row">
    @if ($company)
      <a class="qs-btn qs-btn--secondary" href="{{ route('sales.sheet', $company->id) }}" target="_blank" rel="noopener">{!! Catalog::svg('fileCheck') !!}<span>Order sheet</span></a>
      @if ($prices)<a class="qs-btn qs-btn--secondary" href="{{ route('sales.sheet', [$company->id, 'prices' => 1]) }}" target="_blank" rel="noopener">{!! Catalog::svg('fileCheck') !!}<span>With prices</span></a>@endif
    @endif
    @if ($edit)<a class="qs-btn qs-btn--primary" href="{{ route('sales.products.create', ['company' => $company?->id]) }}">{!! Catalog::svg('plus') !!}<span>Add product</span></a>@endif
  </div>
</div>

<div class="adm-tabs" role="navigation" aria-label="Companies">
  @foreach ($companies as $c)
    <a href="{{ route('sales.home', ['company' => $c->id]) }}" @class(['is-on' => $company?->id === $c->id])>{{ $c->name }} <span class="adm-muted">{{ $c->products_count }}</span></a>
  @endforeach
</div>

<div class="sl-bar">
  <form class="adm-search" method="get" action="{{ route('sales.home') }}"><label class="sr-only" for="q">Search all products</label><input class="qs-input" id="q" name="q" value="{{ $q }}" placeholder="Search by brand, ingredient or dose"><button class="qs-btn qs-btn--secondary qs-btn--sm" type="submit">{!! Catalog::svg('search') !!}<span>Search</span></button></form>
  <div class="sl-view" role="group" aria-label="View">
    <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" @class(['is-on' => $view === 'grid']) aria-label="Cards">{!! Catalog::svg('layout') !!}</a>
    <a href="{{ request()->fullUrlWithQuery(['view' => 'table']) }}" @class(['is-on' => $view === 'table']) aria-label="Table">{!! Catalog::svg('menu') !!}</a>
  </div>
</div>

@if ($products->isEmpty())
  <p class="adm-card adm-empty">No products here yet.@if ($edit && $company) <a href="{{ route('sales.products.create', ['company' => $company->id]) }}">Add the first one</a>.@endif</p>
@elseif ($view === 'grid')
  <div class="sl-grid">
    @foreach ($products as $p)
      <article @class(['sl-card', 'is-off' => ! $p->active])>
        <div class="sl-card__ph">@if ($p->photo)<img src="{{ route('sales.file', $p->photo) }}" alt="" loading="lazy">@else{!! Catalog::svg('image') !!}@endif</div>
        <div class="sl-card__body">
          <h2 class="sl-card__name">{{ $p->brand_name }}</h2>
          <p class="sl-card__meta">{{ collect([$p->dose, $p->dosage_form])->filter()->implode(' · ') }}</p>
          @if ($p->active_ingredient)<p class="sl-card__ing">{{ $p->active_ingredient }}</p>@endif
          @if ($q !== '')<p class="sl-card__co">{{ $p->company->name }}</p>@endif
          <div class="sl-card__foot">
            @if ($prices)<span class="sl-price">{{ $p->price !== null ? S::money($p->price) : 'No price' }}</span>@endif
            @if (! $p->active)<span class="qs-badge qs-badge--warning"><span class="qs-badge__dot"></span><span>Hidden</span></span>@endif
            @if ($edit)<a class="qs-btn qs-btn--ghost qs-btn--sm" href="{{ route('sales.products.edit', $p) }}">{!! Catalog::svg('edit') !!}<span>Edit</span></a>@endif
          </div>
        </div>
      </article>
    @endforeach
  </div>
@else
  <div class="adm-card adm-tablewrap">
    <table class="qs-table adm-table">
      <thead><tr><th scope="col">Brand name</th><th scope="col">Active ingredient</th><th scope="col">Dose</th><th scope="col">Dosage form</th>@if ($q !== '')<th scope="col">Company</th>@endif @if ($prices)<th scope="col" class="sl-num">Price</th>@endif @if ($edit)<th scope="col"><span class="sr-only">Actions</span></th>@endif</tr></thead>
      <tbody>
      @foreach ($products as $p)
        <tr @class(['is-off' => ! $p->active])>
          <td><strong>{{ $p->brand_name }}</strong>@if (! $p->active) <span class="adm-muted">(hidden)</span>@endif</td>
          <td>{{ $p->active_ingredient }}</td><td>{{ $p->dose }}</td><td>{{ $p->dosage_form }}</td>
          @if ($q !== '')<td>{{ $p->company->name }}</td>@endif
          @if ($prices)<td class="sl-num">{{ S::money($p->price) }}</td>@endif
          @if ($edit)<td class="adm-actions"><a class="qs-btn qs-btn--ghost qs-btn--sm" href="{{ route('sales.products.edit', $p) }}">{!! Catalog::svg('edit') !!}<span>Edit</span></a></td>@endif
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
@endif
@endif
@endsection
