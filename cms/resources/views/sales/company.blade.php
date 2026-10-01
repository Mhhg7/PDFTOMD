@extends('sales.layout', ['title' => 'Companies'])
@php use App\Admin\Catalog; @endphp
@section('content')
<div class="adm-head"><div><p class="adm-crumb"><a href="{{ route('sales.companies.index') }}">Companies</a></p><h1>{{ $c->exists ? $c->name : 'Add company' }}</h1></div></div>
<form method="post" enctype="multipart/form-data" action="{{ $c->exists ? route('sales.companies.update', $c) : route('sales.companies.store') }}" class="adm-card adm-form adm-narrow" data-dirty>
  @csrf @if ($c->exists) @method('put') @endif
  <div class="qs-field"><label class="qs-field__label" for="c-name">Name<span class="qs-field__req">*</span></label><input class="qs-input" id="c-name" name="name" value="{{ old('name', $c->name) }}" required maxlength="120"></div>
  <div class="qs-field"><p class="qs-field__label">Logo</p>
    <div class="adm-media"><div class="adm-media__prev">@if ($c->logo)<img src="{{ route('sales.file', $c->logo) }}" alt="" style="object-fit:contain">@else No logo @endif</div>
      <div class="adm-media__ctl"><input class="qs-input" type="file" name="logo" accept="image/jpeg,image/png,image/webp" aria-label="Upload a logo"><p class="qs-field__hint">Shown in a white box, 135 × 67 on the sheet. PNG with a transparent background works best.</p>
      @if ($c->logo)<label class="adm-check"><input type="checkbox" name="remove_logo" value="1"> Remove the current logo</label>@endif</div></div></div>
  <div class="qs-field"><label class="qs-field__label" for="c-sort">Order</label><input class="qs-input adm-num" id="c-sort" name="sort" type="number" min="0" value="{{ old('sort', $c->sort ?? 0) }}"></div>
  <input type="hidden" name="active" value="0"><label class="adm-check"><input type="checkbox" name="active" value="1" @checked(old('active', $c->active ?? true))> Show this company and its products</label>
  <div class="adm-formfoot"><button class="qs-btn qs-btn--primary" type="submit"><span>{{ $c->exists ? 'Save' : 'Add company' }}</span></button><a class="qs-btn qs-btn--ghost" href="{{ route('sales.companies.index') }}"><span>Cancel</span></a></div>
</form>
@if ($c->exists)
<form method="post" action="{{ route('sales.companies.destroy', $c) }}" class="adm-danger adm-narrow" data-confirm="Delete {{ $c->name }}?">@csrf @method('delete')
  <p><strong>Delete this company</strong><br><span class="adm-muted">Only possible once it has no products.</span></p>
  <button class="qs-btn qs-btn--danger qs-btn--sm" type="submit">{!! Catalog::svg('trash') !!}<span>Delete</span></button></form>
@endif
@endsection
