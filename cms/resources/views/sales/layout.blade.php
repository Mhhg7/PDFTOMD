@php
    use App\Admin\Catalog;
    $u = auth()->user();
    $is = fn ($p) => request()->routeIs($p);
    $navCompanies = \App\Models\SalesCompany::query()->where('active', true)->orderBy('sort')->orderBy('name')->get(['id', 'name']);
@endphp
<!doctype html>
<html lang="en" dir="ltr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title ?? 'Sales' }} · Al-Qawsan sales panel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400..700&family=Cairo:wght@400..700&display=swap">
<link rel="stylesheet" href="{{ asset('design-system/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('design-system/components/bundle.css') }}">
<link rel="stylesheet" href="{{ asset('admin-assets/admin.css') }}?v={{ filemtime(public_path('admin-assets/admin.css')) }}">
</head>
<body class="adm adm--sales">
<a class="adm-skip" href="#adm-main">Skip to content</a>
<aside class="adm-side" id="adm-side" aria-label="Sales menu">
  <div class="adm-side__head">
    <a class="adm-brand" href="{{ route('sales.home') }}"><img src="{{ asset('assets/logos/alqawsan-horizontal-color.png') }}" alt="Al-Qawsan Scientific Bureau" width="399" height="234"></a>
    <button class="adm-iconbtn adm-side__close" type="button" data-side="close" aria-label="Close menu">{!! Catalog::svg('x') !!}</button>
  </div>
  <p class="adm-side__tag">Sales panel · private</p>
  <nav class="adm-nav">
    <a href="{{ route('sales.home') }}" @class(['is-on' => $is('sales.home') && ! request('company')])>{!! Catalog::svg('flask') !!}Catalog</a>
    @foreach ($navCompanies as $c)
      <a class="adm-nav__sub" href="{{ route('sales.home', ['company' => $c->id]) }}" @class(['is-on' => $is('sales.home') && (int) request('company') === $c->id])>{{ $c->name }}</a>
    @endforeach
    <a href="{{ route('sales.sheet') }}" target="_blank" rel="noopener">{!! Catalog::svg('fileCheck') !!}Order sheets</a>
    @can('sales.edit')
      <p class="adm-nav__h">Manage</p>
      <a href="{{ route('sales.products.create') }}" @class(['is-on' => $is('sales.products.create')])>{!! Catalog::svg('plus') !!}Add product</a>
      <a href="{{ route('sales.companies.index') }}" @class(['is-on' => $is('sales.companies.*')])>{!! Catalog::svg('hospital') !!}Companies</a>
    @endcan
    @can('sales.users')
      <a href="{{ route('sales.users.index') }}" @class(['is-on' => $is('sales.users.*')])>{!! Catalog::svg('users') !!}Users</a>
      <a href="{{ route('sales.settings') }}" @class(['is-on' => $is('sales.settings*')])>{!! Catalog::svg('settings') !!}Sheet settings</a>
      <a href="{{ route('sales.activity') }}" @class(['is-on' => $is('sales.activity')])>{!! Catalog::svg('clock') !!}Activity</a>
    @endcan
    @can('website')
      <p class="adm-nav__h">Website</p>
      <a href="{{ route('admin.home') }}">{!! Catalog::svg('globe') !!}Website dashboard</a>
    @endcan
  </nav>
</aside>
<div class="adm-scrim" data-side="close"></div>
<div class="adm-body">
  <header class="adm-top">
    <button class="adm-iconbtn adm-burger" type="button" data-side="open" aria-label="Open menu" aria-controls="adm-side">{!! Catalog::svg('menu') !!}</button>
    <p class="adm-top__title">{{ $title ?? 'Sales' }}</p>
    <div class="adm-top__tools">
      <span class="qs-badge qs-badge--{{ Gate::allows('sales.prices') ? 'success' : 'info' }}" title="{{ \App\Support\Roles::HELP[$u->role] ?? '' }}"><span class="qs-badge__dot"></span><span>{{ $u->roleLabel() }}</span></span>
      <span class="adm-user" title="{{ $u->email }}">{{ $u->name }}</span>
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
<script>window.ADMIN = {!! json_encode(['csrf' => csrf_token(), 'icons' => Catalog::ICONS], JSON_HEX_TAG | JSON_UNESCAPED_SLASHES) !!};</script>
<script src="{{ asset('admin-assets/admin.js') }}?v={{ filemtime(public_path('admin-assets/admin.js')) }}"></script>
</body>
</html>
