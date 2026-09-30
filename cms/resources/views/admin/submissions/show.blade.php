@extends('admin.layout', ['title' => 'Inbox'])
@php use App\Admin\Catalog; use App\Support\FormSpec; @endphp
@section('content')
<div class="adm-head">
  <div><p class="adm-crumb"><a href="{{ route('admin.submissions.index', ['kind' => $s->kind]) }}">{{ FormSpec::label($s->kind) }}</a></p>
  <h1>{{ $s->data['name'] ?? $s->data['email'] ?? 'Message' }}</h1>
  <p class="adm-muted">Received {{ $s->created_at->format('j M Y, H:i') }} · sent from the {{ ($s->data['lang'] ?? 'en') === 'ar' ? 'Arabic' : 'English' }} site</p></div>
  @if (! empty($s->data['email']))<a class="qs-btn qs-btn--primary" href="mailto:{{ $s->data['email'] }}">{!! Catalog::svg('mail') !!}<span>Reply by email</span></a>@endif
</div>
<div class="adm-card">
  <dl class="adm-dl">
    @foreach ($s->data as $k => $v)
      @continue($k === 'lang')
      <div><dt>{{ FormSpec::fieldLabel($s->kind, $k) }}</dt><dd dir="auto">{!! nl2br(e($v)) !!}</dd></div>
    @endforeach
    @if ($s->attachment)
      <div><dt>CV</dt><dd><a class="qs-btn qs-btn--secondary qs-btn--sm" href="{{ route('admin.submissions.file', $s) }}">{!! Catalog::svg('download') !!}<span>{{ $s->attachment_name ?: 'Download' }}</span></a></dd></div>
    @endif
  </dl>
</div>
<div class="adm-row adm-mt">
  <form method="post" action="{{ route('admin.submissions.toggle', $s) }}">@csrf<button class="qs-btn qs-btn--ghost qs-btn--sm" type="submit"><span>Mark as unread</span></button></form>
  <form method="post" action="{{ route('admin.submissions.destroy', $s) }}" data-confirm="Delete this message{{ $s->attachment ? ' and its CV' : '' }}?">@csrf @method('delete')<button class="qs-btn qs-btn--danger qs-btn--sm" type="submit">{!! Catalog::svg('trash') !!}<span>Delete</span></button></form>
</div>
@endsection
