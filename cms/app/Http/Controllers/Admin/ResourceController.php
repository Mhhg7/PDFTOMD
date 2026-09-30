<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Fields;
use App\Admin\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/** List, create, edit and delete for every collection defined in App\Admin\Resources. */
class ResourceController extends Controller
{
    public function index(Request $request, string $resource)
    {
        $def = Resources::get($resource);
        $q = $def['model']::query();
        [$col, $dir] = $def['order'] ?? [in_array('sort', array_column($def['fields'], 0), true) ? 'sort' : 'id', 'asc'];
        $rows = $q->orderBy($col, $dir)->orderBy('id')->get();
        if ($s = trim((string) $request->query('q'))) {
            $needle = mb_strtolower($s);
            $rows = $rows->filter(fn ($r) => str_contains(mb_strtolower(json_encode($r->toArray(), JSON_UNESCAPED_UNICODE)), $needle));
        }

        return view('admin.resource.index', ['key' => $resource, 'def' => $def, 'rows' => $rows, 'q' => $s]);
    }

    public function create(string $resource)
    {
        $def = Resources::get($resource);
        abort_if(! empty($def['nocreate']), 404);

        return view('admin.resource.form', ['key' => $resource, 'def' => $def, 'row' => new $def['model'](['active' => true, 'published' => true]), 'pages' => Fields::pageChoices()]);
    }

    public function store(Request $request, string $resource)
    {
        $def = Resources::get($resource);
        abort_if(! empty($def['nocreate']), 404);
        $row = new $def['model'];
        $this->fill($request, $def, $row);
        $row->save();

        return redirect()->route('admin.r.edit', [$resource, $row->getKey()])->with('ok', ucfirst($def['one']).' added.');
    }

    public function edit(string $resource, int $id)
    {
        $def = Resources::get($resource);

        return view('admin.resource.form', ['key' => $resource, 'def' => $def, 'row' => $def['model']::query()->findOrFail($id), 'pages' => Fields::pageChoices()]);
    }

    public function update(Request $request, string $resource, int $id)
    {
        $def = Resources::get($resource);
        $row = $def['model']::query()->findOrFail($id);
        $this->fill($request, $def, $row);
        $row->save();

        return redirect()->route('admin.r.edit', [$resource, $row->getKey()])->with('ok', 'Saved. The site shows the change now.');
    }

    public function destroy(string $resource, int $id)
    {
        $def = Resources::get($resource);
        abort_if(! empty($def['nocreate']), 403);
        $def['model']::query()->findOrFail($id)->delete();

        return redirect()->route('admin.r.index', $resource)->with('ok', ucfirst($def['one']).' deleted.');
    }

    private function fill(Request $request, array $def, $row): void
    {
        $table = $row->getTable();
        $request->validate(Fields::rules($def['fields'], $table, $row->getKey()), [], collect($def['fields'])->mapWithKeys(fn ($f) => [$f[0] => $f[2], $f[0].'.en' => $f[2].' (English)', $f[0].'.ar' => $f[2].' (Arabic)'])->all());
        foreach ($def['fields'] as $f) {
            [$name, $type] = $f;
            $opt = $f[3] ?? [];
            if (! empty($opt['readonly']) && $row->exists) {
                continue;
            }
            $v = $request->input($name);
            if ($type === 'paragraphs') {
                $v = json_decode((string) $v, true);
            }
            if ($type === 'bool') {
                $v = $request->boolean($name);
            }
            $row->{$name} = Fields::cleanValue($type, $v, $opt);
        }
    }
}
