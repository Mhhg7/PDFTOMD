<?php

namespace App\Admin;

use App\Models\Page;
use App\Support\Html;

/** Turns submitted dashboard values into clean stored values, by field type. */
class Fields
{
    public static function cleanValue(string $type, $v, array $f = [])
    {
        switch ($type) {
            case 'l10n':
            case 'l10n_area':
                return Html::l10n($v);
            case 'paragraphs':
            case 'lines':
                $list = array_values(array_filter(array_map([Html::class, 'l10n'], is_array($v) ? $v : [])));

                return $list ?: [];
            case 'items':
                $list = [];
                foreach (is_array($v) ? $v : [] as $item) {
                    if (! is_array($item)) {
                        continue;
                    }
                    $row = [];
                    foreach ($f['sub'] ?? [] as $sf) {
                        $sv = self::cleanValue($sf['t'], $item[$sf['n']] ?? null, $sf);
                        if ($sv !== null) {
                            $row[$sf['n']] = $sv;
                        }
                    }
                    if ($row) {
                        $list[] = $row;
                    }
                }
                if (isset($f['max'])) {
                    $list = array_slice($list, 0, $f['max']);
                }

                return $list;
            case 'text':
            case 'gov':
            case 'area':
            case 'select':
                $s = trim(strip_tags((string) (is_scalar($v) ? $v : '')));

                return $s === '' ? null : mb_substr($s, 0, 500);
            case 'slug':
                $s = strtolower(trim((string) (is_scalar($v) ? $v : '')));

                return $s === '' ? null : $s;
            case 'email':
                $s = trim((string) (is_scalar($v) ? $v : ''));

                return filter_var($s, FILTER_VALIDATE_EMAIL) ? $s : null;
            case 'url':
                return Html::url(is_scalar($v) ? (string) $v : null);
            case 'image':
            case 'file':
                return self::assetPath(is_scalar($v) ? (string) $v : '');
            case 'icon':
                return is_string($v) && isset(Catalog::ICONS[$v]) ? $v : null;
            case 'page':
                return is_string($v) && $v !== '' && preg_match('/^[a-z0-9-]+$/', $v) ? $v : null;
            case 'pages':
                $list = array_values(array_filter(is_array($v) ? $v : [], fn ($s) => is_string($s) && preg_match('/^[a-z0-9-]+$/', $s)));

                return $list ?: null;
            case 'number':
                return (int) (is_numeric($v) ? $v : 0);
            case 'bool':
                return (bool) $v;
        }

        return null;
    }

    /** Only files inside public/uploads or public/assets can be linked. */
    public static function assetPath(string $s): ?string
    {
        $s = ltrim(trim($s), '/');
        if ($s === '' || str_contains($s, '..') || ! preg_match('~^(uploads|assets)/[\w./-]+$~', $s)) {
            return null;
        }

        return $s;
    }

    /** Validation rules for a resource form; the values are then cleaned with cleanValue. */
    public static function rules(array $fields, ?string $table, $id): array
    {
        $rules = [];
        foreach ($fields as $f) {
            [$name, $type] = $f;
            $opt = $f[3] ?? [];
            $req = ! empty($opt['required']);
            if (! empty($opt['readonly']) && $id) {
                continue;
            }
            switch ($type) {
                case 'l10n':
                case 'l10n_area':
                    $rules[$name] = ['array'];
                    $rules[$name.'.en'] = array_merge($req ? ['required'] : ['nullable'], ['string', 'max:'.($type === 'l10n' ? 500 : 10000)]);
                    $rules[$name.'.ar'] = ['nullable', 'string', 'max:'.($type === 'l10n' ? 500 : 10000)];
                    break;
                case 'slug':
                    $r = [$req ? 'required' : 'nullable', 'string', 'max:80', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/'];
                    if (! empty($opt['unique'])) {
                        $r[] = 'unique:'.$table.','.$name.($id ? ','.$id : '');
                    }
                    $rules[$name] = $r;
                    break;
                case 'gov':
                    $r = [$req ? 'required' : 'nullable', 'in:'.implode(',', array_keys(Catalog::GOVERNORATES))];
                    if (! empty($opt['unique'])) {
                        $r[] = 'unique:'.$table.','.$name.($id ? ','.$id : '');
                    }
                    $rules[$name] = $r;
                    break;
                case 'select':
                    $rules[$name] = [$req ? 'required' : 'nullable', 'in:'.implode(',', array_keys($opt['choices'] ?? []))];
                    break;
                case 'icon':
                    $rules[$name] = [$req ? 'required' : 'nullable', 'in:'.implode(',', Catalog::PICKABLE)];
                    break;
                case 'page':
                    $rules[$name] = [$req ? 'required' : 'nullable', 'string', 'exists:pages,slug'];
                    break;
                case 'area':
                    $rules[$name] = [$req ? 'required' : 'nullable', 'string', 'exists:areas,code'];
                    break;
                case 'email':
                    $rules[$name] = [$req ? 'required' : 'nullable', 'email', 'max:200'];
                    break;
                case 'url':
                    $rules[$name] = [$req ? 'required' : 'nullable', 'string', 'max:1000', 'regex:~^(https?://|mailto:|tel:|#|/)~i'];
                    break;
                case 'number':
                    $rules[$name] = ['nullable', 'integer', 'min:0', 'max:100000'];
                    break;
                case 'bool':
                    $rules[$name] = ['nullable', 'boolean'];
                    break;
                case 'image':
                case 'file':
                    $rules[$name] = [$req ? 'required' : 'nullable', 'string', 'max:500', 'regex:~^/?(uploads|assets)/[\w./-]+$~', 'not_regex:/\.\./'];
                    break;
                case 'pages':
                    $rules[$name] = ['nullable', 'array'];
                    $rules[$name.'.*'] = ['string', 'exists:pages,slug'];
                    break;
                case 'paragraphs':
                    $rules[$name] = ['nullable', 'string']; // JSON from the editor
                    break;
                default:
                    $rules[$name] = [$req ? 'required' : 'nullable', 'string', 'max:500'];
            }
        }

        return $rules;
    }

    /** Page slugs with titles, for page pickers. */
    public static function pageChoices(): array
    {
        return Page::query()->orderBy('section')->orderBy('sort')->get()
            ->mapWithKeys(fn ($p) => [$p->slug => ($p->title['en'] ?? $p->slug).'  ·  '.$p->slug])->all();
    }
}
