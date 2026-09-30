@extends('admin.layout', ['title' => 'Settings', 'pageChoices' => $pages])
@section('content')
<div class="adm-head"><div><h1>Settings</h1><p class="adm-muted">Contact details, logo and site-wide options. The address and working hours are in Interface text, under Footer.</p></div></div>
<form method="post" action="{{ route('admin.settings.update') }}" data-dirty>
  @csrf
  @foreach ($groups as $title => $fields)
    <section class="adm-card adm-form">
      <h2 class="adm-card__title">{{ $title }}</h2>
      <div class="adm-grid">
        @foreach ($fields as $f)
          @include('admin.partials.field', ['f' => $f, 'value' => $values[$f[0]] ?? null])
        @endforeach
      </div>
    </section>
  @endforeach
  <div class="adm-savebar"><button class="qs-btn qs-btn--primary" type="submit"><span>Save settings</span></button></div>
</form>
@endsection
