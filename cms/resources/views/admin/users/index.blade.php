@extends('admin.layout', ['title' => 'Users'])
@php use App\Admin\Catalog; @endphp
@section('content')
<div class="adm-head"><div><h1>Users</h1><p class="adm-muted">People who can sign in to this dashboard. Every user can edit everything.</p></div>
<a class="qs-btn qs-btn--primary" href="{{ route('admin.users.create') }}">{!! Catalog::svg('plus') !!}<span>Add user</span></a></div>
<div class="adm-card adm-tablewrap"><table class="qs-table adm-table">
  <thead><tr><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Added</th><th scope="col"><span class="sr-only">Actions</span></th></tr></thead>
  <tbody>
  @foreach ($users as $u)
    <tr><td><a class="adm-rowlink" href="{{ route('admin.users.edit', $u) }}">{{ $u->name }}</a>@if ($u->is(auth()->user())) <span class="adm-muted">(you)</span>@endif</td><td dir="ltr">{{ $u->email }}</td><td>{{ $u->created_at?->format('j M Y') }}</td>
    <td class="adm-actions">@unless ($u->is(auth()->user()))<form method="post" action="{{ route('admin.users.destroy', $u) }}" data-confirm="Remove {{ $u->name }}? They will no longer be able to sign in.">@csrf @method('delete')<button class="qs-btn qs-btn--ghost qs-btn--sm adm-del" type="submit">{!! Catalog::svg('trash') !!}<span>Remove</span></button></form>@endunless</td></tr>
  @endforeach
  </tbody>
</table></div>
@endsection
