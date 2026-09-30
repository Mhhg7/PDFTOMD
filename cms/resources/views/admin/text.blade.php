@extends('admin.layout', ['title' => 'Interface text'])
@php use App\Admin\Catalog; @endphp
@section('content')
<div class="adm-head"><div><h1>Interface text</h1><p class="adm-muted">Buttons, labels, headings and messages the site shows around the page content, in English and Arabic. <code>%s</code> is filled in by the site (for example a team name), so keep it in both languages.</p></div></div>
<div class="adm-tabs" role="navigation" aria-label="Text groups">
  @foreach ($groups as $g => $label)
    <a href="{{ route('admin.text', ['group' => $g]) }}" @class(['is-on' => $q === '' && $group === $g])>{{ $label }} <span class="adm-muted">{{ $counts[$g] ?? 0 }}</span></a>
  @endforeach
</div>
<form class="adm-search" method="get"><label class="sr-only" for="tq">Search all text</label><input class="qs-input" id="tq" name="q" value="{{ $q }}" placeholder="Search all text, e.g. phone or partner"><button class="qs-btn qs-btn--secondary qs-btn--sm" type="submit">{!! Catalog::svg('search') !!}<span>Search</span></button></form>
<form method="post" action="{{ route('admin.text.update') }}" data-dirty>
  @csrf
  <div class="adm-card adm-texts">
    @forelse ($rows as $r)
      @php $long = mb_strlen($r->en) > 70 || mb_strlen((string) $r->ar) > 70 || str_contains($r->en, "\n"); @endphp
      <div class="adm-text">
        <p class="adm-text__key"><code>{{ $r->key }}</code></p>
        <div class="adm-l10n">
          <label class="adm-l10n__side"><span class="adm-l10n__tag">English</span>
            @if ($long)<textarea class="qs-input" name="text[{{ $r->id }}][en]" rows="3" required>{{ $r->en }}</textarea>@else<input class="qs-input" name="text[{{ $r->id }}][en]" value="{{ $r->en }}" required>@endif
          </label>
          <label class="adm-l10n__side" lang="ar" dir="rtl"><span class="adm-l10n__tag">العربية</span>
            @if ($long)<textarea class="qs-input" name="text[{{ $r->id }}][ar]" rows="3" dir="rtl">{{ $r->ar }}</textarea>@else<input class="qs-input" name="text[{{ $r->id }}][ar]" value="{{ $r->ar }}" dir="rtl">@endif
          </label>
        </div>
      </div>
    @empty
      <p class="adm-empty">No text matches “{{ $q }}”.</p>
    @endforelse
  </div>
  @if ($rows->isNotEmpty())
  <div class="adm-savebar"><button class="qs-btn qs-btn--primary" type="submit"><span>Save text</span></button><span class="adm-muted">Lists (like form options) take one option per line.</span></div>
  @endif
</form>
@endsection
