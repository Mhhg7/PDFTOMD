@extends('sales.layout', ['title' => $p->exists ? 'Edit product' : 'Add product'])
@php use App\Admin\Catalog; @endphp
@section('content')
<div class="adm-head"><div>
  <p class="adm-crumb"><a href="{{ route('sales.home', ['company' => $p->company_id]) }}">Catalog</a></p>
  <h1>{{ $p->exists ? $p->brand_name : 'Add product' }}</h1>
  <p class="adm-muted">The first four fields print on the order sheet card, exactly as typed.</p>
</div></div>
<form method="post" enctype="multipart/form-data" action="{{ $p->exists ? route('sales.products.update', $p) : route('sales.products.store') }}" class="adm-card adm-form" data-dirty>
  @csrf @if ($p->exists) @method('put') @endif
  <div class="adm-grid">
    <div class="adm-field"><label class="qs-field__label" for="f-company">Company<span class="qs-field__req">*</span></label>
      <select class="qs-input" id="f-company" name="company_id" required><option value="">Choose</option>@foreach ($companies as $c)<option value="{{ $c->id }}" @selected((int) old('company_id', $p->company_id) === $c->id)>{{ $c->name }}</option>@endforeach</select></div>
    <div class="adm-field"><label class="qs-field__label" for="f-brand">Brand name<span class="qs-field__req">*</span></label><input class="qs-input" id="f-brand" name="brand_name" value="{{ old('brand_name', $p->brand_name) }}" required maxlength="120"></div>
    <div class="adm-field adm-field--wide"><label class="qs-field__label" for="f-ing">Active ingredient</label><input class="qs-input" id="f-ing" name="active_ingredient" value="{{ old('active_ingredient', $p->active_ingredient) }}" maxlength="300"><p class="qs-field__hint">Up to about 60 characters fit on two lines of the card.</p></div>
    <div class="adm-field"><label class="qs-field__label" for="f-dose">Dose</label><input class="qs-input" id="f-dose" name="dose" value="{{ old('dose', $p->dose) }}" maxlength="120" placeholder="e.g. 20 mg / 2 ml"></div>
    <div class="adm-field"><label class="qs-field__label" for="f-form">Dosage form</label><input class="qs-input" id="f-form" name="dosage_form" value="{{ old('dosage_form', $p->dosage_form) }}" maxlength="120" placeholder="e.g. Tablet – 30"></div>
    <div class="adm-field"><label class="qs-field__label" for="f-price">Price</label><input class="qs-input" id="f-price" name="price" type="number" min="0" step="0.01" inputmode="decimal" value="{{ old('price', $p->price) }}" dir="ltr"><p class="qs-field__hint">Only Sales, Editors, Managers and Admins see it. Leave empty if not set.</p></div>
    <div class="adm-field"><label class="qs-field__label" for="f-sort">Order on the sheet</label><input class="qs-input adm-num" id="f-sort" name="sort" type="number" min="0" value="{{ old('sort', $p->sort ?? 0) }}"></div>
    <div class="adm-field adm-field--wide">
      <p class="qs-field__label">Product photo</p>
      <div class="adm-media">
        <div class="adm-media__prev">@if ($p->photo)<img src="{{ route('sales.file', $p->photo) }}" alt="">@else No photo @endif</div>
        <div class="adm-media__ctl">
          <input class="qs-input" type="file" name="photo" accept="image/jpeg,image/png,image/webp" aria-label="Upload a product photo">
          <p class="qs-field__hint">JPG, PNG or WebP up to 8 MB. A pack shot on white works best; it is fitted inside the white frame.</p>
          @if ($p->photo)<label class="adm-check"><input type="checkbox" name="remove_photo" value="1"> Remove the current photo</label>@endif
        </div>
      </div>
    </div>
    <div class="adm-field adm-field--wide"><label class="qs-field__label" for="f-notes">Internal notes</label><textarea class="qs-input" id="f-notes" name="notes" rows="3" maxlength="2000">{{ old('notes', $p->notes) }}</textarea><p class="qs-field__hint">Never printed.</p></div>
    <div class="adm-field"><input type="hidden" name="active" value="0"><label class="adm-check"><input type="checkbox" name="active" value="1" @checked(old('active', $p->active ?? true))> Show in the catalog and on order sheets</label></div>
  </div>
  <div class="adm-formfoot"><button class="qs-btn qs-btn--primary" type="submit"><span>{{ $p->exists ? 'Save product' : 'Add product' }}</span></button><a class="qs-btn qs-btn--ghost" href="{{ route('sales.home', ['company' => $p->company_id]) }}"><span>Cancel</span></a></div>
</form>
@if ($p->exists)
<form method="post" action="{{ route('sales.products.destroy', $p) }}" class="adm-danger" data-confirm="Delete {{ $p->brand_name }}? This cannot be undone.">@csrf @method('delete')
  <p><strong>Delete this product</strong><br><span class="adm-muted">To hide it for a while, untick “Show in the catalog” instead.</span></p>
  <button class="qs-btn qs-btn--danger qs-btn--sm" type="submit">{!! Catalog::svg('trash') !!}<span>Delete</span></button></form>
@endif
@endsection
