{{-- One dashboard form field. $f = [name, type, label, options]; $value = stored value. --}}
@php
    use App\Admin\Catalog;
    [$name, $type, $label] = $f;
    $opt = $f[3] ?? [];
    $req = ! empty($opt['required']);
    $ro = ! empty($opt['readonly']) && ($locked ?? false);
    $id = 'f-'.$name;
    $err = $errors->has($name) || $errors->has($name.'.en') || $errors->has($name.'.ar');
    $hasOld = session()->hasOldInput();
    $val = $hasOld ? old($name) : $value;
@endphp
<div @class(['adm-field', 'adm-field--wide' => in_array($type, ['l10n', 'l10n_area', 'paragraphs', 'image', 'file', 'pages']), 'has-error' => $err])>
  @if ($type === 'bool')
    <input type="hidden" name="{{ $name }}" value="0">
    <label class="adm-check"><input type="checkbox" id="{{ $id }}" name="{{ $name }}" value="1" @checked($hasOld ? old($name) : $value)> {{ $label }}</label>
  @else
    <p class="qs-field__label" id="{{ $id }}-l">{{ $label }}@if ($req)<span class="qs-field__req" aria-hidden="true">*</span>@endif</p>
    @switch($type)
      @case('l10n')
      @case('l10n_area')
        <div class="adm-l10n" role="group" aria-labelledby="{{ $id }}-l">
          @foreach (['en' => 'English', 'ar' => 'العربية'] as $lc => $ln)
            <label class="adm-l10n__side" @if($lc === 'ar') lang="ar" dir="rtl" @endif>
              <span class="adm-l10n__tag">{{ $ln }}</span>
              @if ($type === 'l10n')
                <input class="qs-input" name="{{ $name }}[{{ $lc }}]" value="{{ $hasOld ? old($name.'.'.$lc) : ($value[$lc] ?? '') }}" @if($lc === 'en' && $req) required @endif dir="{{ $lc === 'ar' ? 'rtl' : 'ltr' }}" maxlength="500">
              @else
                <textarea class="qs-input" name="{{ $name }}[{{ $lc }}]" rows="4" dir="{{ $lc === 'ar' ? 'rtl' : 'ltr' }}" @if($lc === 'en' && $req) required @endif>{{ $hasOld ? old($name.'.'.$lc) : ($value[$lc] ?? '') }}</textarea>
              @endif
            </label>
          @endforeach
        </div>
        @break
      @case('bool')
        @break
      @case('number')
        <input class="qs-input adm-num" id="{{ $id }}" name="{{ $name }}" type="number" min="0" step="1" value="{{ $val ?? 0 }}" aria-labelledby="{{ $id }}-l">
        @break
      @case('select')
      @case('gov')
      @case('area')
      @case('page')
      @case('icon')
        @php
          $choices = match ($type) {
              'select' => $opt['choices'] ?? [],
              'gov' => collect(Catalog::GOVERNORATES)->map(fn ($g) => $g[0].'  ·  '.$g[1])->all(),
              'area' => \App\Models\Area::query()->orderBy('sort')->get()->mapWithKeys(fn ($a) => [$a->code => $a->title['en'] ?? $a->code])->all(),
              'page' => $pages ?? [],
              'icon' => array_combine(Catalog::PICKABLE, Catalog::PICKABLE),
          };
        @endphp
        <div class="adm-selectwrap" @if($type === 'icon') data-icon-select @endif>
          @if ($type === 'icon')<span class="adm-iconprev" aria-hidden="true"></span>@endif
          <select class="qs-input" id="{{ $id }}" name="{{ $name }}" aria-labelledby="{{ $id }}-l" @disabled($ro) @if($req) required @endif>
            <option value="">{{ $req ? 'Choose' : 'None' }}</option>
            @foreach ($choices as $k => $v)<option value="{{ $k }}" @selected((string) $val === (string) $k)>{{ $v }}</option>@endforeach
          </select>
        </div>
        @break
      @case('image')
      @case('file')
        <div class="adm-media" data-media-field data-kind="{{ $type }}">
          <div class="adm-media__prev" data-prev></div>
          <div class="adm-media__ctl">
            <input class="qs-input" id="{{ $id }}" name="{{ $name }}" value="{{ $val }}" dir="ltr" placeholder="{{ $type === 'image' ? 'No photo yet' : 'No file yet' }}" aria-labelledby="{{ $id }}-l" data-path>
            <div class="adm-row">
              <label class="qs-btn qs-btn--secondary qs-btn--sm adm-upload">{!! Catalog::svg('upload') !!}<span>Upload</span><input type="file" accept="{{ $type === 'image' ? 'image/jpeg,image/png,image/webp,image/gif' : '.pdf,image/*' }}" data-upload hidden></label>
              <button class="qs-btn qs-btn--ghost qs-btn--sm" type="button" data-library>{!! Catalog::svg('image') !!}<span>Choose from library</span></button>
              <button class="qs-btn qs-btn--ghost qs-btn--sm" type="button" data-clear>{!! Catalog::svg('x') !!}<span>Remove</span></button>
            </div>
          </div>
        </div>
        @break
      @case('pages')
        <div class="adm-pages" data-pages-field data-name="{{ $name }}" data-value="{{ json_encode(array_values((array) ($val ?? []))) }}"></div>
        @break
      @case('paragraphs')
        <input type="hidden" name="{{ $name }}" value="{{ is_string($val) ? $val : json_encode($val ?? [], JSON_UNESCAPED_UNICODE) }}" data-json-field='{"t":"paragraphs","l":"Paragraph"}'>
        @break
      @default
        <input class="qs-input" id="{{ $id }}" name="{{ $name }}" value="{{ $val }}" aria-labelledby="{{ $id }}-l"
          type="{{ $type === 'email' ? 'email' : ($type === 'url' ? 'text' : 'text') }}" @if(in_array($type, ['email', 'url', 'slug'])) dir="ltr" @endif
          @readonly($ro) @if($req) required @endif maxlength="500">
    @endswitch
  @endif
  @if (! empty($opt['help']))<p class="qs-field__hint">{{ $opt['help'] }}</p>@endif
</div>
