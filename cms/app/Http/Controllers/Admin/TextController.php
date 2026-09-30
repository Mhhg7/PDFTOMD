<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UiString;
use App\Support\ContentBuilder;
use App\Support\Html;
use Illuminate\Http\Request;

/** Interface text: every label, heading and message the site shows outside page content. */
class TextController extends Controller
{
    public const GROUPS = [
        'header' => 'Header and menu',
        'home' => 'Home page',
        'footer' => 'Footer and contact details',
        'pages' => 'Page sections',
        'products' => 'Products',
        'forms' => 'Forms',
        'map' => 'Iraq map',
        'general' => 'General',
    ];

    public function index(Request $request)
    {
        $group = $request->query('group', 'header');
        $q = trim((string) $request->query('q'));
        $rows = UiString::query()->orderBy('id');
        if ($q !== '') {
            $rows->where(fn ($w) => $w->where('key', 'like', "%{$q}%")->orWhere('en', 'like', "%{$q}%")->orWhere('ar', 'like', "%{$q}%"));
        } else {
            $rows->where('group', $group);
        }

        return view('admin.text', [
            'rows' => $rows->get(),
            'groups' => self::GROUPS,
            'group' => $group,
            'q' => $q,
            'counts' => UiString::query()->selectRaw('`group`, count(*) as n')->groupBy('group')->pluck('n', 'group'),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'text' => ['required', 'array'],
            'text.*.en' => ['nullable', 'string', 'max:5000'],
            'text.*.ar' => ['nullable', 'string', 'max:5000'],
        ]);
        $n = 0;
        foreach ($data['text'] as $id => $v) {
            $row = UiString::query()->find($id);
            if (! $row) {
                continue;
            }
            $en = Html::clean($v['en'] ?? '');
            $ar = Html::clean($v['ar'] ?? '');
            if ($en === '' && $row->en !== '') {
                continue; // English is the fallback, so it is never blanked
            }
            if ($row->en !== $en || $row->ar !== $ar) {
                $row->forceFill(['en' => $en, 'ar' => $ar])->saveQuietly();
                $n++;
            }
        }
        ContentBuilder::forget();

        return back()->with('ok', $n ? "Saved {$n} ".($n === 1 ? 'text' : 'texts').'.' : 'Nothing changed.');
    }
}
