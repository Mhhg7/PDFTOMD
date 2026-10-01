@extends('sales.layout', ['title' => 'Users'])
@php use App\Admin\Catalog; use App\Support\Roles; @endphp
@section('content')
<div class="adm-head"><div><h1>Users</h1><p class="adm-muted">Everyone who can sign in. Each user has one role.</p></div>
<a class="qs-btn qs-btn--primary" href="{{ route('sales.users.create') }}">{!! Catalog::svg('plus') !!}<span>Add user</span></a></div>
<div class="adm-card sl-roles">
  @foreach (Roles::LABELS as $r => $label)<div><strong>{{ $label }}</strong><span class="adm-muted">{{ Roles::HELP[$r] }}</span></div>@endforeach
</div>
<div class="adm-card adm-tablewrap"><table class="qs-table adm-table">
  <thead><tr><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Role</th><th scope="col">Status</th><th scope="col"><span class="sr-only">Actions</span></th></tr></thead>
  <tbody>
  @foreach ($users as $x)
    @php $mine = Roles::rank($x->role) <= Roles::rank(auth()->user()->role); @endphp
    <tr @class(['is-off' => ! $x->active])>
      <td>@if ($mine)<a class="adm-rowlink" href="{{ route('sales.users.edit', $x) }}">{{ $x->name }}</a>@else<strong>{{ $x->name }}</strong>@endif @if ($x->is(auth()->user()))<span class="adm-muted">(you)</span>@endif</td>
      <td dir="ltr">{{ $x->email }}</td>
      <td>{{ $x->roleLabel() }}</td>
      <td>@if ($x->active)<span class="qs-badge qs-badge--success"><span class="qs-badge__dot"></span><span>Active</span></span>@else<span class="qs-badge qs-badge--warning"><span class="qs-badge__dot"></span><span>Switched off</span></span>@endif</td>
      <td class="adm-actions">@if ($mine)<a class="qs-btn qs-btn--ghost qs-btn--sm" href="{{ route('sales.users.edit', $x) }}">{!! Catalog::svg('edit') !!}<span>Edit</span></a>@endif</td>
    </tr>
  @endforeach
  </tbody></table></div>
@endsection
