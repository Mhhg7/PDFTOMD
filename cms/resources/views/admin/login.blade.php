<!doctype html>
<html lang="en" dir="ltr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Sign in · Al-Qawsan dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400..700&family=Cairo:wght@400..700&display=swap">
<link rel="stylesheet" href="{{ asset('design-system/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('design-system/components/bundle.css') }}">
<link rel="stylesheet" href="{{ asset('admin-assets/admin.css') }}?v={{ filemtime(public_path('admin-assets/admin.css')) }}">
</head>
<body class="adm adm-login">
<main class="adm-login__card">
  <img class="adm-login__logo" src="{{ asset('assets/logos/alqawsan-horizontal-color.png') }}" alt="Al-Qawsan Scientific Bureau" width="399" height="234">
  <h1>Website dashboard</h1>
  <p class="adm-muted">Sign in to edit the Al-Qawsan website.</p>
  <form method="post" action="{{ route('admin.login') }}" class="adm-stack">
    @csrf
    <div class="qs-field">
      <label class="qs-field__label" for="email">Email</label>
      <input @class(['qs-input', 'qs-input--error' => $errors->has('email')]) id="email" name="email" type="email" dir="ltr" autocomplete="username" required autofocus value="{{ old('email') }}">
      @error('email')<p class="qs-field__error">{{ $message }}</p>@enderror
    </div>
    <div class="qs-field">
      <label class="qs-field__label" for="password">Password</label>
      <input class="qs-input" id="password" name="password" type="password" autocomplete="current-password" required>
    </div>
    <label class="adm-check"><input type="checkbox" name="remember" value="1"> Keep me signed in on this device</label>
    <button class="qs-btn qs-btn--primary" type="submit"><span>Sign in</span></button>
  </form>
  <p class="adm-fine"><a href="{{ route('site') }}">Back to the website</a></p>
</main>
</body>
</html>
