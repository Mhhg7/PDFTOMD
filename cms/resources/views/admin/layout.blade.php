@php
    use App\Admin\Catalog;
    use App\Admin\Resources;
    $unread = \App\Models\Submission::query()->where('is_read', false)->count();
    $groups = [];
    foreach (Resources::all() as $k => $r) { $groups[$r['group']][$k] = $r; }
    $is = fn ($pattern) => request()->routeIs($pattern);
    $isR = fn ($k) => request()->routeIs('admin.r.*') && request()->route('resource') === $k;
@endphp
<!doctype html>
<html lang="en" dir="ltr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title ?? 'Dashboard' }} · Al-Qawsan dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400..700&family=Cairo:wght@400..700&display=swap">
<link rel="stylesheet" href="{{ asset('design-system/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('design-system/components/bundle.css') }}">
<link rel="stylesheet" href="{{ asset('admin-assets/admin.css') }}?v={{ filemtime(public_path('admin-assets/admin.css')) }}">
</head>
<body class="adm">
<a class="adm-skip" href="#adm-main">Skip to content</a>
<aside class="adm-side" id="adm-side" aria-label="Dashboard menu">
  <div class="adm-side__head">
    <a class="adm-brand" href="{{ route('admin.home') }}"><img src="{{ asset('assets/logos/alqawsan-horizontal-color.png') }}" alt="Al-Qawsan Scientific Bureau" width="399" height="234"></a>
    <button class="adm-iconbtn adm-side__close" type="button" data-side="close" aria-label="Close menu">{!! Catalog::svg('x') !!}</button>
  </div>
  <nav class="adm-nav">
    <a href="{{ route('admin.home') }}" @class(['is-on' => $is('admin.home')])>{!! Catalog::svg('home') !!}Overview</a>
    <a href="{{ route('admin.submissions.index') }}" @class(['is-on' => $is('admin.submissions.*')])>{!! Catalog::svg('inbox') !!}Inbox @if($unread)<span class="adm-count">{{ $unread }}</span>@endif</a>
    <p class="adm-nav__h">Website</p>
    <a href="{{ route('admin.pages.index') }}" @class(['is-on' => $is('admin.pages.*')])>{!! Catalog::svg('layout') !!}Pages</a>
    <a href="{{ route('admin.text') }}" @class(['is-on' => $is('admin.text*')])>{!! Catalog::svg('type') !!}Interface text</a>
    <a href="{{ route('admin.media.index') }}" @class(['is-on' => $is('admin.media.*')])>{!! Catalog::svg('image') !!}Photos and files</a>
    @foreach ($groups as $g => $items)
      <p class="adm-nav__h">{{ $g }}</p>
      @foreach ($items as $k => $r)
        <a href="{{ route('admin.r.index', $k) }}" @class(['is-on' => $isR($k)])>{!! Catalog::svg($r['icon']) !!}{{ $r['label'] }}</a>
      @endforeach
      @if ($g === 'Site')
        <a href="{{ route('admin.settings') }}" @class(['is-on' => $is('admin.settings*')])>{!! Catalog::svg('settings') !!}Settings</a>
        <a href="{{ route('admin.users.index') }}" @class(['is-on' => $is('admin.users.*')])>{!! Catalog::svg('user') !!}Users</a>
      @endif
    @endforeach
  </nav>
</aside>
<div class="adm-scrim" data-side="close"></div>
<div class="adm-body">
  <header class="adm-top">
    <button class="adm-iconbtn adm-burger" type="button" data-side="open" aria-label="Open menu" aria-controls="adm-side">{!! Catalog::svg('menu') !!}</button>
    <p class="adm-top__title">{{ $title ?? 'Dashboard' }}</p>
    <div class="adm-top__tools">
      <a class="qs-btn qs-btn--secondary qs-btn--sm" href="{{ route('site') }}" target="_blank" rel="noopener">{!! Catalog::svg('eye') !!}<span>View site</span></a>
      <span class="adm-user" title="{{ auth()->user()->email }}">{{ auth()->user()->name }}</span>
      <form method="post" action="{{ route('admin.logout') }}">@csrf<button class="adm-iconbtn" type="submit" aria-label="Sign out" title="Sign out">{!! Catalog::svg('logout') !!}</button></form>
    </div>
  </header>
  <main class="adm-main" id="adm-main" tabindex="-1">
    @if (session('ok'))<p class="adm-flash adm-flash--ok" role="status">{!! Catalog::svg('checkCircle') !!}<span>{{ session('ok') }}</span></p>@endif
    @if (session('err'))<p class="adm-flash adm-flash--err" role="alert">{!! Catalog::svg('info') !!}<span>{{ session('err') }}</span></p>@endif
    @if ($errors->any())
      <div class="adm-flash adm-flash--err" role="alert">{!! Catalog::svg('info') !!}<div><strong>Please check the highlighted fields.</strong><ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div></div>
    @endif
    @yield('content')
  </main>
</div>
<script>
window.ADMIN = {!! json_encode([
    'csrf' => csrf_token(),
    'mediaJson' => route('admin.media.json'),
    'mediaUpload' => route('admin.media.store'),
    'icons' => Catalog::ICONS,
    'pickable' => Catalog::PICKABLE,
    'pages' => $pageChoices ?? null,
    'schema' => $blockSchema ?? null,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!};
</script>
<script src="{{ asset('admin-assets/admin.js') }}?v={{ filemtime(public_path('admin-assets/admin.js')) }}"></script>
</body>
</html>
