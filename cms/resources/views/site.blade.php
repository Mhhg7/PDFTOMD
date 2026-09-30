<!doctype html>
<html lang="en" dir="ltr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Al-Qawsan Scientific Bureau</title>
<meta name="description" content="{{ $description }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('favicon.ico') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400..800&family=Cairo:wght@400..800&display=swap">
<link rel="stylesheet" href="{{ asset('design-system/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('design-system/components/bundle.css') }}">
<link rel="stylesheet" href="{{ asset('site.css') }}?v={{ filemtime(public_path('site.css')) }}">
</head>
<body>
<div id="app" class="qs">
  <a class="skip" href="#main">Skip to content</a>
  <header id="hdr" class="site-header"></header>
  <main id="main"></main>
  <div id="ftr"></div>
  <div id="overlay" hidden></div>
</div>
<script>
window.QS = {!! $qs !!};
window.QS_API = {!! json_encode(['forms' => url('/api/forms'), 'csrf' => csrf_token()], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!};
</script>
<script src="{{ asset('assets/iraq-map.js') }}"></script>
<script src="{{ asset('site.js') }}?v={{ filemtime(public_path('site.js')) }}"></script>
</body>
</html>
