@extends('admin.layout', ['title' => $def['label']])
@php
    use App\Admin\Catalog;
    $cols = array_values(array_filter($def['fields'], fn ($f) => ! empty($f[3]['list'])));
    $show = function ($row, $f) {
        [$n, $t] = $f;
        $v = $row->{$n};
        return match ($t) {
            'l10n', 'l10n_area' => e(strip_tags($v['en'] ?? '')).(! empty($v['ar']) ? '<br><span class="adm-muted" lang="ar" dir="rtl">'.e(strip_tags($v['ar'])).'</span>' : ''),
            'bool' => $v ? '<span class="qs-badge qs-badge--success"><span class="qs-badge__dot"></span><span>Yes</span></span>' : '<span class="qs-badge qs-badge--warning"><span class="qs-badge__dot"></span><span>No</span></span>',
            'image' => $v ? '<img class="adm-thumb" src="'.e(asset($v)).'" alt="">' : '<span class="adm-muted">None</span>',
            'gov' => e((Catalog::GOVERNORATES[$v][0] ?? $v)),
            default => e((string) $v),
        };
    };
@endphp
@section('content')
<div class="adm-head">
  <div><h1>{{ $def['label'] }}</h1><p class="adm-muted">{{ $def['intro'] }}</p></div>
  @if (empty($def['nocreate']))<a class="qs-btn qs-btn--primary" href="{{ route('admin.r.create', $key) }}">{!! Catalog::svg('plus') !!}<span>Add {{ $def['one'] }}</span></a>@endif
</div>
<form class="adm-search" method="get"><label class="sr-only" for="q">Search</label><input class="qs-input" id="q" name="q" value="{{ $q }}" placeholder="Search {{ strtolower($def['label']) }}"><button class="qs-btn qs-btn--secondary qs-btn--sm" type="submit">{!! Catalog::svg('search') !!}<span>Search</span></button></form>
<div class="adm-card adm-tablewrap">
  <table class="qs-table adm-table">
    <thead><tr>@foreach ($cols as $f)<th scope="col">{{ $f[2] }}</th>@endforeach<th scope="col"><span class="sr-only">Actions</span></th></tr></thead>
    <tbody>
    @forelse ($rows as $row)
      <tr>
        @foreach ($cols as $i => $f)
          <td>@if ($i === 0)<a class="adm-rowlink" href="{{ route('admin.r.edit', [$key, $row->getKey()]) }}">{!! $show($row, $f) !!}</a>@else{!! $show($row, $f) !!}@endif</td>
        @endforeach
        <td class="adm-actions"><a class="qs-btn qs-btn--ghost qs-btn--sm" href="{{ route('admin.r.edit', [$key, $row->getKey()]) }}">{!! Catalog::svg('edit') !!}<span>Edit</span></a></td>
      </tr>
    @empty
      <tr><td colspan="{{ count($cols) + 1 }}" class="adm-empty">Nothing here yet.@if (empty($def['nocreate'])) <a href="{{ route('admin.r.create', $key) }}">Add the first {{ $def['one'] }}</a>.@endif</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
@endsection
