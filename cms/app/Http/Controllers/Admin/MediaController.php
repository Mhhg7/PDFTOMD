<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Support\Html;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** The photo and file library. Files live in public/uploads so the site links to them directly. */
class MediaController extends Controller
{
    /** Tables and columns that can hold a file path, for the "in use" check. */
    private const USES = [
        'pages' => ['image', 'blocks'], 'news' => ['image'], 'partners' => ['logo'], 'products' => ['image', 'leaflet'],
        'people' => ['photo'], 'group_companies' => ['logo'], 'gallery_items' => ['image'], 'settings' => ['value'],
    ];

    public function index(Request $request)
    {
        $items = Media::query()->latest()->paginate(48);

        return view('admin.media', ['items' => $items]);
    }

    public function json(Request $request)
    {
        $type = $request->query('type', 'image');
        $q = Media::query()->latest()->limit(300);
        if ($type === 'image') {
            $q->where('mime', 'like', 'image/%');
        }

        return $q->get()->map(fn ($m) => ['id' => $m->id, 'path' => $m->path, 'name' => $m->original_name, 'image' => $m->isImage(), 'w' => $m->width, 'h' => $m->height]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'files' => ['required', 'array', 'max:20'],
            'files.*' => ['file', 'max:8192', 'mimes:jpg,jpeg,png,webp,gif,pdf'],
        ], [], ['files.*' => 'file']);

        $saved = [];
        foreach ($request->file('files') as $file) {
            $ext = strtolower($file->extension() ?: $file->getClientOriginalExtension());
            $base = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'file';
            $name = Str::limit($base, 60, '').'-'.Str::lower(Str::random(6)).'.'.$ext;
            $dir = date('Y/m');
            Storage::disk('uploads')->putFileAs($dir, $file, $name);
            $path = 'uploads/'.$dir.'/'.$name;
            $size = @getimagesize(public_path($path)) ?: [null, null];
            $saved[] = Media::query()->create([
                'path' => $path, 'original_name' => Str::limit($file->getClientOriginalName(), 190, ''), 'mime' => $file->getMimeType() ?: 'application/octet-stream',
                'size' => $file->getSize(), 'width' => $size[0], 'height' => $size[1],
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(array_map(fn ($m) => ['id' => $m->id, 'path' => $m->path, 'name' => $m->original_name, 'image' => $m->isImage()], $saved));
        }

        return back()->with('ok', count($saved).' '.(count($saved) === 1 ? 'file' : 'files').' uploaded.');
    }

    public function update(Request $request, Media $media)
    {
        $request->validate(['alt.en' => 'nullable|string|max:300', 'alt.ar' => 'nullable|string|max:300']);
        $media->alt = Html::l10n($request->input('alt'));
        $media->save();

        return back()->with('ok', 'Description saved.');
    }

    public function destroy(Media $media)
    {
        if ($this->usage($media->path) && ! request()->boolean('force')) {
            return back()->with('err', 'This file is still used on the site. Replace it there first, or confirm to delete anyway.')->with('force', $media->id);
        }
        Storage::disk('uploads')->delete(Str::after($media->path, 'uploads/'));
        $media->delete();

        return back()->with('ok', 'File deleted.');
    }

    /** How many records link to a file. JSON columns store "/" as "\/", so both spellings are checked. */
    public static function usage(string $path): int
    {
        $forms = [$path, str_replace('/', '\\/', $path)];
        $n = 0;
        foreach (self::USES as $table => $cols) {
            foreach (DB::table($table)->get($cols) as $row) {
                foreach ($cols as $c) {
                    $v = (string) ($row->{$c} ?? '');
                    if ($v !== '' && (str_contains($v, $forms[0]) || str_contains($v, $forms[1]))) {
                        $n++;

                        continue 2;
                    }
                }
            }
        }

        return $n;
    }
}
