@extends('admin.layout', ['title' => 'Overview'])
@php use App\Admin\Catalog; use App\Support\FormSpec; @endphp
@section('content')
<div class="adm-head"><div><h1>Welcome, {{ auth()->user()->name }}</h1><p class="adm-muted">Everything on alqawsangroup.com is edited here. Changes show on the site as soon as you save.</p></div></div>

<div class="adm-tiles">
  <a class="adm-tile adm-tile--accent" href="{{ route('admin.submissions.index', ['unread' => 1]) }}">{!! Catalog::svg('inbox') !!}<span class="adm-tile__n">{{ $unread }}</span><span class="adm-tile__l">Unread messages</span></a>
  <a class="adm-tile" href="{{ route('admin.pages.index') }}">{!! Catalog::svg('layout') !!}<span class="adm-tile__n">{{ $counts['pages'] }}</span><span class="adm-tile__l">Pages</span></a>
  <a class="adm-tile" href="{{ route('admin.r.index', 'products') }}">{!! Catalog::svg('flask') !!}<span class="adm-tile__n">{{ $counts['products'] }}</span><span class="adm-tile__l">Products</span></a>
  <a class="adm-tile" href="{{ route('admin.r.index', 'news') }}">{!! Catalog::svg('globe') !!}<span class="adm-tile__n">{{ $counts['news'] }}</span><span class="adm-tile__l">News articles</span></a>
  <a class="adm-tile" href="{{ route('admin.media.index') }}">{!! Catalog::svg('image') !!}<span class="adm-tile__n">{{ $counts['media'] }}</span><span class="adm-tile__l">Photos and files</span></a>
</div>

<div class="adm-cols">
  <section class="adm-card">
    <div class="adm-card__head"><h2>Latest messages</h2><a href="{{ route('admin.submissions.index') }}">Open inbox</a></div>
    @forelse ($latest as $s)
      <a class="adm-msg @if(! $s->is_read) is-new @endif" href="{{ route('admin.submissions.show', $s) }}">
        <span class="adm-msg__k">{{ FormSpec::label($s->kind) }}</span>
        <span class="adm-msg__who">{{ $s->data['name'] ?? $s->data['company'] ?? $s->data['email'] ?? '—' }}</span>
        <span class="adm-muted">{{ $s->created_at->diffForHumans() }}</span>
      </a>
    @empty
      <p class="adm-muted">No messages yet. Forms on the site send here.</p>
    @endforelse
  </section>
  <section class="adm-card">
    <div class="adm-card__head"><h2>Quick edits</h2></div>
    <ul class="adm-quick">
      <li><a href="{{ route('admin.r.index', 'slides') }}">{!! Catalog::svg('image') !!}Home page slides</a></li>
      <li><a href="{{ route('admin.r.index', 'stats') }}">{!! Catalog::svg('chartUp') !!}Key figures</a></li>
      <li><a href="{{ route('admin.r.create', 'news') }}">{!! Catalog::svg('plus') !!}Write a news article</a></li>
      <li><a href="{{ route('admin.r.create', 'products') }}">{!! Catalog::svg('plus') !!}Add a product</a></li>
      <li><a href="{{ route('admin.r.create', 'jobs') }}">{!! Catalog::svg('plus') !!}Post an open position</a></li>
      <li><a href="{{ route('admin.settings') }}">{!! Catalog::svg('phone') !!}Phone, email and logo</a></li>
      <li><a href="{{ route('admin.text') }}">{!! Catalog::svg('type') !!}Buttons, labels and footer text</a></li>
    </ul>
    <h3 class="adm-sub">Recently edited pages</h3>
    <ul class="adm-quick">
      @foreach ($recentPages as $p)<li><a href="{{ route('admin.pages.edit', $p) }}">{!! Catalog::svg('layout') !!}{{ $p->title['en'] ?? $p->slug }}</a></li>@endforeach
    </ul>
  </section>
</div>
@endsection
