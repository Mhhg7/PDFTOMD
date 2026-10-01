@extends('sales.layout', ['title' => 'Users'])
@php use App\Admin\Catalog; use App\Support\Roles; $self = $u->exists && $u->is(auth()->user()); @endphp
@section('content')
<div class="adm-head"><div><p class="adm-crumb"><a href="{{ route('sales.users.index') }}">Users</a></p><h1>{{ $u->exists ? $u->name : 'Add user' }}</h1></div></div>
<form method="post" action="{{ $u->exists ? route('sales.users.update', $u) : route('sales.users.store') }}" class="adm-card adm-form adm-narrow">
  @csrf @if ($u->exists) @method('put') @endif
  <div class="qs-field"><label class="qs-field__label" for="u-name">Name</label><input class="qs-input" id="u-name" name="name" value="{{ old('name', $u->name) }}" required maxlength="120" autocomplete="off"></div>
  <div class="qs-field"><label class="qs-field__label" for="u-email">Email</label><input class="qs-input" id="u-email" name="email" type="email" dir="ltr" value="{{ old('email', $u->email) }}" required autocomplete="off"></div>
  <fieldset class="sl-rolepick" @disabled($self)>
    <legend class="qs-field__label">Role @if ($self)<span class="adm-muted">(you cannot change your own role)</span>@endif</legend>
    @foreach ($roles as $r)
      <label class="sl-rolepick__opt"><input type="radio" name="role" value="{{ $r }}" @checked(old('role', $u->role) === $r) required><span><strong>{{ Roles::LABELS[$r] }}</strong><span class="adm-muted">{{ Roles::HELP[$r] }}</span></span></label>
    @endforeach
  </fieldset>
  <div class="qs-field"><label class="qs-field__label" for="u-pw">{{ $u->exists ? 'New password' : 'Password' }}</label><input class="qs-input" id="u-pw" name="password" type="password" autocomplete="new-password" @required(! $u->exists) minlength="10"><p class="qs-field__hint">At least 10 characters.@if ($u->exists) Leave empty to keep the current one.@endif Share it with the person privately.</p></div>
  <div class="qs-field"><label class="qs-field__label" for="u-pw2">Repeat the password</label><input class="qs-input" id="u-pw2" name="password_confirmation" type="password" autocomplete="new-password"></div>
  @if ($u->exists && ! $self)
    <input type="hidden" name="active" value="0"><label class="adm-check"><input type="checkbox" name="active" value="1" @checked(old('active', $u->active))> Can sign in (untick to switch the account off straight away)</label>
  @endif
  <div class="adm-formfoot"><button class="qs-btn qs-btn--primary" type="submit"><span>{{ $u->exists ? 'Save' : 'Add user' }}</span></button><a class="qs-btn qs-btn--ghost" href="{{ route('sales.users.index') }}"><span>Cancel</span></a></div>
</form>
@if ($u->exists && ! $self)
<form method="post" action="{{ route('sales.users.destroy', $u) }}" class="adm-danger adm-narrow" data-confirm="Remove {{ $u->name }}? They will no longer be able to sign in.">@csrf @method('delete')
  <p><strong>Remove this user</strong><br><span class="adm-muted">Switching the account off keeps their name in the activity log.</span></p>
  <button class="qs-btn qs-btn--danger qs-btn--sm" type="submit">{!! Catalog::svg('trash') !!}<span>Remove</span></button></form>
@endif
@endsection
