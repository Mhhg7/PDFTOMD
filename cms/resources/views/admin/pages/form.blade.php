@extends('admin.layout', ['title' => 'Pages', 'pageChoices' => $pages, 'blockSchema' => $schema])
@php use App\Admin\Catalog; $o = session()->hasOldInput(); @endphp
@section('content')
<div class="adm-head">
  <div>
    <p class="adm-crumb"><a href="{{ route('admin.pages.index') }}">Pages</a></p>
    <h1>{{ $page->exists ? ($page->title['en'] ?? $page->slug) : 'New page' }}</h1>
    @if ($page->exists)<p class="adm-muted">Opens at <code>{{ url('/') }}/#{{ $page->slug }}</code></p>@endif
  </div>
  @if ($page->exists)<a class="qs-btn qs-btn--secondary" href="{{ url('/#'.$page->slug) }}" target="_blank" rel="noopener">{!! Catalog::svg('eye') !!}<span>View page</span></a>@endif
</div>
<form method="post" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" data-dirty>
  @csrf @if ($page->exists) @method('put') @endif
  <section class="adm-card adm-form">
    <h2 class="adm-card__title">Banner</h2>
    <div class="adm-grid">
      @include('admin.partials.field', ['f' => ['title', 'l10n', 'Title', ['required' => true]], 'value' => $page->title])
      @include('admin.partials.field', ['f' => ['lead', 'l10n_area', 'Introduction', ['help' => 'The line under the title in the banner.']], 'value' => $page->lead])
      @include('admin.partials.field', ['f' => ['image', 'image', 'Banner photo', ['help' => 'Optional. Shown behind the banner under a navy overlay so the text stays readable.']], 'value' => $page->image])
      @include('admin.partials.field', ['f' => ['icon', 'icon', 'Banner icon'], 'value' => $page->icon])
      @include('admin.partials.field', ['f' => ['date', 'text', 'Date', ['help' => 'Optional, shown above the title.']], 'value' => $page->date])
    </div>
  </section>

  <section class="adm-card adm-form">
    <div class="adm-card__head"><h2 class="adm-card__title">Sections</h2><p class="adm-muted">Add, reorder and edit the parts of the page. Text allows &lt;b&gt;, &lt;em&gt; and &lt;bdi dir="ltr"&gt; for numbers inside Arabic.</p></div>
    <input type="hidden" name="blocks" value="{{ $o ? old('blocks') : json_encode($page->blocks ?? [], JSON_UNESCAPED_UNICODE) }}" data-blocks-field>
  </section>

  <section class="adm-card adm-form">
    <h2 class="adm-card__title">Place in the site</h2>
    <div class="adm-grid">
      @if (! $page->exists)
        @include('admin.partials.field', ['f' => ['slug', 'slug', 'Web address', ['required' => true, 'help' => 'Lower case with dashes, e.g. services-training. Cannot be changed later.']], 'value' => null])
      @endif
      <div class="adm-field">
        <label class="qs-field__label" for="f-section">Menu section</label>
        <select class="qs-input" id="f-section" name="section">
          <option value="">Not in the menu</option>
          @foreach ($sections as $s)<option value="{{ $s->code }}" @selected(($o ? old('section') : $page->section) === $s->code)>{{ $s->title['en'] }}</option>@endforeach
        </select>
      </div>
      @include('admin.partials.field', ['f' => ['sort', 'number', 'Order in the section'], 'value' => $page->sort])
      @include('admin.partials.field', ['f' => ['parent_slug', 'page', 'Parent page', ['help' => 'For sub-pages: shown in the breadcrumb.']], 'value' => $page->parent_slug])
      @include('admin.partials.field', ['f' => ['hidden', 'bool', 'Hide from the menu (the page still opens from links)'], 'value' => $page->hidden])
      @include('admin.partials.field', ['f' => ['no_cta', 'bool', 'Hide the “Become a partner” band at the bottom'], 'value' => $page->no_cta])
      @include('admin.partials.field', ['f' => ['related', 'pages', 'Related pages', ['help' => 'Up to three cards at the end of the page. Empty uses the other pages of the section.']], 'value' => $page->related])
    </div>
  </section>

  <div class="adm-savebar">
    <button class="qs-btn qs-btn--primary" type="submit"><span>{{ $page->exists ? 'Save page' : 'Create page' }}</span></button>
    <a class="qs-btn qs-btn--ghost" href="{{ route('admin.pages.index') }}"><span>Cancel</span></a>
  </div>
</form>
@if ($page->exists)
  @if ($page->isSystem())
    <p class="adm-fine">This page is linked from other parts of the site, so it cannot be deleted. You can hide it from the menu.</p>
  @else
    <form method="post" action="{{ route('admin.pages.destroy', $page) }}" class="adm-danger" data-confirm="Delete this page? Links to it will stop working.">
      @csrf @method('delete')
      <p><strong>Delete this page</strong><br><span class="adm-muted">It disappears from the site and the menu straight away.</span></p>
      <button class="qs-btn qs-btn--danger qs-btn--sm" type="submit">{!! Catalog::svg('trash') !!}<span>Delete</span></button>
    </form>
  @endif
@endif
@endsection
