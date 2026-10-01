<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Support\Facades\Storage;

/** Streams catalog photos and logos to signed-in users only. */
class FileController extends SalesController
{
    public function show(string $path)
    {
        abort_unless(preg_match('~^sales/(photos|logos)/[A-Za-z0-9._-]+$~', $path), 404);
        $disk = Storage::disk('local');
        abort_unless($disk->exists($path), 404);

        return response()->file($disk->path($path), [
            'Cache-Control' => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
