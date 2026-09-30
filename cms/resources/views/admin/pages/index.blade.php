@extends('admin.layout', ['title' => 'Pages'])
@php use App\Admin\Catalog; @endphp
@section('content')
<div class="adm-head">
  <div><h1>Pages</h1><p class="adm-muted">Every page of the site, grouped by its menu section. Open a page to edit its title, banner and sections.</p></div>
  <a class="qs-btn qs-btn--primary" href="{{ route('admin.pages.create') }}">{!! Catalog::svg('plus') !!}<span>New page</span></a>
</div>
@php $groups = $sections->map(fn ($s) => ['t' => $s->title['en'], 'code' => $s->code])->push(['t' => 'Outside the menu', 'code' => null]); @endphp
@foreach ($groups as $g)
  @php $list = $pages->filter(fn ($p) => $p->section === $g['code'] || ($g['code'] === null && ! $sections->pluck('code')->contains($p->section))); @endphp
  @continue($list->isEmpty())
  <section class="adm-card adm-tablewrap">
    <h2 class="adm-card__title">{{ $g['t'] }}</h2>
    <table class="qs-table adm-table">
      <thead><tr><th scope="col">Title</th><th scope="col">Address</th><th scope="col">Sections</th><th scope="col">Shown in menu</th><th scope="col"><span class="sr-only">Actions</span></th></tr></thead>
      <tbody>
      @foreach ($list as $p)
        <tr>
          <td><a class="adm-rowlink" href="{{ route('admin.pages.edit', $p) }}">{{ $p->title['en'] ?? '' }}</a><br><span class="adm-muted" lang="ar" dir="rtl">{{ $p->title['ar'] ?? '' }}</span></td>
          <td><code>#{{ $p->slug }}</code></td>
          <td class="adm-muted">{{ collect($p->blocks)->pluck('type')->map(fn ($t) => \App\Admin\Blocks::schema()[$t]['label'] ?? $t)->implode(', ') }}</td>
          <td>@if ($p->hidden || ! $p->section)<span class="qs-badge qs-badge--warning"><span class="qs-badge__dot"></span><span>No</span></span>@else<span class="qs-badge qs-badge--success"><span class="qs-badge__dot"></span><span>Yes</span></span>@endif</td>
          <td class="adm-actions"><a class="qs-btn qs-btn--ghost qs-btn--sm" href="{{ url('/#'.$p->slug) }}" target="_blank" rel="noopener">{!! Catalog::svg('eye') !!}<span>View</span></a><a class="qs-btn qs-btn--ghost qs-btn--sm" href="{{ route('admin.pages.edit', $p) }}">{!! Catalog::svg('edit') !!}<span>Edit</span></a></td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </section>
@endforeach
@endsection
