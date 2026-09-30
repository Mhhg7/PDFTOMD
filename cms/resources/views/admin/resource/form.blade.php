@extends('admin.layout', ['title' => $def['label'], 'pageChoices' => $pages])
@php use App\Admin\Catalog; @endphp
@section('content')
<div class="adm-head">
  <div>
    <p class="adm-crumb"><a href="{{ route('admin.r.index', $key) }}">{{ $def['label'] }}</a></p>
    <h1>{{ $row->exists ? 'Edit '.$def['one'] : 'Add '.$def['one'] }}</h1>
    <p class="adm-muted">{{ $def['intro'] }}</p>
  </div>
</div>
<form method="post" action="{{ $row->exists ? route('admin.r.update', [$key, $row->getKey()]) : route('admin.r.store', $key) }}" class="adm-card adm-form" data-dirty>
  @csrf @if ($row->exists) @method('put') @endif
  <div class="adm-grid">
    @foreach ($def['fields'] as $f)
      @include('admin.partials.field', ['f' => $f, 'value' => $row->{$f[0]}, 'locked' => $row->exists])
    @endforeach
  </div>
  <div class="adm-formfoot">
    <button class="qs-btn qs-btn--primary" type="submit"><span>{{ $row->exists ? 'Save changes' : 'Add '.$def['one'] }}</span></button>
    <a class="qs-btn qs-btn--ghost" href="{{ route('admin.r.index', $key) }}"><span>Cancel</span></a>
  </div>
</form>
@if ($row->exists && empty($def['nocreate']))
<form method="post" action="{{ route('admin.r.destroy', [$key, $row->getKey()]) }}" class="adm-danger" data-confirm="Delete this {{ $def['one'] }}? This cannot be undone.">
  @csrf @method('delete')
  <p><strong>Delete this {{ $def['one'] }}</strong><br><span class="adm-muted">It disappears from the website straight away. To hide it for now, untick “Show on the site” instead.</span></p>
  <button class="qs-btn qs-btn--danger qs-btn--sm" type="submit">{!! Catalog::svg('trash') !!}<span>Delete</span></button>
</form>
@endif
@endsection
