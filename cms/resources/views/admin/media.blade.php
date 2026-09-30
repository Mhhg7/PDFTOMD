@extends('admin.layout', ['title' => 'Photos and files'])
@php use App\Admin\Catalog; use App\Http\Controllers\Admin\MediaController; @endphp
@section('content')
<div class="adm-head"><div><h1>Photos and files</h1><p class="adm-muted">Upload photos (JPG, PNG, WebP, GIF) and PDF leaflets up to 8&nbsp;MB. Use real photographs of the team, warehouses and fleet; wide photos of at least 1600&nbsp;px work best for banners.</p></div></div>
<form class="adm-card adm-drop" method="post" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" data-dropzone>
  @csrf
  {!! Catalog::svg('upload', 'adm-drop__icon') !!}
  <p><strong>Drop files here</strong> or choose them from your computer.</p>
  <label class="qs-btn qs-btn--primary">{!! Catalog::svg('plus') !!}<span>Choose files</span><input type="file" name="files[]" multiple accept="image/jpeg,image/png,image/webp,image/gif,application/pdf" hidden data-autosubmit></label>
</form>
<div class="adm-lib">
  @forelse ($items as $m)
    @php $uses = MediaController::usage($m->path); @endphp
    <figure class="adm-lib__item">
      <div class="adm-lib__img">@if ($m->isImage())<img src="{{ asset($m->path) }}" alt="{{ $m->alt['en'] ?? '' }}" loading="lazy">@else{!! Catalog::svg('file') !!}<span>PDF</span>@endif</div>
      <figcaption>
        <span class="adm-lib__name" title="{{ $m->original_name }}">{{ $m->original_name }}</span>
        <span class="adm-muted">@if ($m->width){{ $m->width }}×{{ $m->height }} · @endif{{ number_format($m->size / 1024) }} KB · {{ $uses ? 'in use' : 'not used' }}</span>
        <span class="adm-row">
          <button type="button" class="qs-btn qs-btn--ghost qs-btn--sm" data-copy="{{ $m->path }}">{!! Catalog::svg('copy') !!}<span>Copy path</span></button>
          <form method="post" action="{{ route('admin.media.destroy', $m) }}" data-confirm="{{ $uses ? 'This file is used on the site. Delete it anyway? The site will show a placeholder instead.' : 'Delete this file?' }}">
            @csrf @method('delete') @if ($uses)<input type="hidden" name="force" value="1">@endif
            <button class="qs-btn qs-btn--ghost qs-btn--sm adm-del" type="submit">{!! Catalog::svg('trash') !!}<span>Delete</span></button>
          </form>
        </span>
      </figcaption>
    </figure>
  @empty
    <p class="adm-empty">No files yet. Photos you upload here, or straight from a photo field, appear in this library.</p>
  @endforelse
</div>
{{ $items->links('admin.partials.pager') }}
@endsection
