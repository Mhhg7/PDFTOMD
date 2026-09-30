@extends('admin.layout', ['title' => 'Inbox'])
@php use App\Admin\Catalog; use App\Support\FormSpec; @endphp
@section('content')
<div class="adm-head">
  <div><h1>Inbox</h1><p class="adm-muted">Everything sent through the website forms. Set who is alerted by email under Form routing.</p></div>
  @if ($kind)<a class="qs-btn qs-btn--secondary" href="{{ route('admin.submissions.export', ['kind' => $kind]) }}">{!! Catalog::svg('download') !!}<span>Export to Excel (CSV)</span></a>@endif
</div>
<div class="adm-tabs" role="navigation" aria-label="Forms">
  <a href="{{ route('admin.submissions.index') }}" @class(['is-on' => ! $kind])>All</a>
  @foreach ($kinds as $k => $spec)
    <a href="{{ route('admin.submissions.index', ['kind' => $k]) }}" @class(['is-on' => $kind === $k])>{{ $spec['label'] }}@if ($unread[$k] ?? 0) <span class="adm-count">{{ $unread[$k] }}</span>@endif</a>
  @endforeach
</div>
<div class="adm-card adm-tablewrap">
  <table class="qs-table adm-table">
    <thead><tr><th scope="col">From</th><th scope="col">Form</th><th scope="col">Summary</th><th scope="col">Received</th></tr></thead>
    <tbody>
    @forelse ($items as $s)
      <tr @class(['is-new' => ! $s->is_read])>
        <td><a class="adm-rowlink" href="{{ route('admin.submissions.show', $s) }}">@if (! $s->is_read)<span class="adm-dot" aria-label="Unread"></span>@endif{{ $s->data['name'] ?? $s->data['email'] ?? '—' }}</a><br><span class="adm-muted" dir="ltr">{{ $s->data['email'] ?? '' }}</span></td>
        <td>{{ FormSpec::label($s->kind) }}</td>
        <td class="adm-muted">{{ \Illuminate\Support\Str::limit($s->data['message'] ?? $s->data['question'] ?? $s->data['portfolio'] ?? $s->data['role'] ?? $s->data['company'] ?? '', 90) }}@if ($s->attachment) · CV attached @endif</td>
        <td><span title="{{ $s->created_at }}">{{ $s->created_at->diffForHumans() }}</span></td>
      </tr>
    @empty
      <tr><td colspan="4" class="adm-empty">No messages here yet.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
{{ $items->links('admin.partials.pager') }}
@endsection
