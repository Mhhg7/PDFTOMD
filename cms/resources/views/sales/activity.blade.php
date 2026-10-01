@extends('sales.layout', ['title' => 'Activity'])
@section('content')
<div class="adm-head"><div><h1>Activity</h1><p class="adm-muted">Who changed what in the sales panel, newest first. Prints with prices are listed too.</p></div></div>
<div class="adm-card adm-tablewrap"><table class="qs-table adm-table">
  <thead><tr><th scope="col">When</th><th scope="col">Who</th><th scope="col">What</th><th scope="col">Details</th></tr></thead>
  <tbody>
  @forelse ($items as $a)
    <tr><td><span title="{{ $a->created_at }}">{{ $a->created_at?->diffForHumans() }}</span></td><td>{{ $a->user?->name ?? 'Removed user' }}</td>
      <td><strong>{{ ucfirst(str_replace(['.', '_'], [' ', ' '], $a->action)) }}</strong><br>{{ $a->subject }}</td><td class="adm-muted">{{ $a->details }}</td></tr>
  @empty
    <tr><td colspan="4" class="adm-empty">Nothing yet.</td></tr>
  @endforelse
  </tbody></table></div>
{{ $items->links('admin.partials.pager') }}
@endsection
