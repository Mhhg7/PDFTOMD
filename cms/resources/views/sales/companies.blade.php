@extends('sales.layout', ['title' => 'Companies'])
@php use App\Admin\Catalog; @endphp
@section('content')
<div class="adm-head"><div><h1>Companies</h1><p class="adm-muted">Each company gets its own order sheet with its logo in the top corner.</p></div>
<a class="qs-btn qs-btn--primary" href="{{ route('sales.companies.create') }}">{!! Catalog::svg('plus') !!}<span>Add company</span></a></div>
<div class="adm-card adm-tablewrap"><table class="qs-table adm-table">
  <thead><tr><th scope="col">Logo</th><th scope="col">Name</th><th scope="col">Products</th><th scope="col">On sheets</th><th scope="col"><span class="sr-only">Actions</span></th></tr></thead>
  <tbody>
  @forelse ($companies as $c)
    <tr><td>@if ($c->logo)<img class="sl-logo" src="{{ route('sales.file', $c->logo) }}" alt="">@endif</td>
      <td><a class="adm-rowlink" href="{{ route('sales.companies.edit', $c) }}">{{ $c->name }}</a></td>
      <td><a href="{{ route('sales.home', ['company' => $c->id]) }}">{{ $c->products_count }}</a></td>
      <td>@if ($c->active)<span class="qs-badge qs-badge--success"><span class="qs-badge__dot"></span><span>Yes</span></span>@else<span class="qs-badge qs-badge--warning"><span class="qs-badge__dot"></span><span>Off</span></span>@endif</td>
      <td class="adm-actions"><a class="qs-btn qs-btn--ghost qs-btn--sm" href="{{ route('sales.companies.edit', $c) }}">{!! Catalog::svg('edit') !!}<span>Edit</span></a></td></tr>
  @empty
    <tr><td colspan="5" class="adm-empty">No companies yet.</td></tr>
  @endforelse
  </tbody></table></div>
@endsection
