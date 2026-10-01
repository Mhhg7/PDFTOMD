@php
    use App\Admin\Catalog;
    use App\Http\Controllers\Sales\SalesController as S;
    // Header hex pattern from the Figma frame: 44 × 38.1 flat-top cells, columns every 33 px.
    $hex = '';
    for ($i = 0; $i < 26; $i++) {
        $x = -22 + 33 * $i;
        for ($j = 0; $j < 6; $j++) {
            $y = ($i % 2 ? -38.105 : -57.158) + 38.105 * $j;
            $hex .= sprintf('<polygon points="%.1f,%.1f %.1f,%.1f %.1f,%.1f %.1f,%.1f %.1f,%.1f %.1f,%.1f"/>',
                $x, $y + 19.05, $x + 11, $y, $x + 33, $y, $x + 44, $y + 19.05, $x + 33, $y + 38.1, $x + 11, $y + 38.1);
        }
    }
    $title = $company === 'all' ? 'All companies' : ($pages[0]['company']->name ?? 'Order sheet');
@endphp
<!doctype html>
<html lang="en" dir="ltr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Order sheet · {{ $title }}{{ $prices ? ' · with prices' : '' }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700&family=Plus+Jakarta+Sans:wght@800&family=Alexandria:wght@500;600&display=swap">
<link rel="stylesheet" href="{{ asset('admin-assets/sheet.css') }}?v={{ filemtime(public_path('admin-assets/sheet.css')) }}">
</head>
<body>
<div class="tb" role="toolbar" aria-label="Order sheet">
  <a class="tb__btn" href="{{ route('sales.home', $company === 'all' ? [] : ['company' => $company]) }}">{!! Catalog::svg('chevLeft') !!}<span>Back</span></a>
  <label class="tb__pick"><span class="sr-only">Company</span>
    <select onchange="location.href=this.value">
      <option value="{{ route('sales.sheet', ['company' => 'all', 'prices' => $prices ? 1 : null]) }}" @selected($company === 'all')>All companies</option>
      @foreach ($companies as $c)<option value="{{ route('sales.sheet', ['company' => $c->id, 'prices' => $prices ? 1 : null]) }}" @selected((string) $company === (string) $c->id)>{{ $c->name }}</option>@endforeach
    </select>
  </label>
  @if ($canPrices)
    <a class="tb__btn {{ $prices ? 'is-on' : '' }}" href="{{ route('sales.sheet', ['company' => $company, 'prices' => $prices ? null : 1]) }}" aria-pressed="{{ $prices ? 'true' : 'false' }}">{!! Catalog::svg($prices ? 'checkCircle' : 'info') !!}<span>{{ $prices ? 'Prices shown' : 'Without prices' }}</span></a>
  @endif
  <span class="tb__info">{{ count($pages) }} {{ count($pages) === 1 ? 'page' : 'pages' }} · A4</span>
  <button class="tb__btn tb__btn--primary" type="button" onclick="window.print()">{!! Catalog::svg('download') !!}<span>Print or save as PDF</span></button>
</div>

<main class="sheets">
@forelse ($pages as $page)
  <section class="sheet" aria-label="{{ $page['company']->name }}, page {{ $loop->iteration }}">
    <header class="sheet__hd">
      <svg class="sheet__hex" viewBox="0 0 794 118" preserveAspectRatio="none" aria-hidden="true"><g fill="none" stroke="#fff" stroke-width="1.1" stroke-linejoin="round">{!! $hex !!}</g></svg>
      <div class="sheet__brand"><img src="{{ asset('assets/logos/alqawsan-horizontal-color.png') }}" alt="Al-Qawsan Scientific Bureau"></div>
      <p class="sheet__title">AL-QAWSAN GROUP</p>
      <div class="sheet__co">@if ($page['company']->logo)<img src="{{ route('sales.file', $page['company']->logo) }}" alt="{{ $page['company']->name }}">@else<span>{{ $page['company']->name }}</span>@endif</div>
    </header>

    <div class="sheet__grid">
      @foreach ($page['products'] as $p)
        <article @class(['card', 'card--price' => $prices])>
          <div class="card__ph">@if ($p->photo)<img src="{{ route('sales.file', $p->photo) }}" alt="">@endif</div>
          <dl class="card__info">
            <div class="card__row card__row--brand"><dt>Brand Name</dt><dd>{{ $p->brand_name }}</dd></div>
            <div class="card__row card__row--ing"><dt>Active Ingredient</dt><dd>{{ $p->active_ingredient }}</dd></div>
            <div class="card__row"><dt>DOSE</dt><dd>{{ $p->dose }}</dd></div>
            <div class="card__row"><dt>DOSAGE FORM</dt><dd>{{ $p->dosage_form }}</dd></div>
            @if ($prices)<div class="card__row card__row--price"><dt>PRICE</dt><dd>{{ S::money($p->price) ?: '—' }}</dd></div>@endif
          </dl>
        </article>
      @endforeach
    </div>

    <footer class="sheet__ft">
      <div class="sheet__contact">
        @if ($s['website'])<p>{!! Catalog::svg('globe') !!}<span>{{ $s['website'] }}</span></p>@endif
        @if ($s['address'])<p>{!! Catalog::svg('mapPin') !!}<span>{{ $s['address'] }}</span></p>@endif
      </div>
      @if ($s['phone'])<p class="sheet__phone">{!! Catalog::svg('phone') !!}<span dir="ltr">{{ $s['phone'] }}</span></p>@endif
      <div @class(['sheet__qr', 'is-empty' => ! $qr])>@if ($qr){!! $qr !!}@else<span>QR</span>@endif</div>
    </footer>
  </section>
@empty
  <p class="sheets__empty">No products to print yet. Add products to this company in the catalog.</p>
@endforelse
</main>
<script>
  // Fit the A4 pages to narrow screens; printing always uses the real size.
  (function () {
    function fit() { document.documentElement.style.setProperty("--z", Math.min(1, (window.innerWidth - 24) / 794).toFixed(3)); }
    fit(); window.addEventListener("resize", fit);
  })();
</script>
</body>
</html>
