@extends('admin.layout', ['title' => 'Users'])
@section('content')
<div class="adm-head"><div><p class="adm-crumb"><a href="{{ route('admin.users.index') }}">Users</a></p><h1>{{ $user->exists ? 'Edit '.$user->name : 'Add user' }}</h1></div></div>
<form method="post" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" class="adm-card adm-form adm-narrow">
  @csrf @if ($user->exists) @method('put') @endif
  <div class="qs-field"><label class="qs-field__label" for="u-name">Name</label><input class="qs-input" id="u-name" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name"></div>
  <div class="qs-field"><label class="qs-field__label" for="u-email">Email</label><input class="qs-input" id="u-email" name="email" type="email" dir="ltr" value="{{ old('email', $user->email) }}" required autocomplete="email"></div>
  <div class="qs-field"><label class="qs-field__label" for="u-pw">{{ $user->exists ? 'New password' : 'Password' }}</label><input class="qs-input" id="u-pw" name="password" type="password" autocomplete="new-password" @required(! $user->exists) minlength="10"><p class="qs-field__hint">At least 10 characters.@if ($user->exists) Leave empty to keep the current password.@endif</p></div>
  <div class="qs-field"><label class="qs-field__label" for="u-pw2">Repeat the password</label><input class="qs-input" id="u-pw2" name="password_confirmation" type="password" autocomplete="new-password"></div>
  <div class="adm-formfoot"><button class="qs-btn qs-btn--primary" type="submit"><span>{{ $user->exists ? 'Save' : 'Add user' }}</span></button><a class="qs-btn qs-btn--ghost" href="{{ route('admin.users.index') }}"><span>Cancel</span></a></div>
</form>
@endsection
