<?php

namespace App\Models\Concerns;

use App\Support\ContentBuilder;

/**
 * Any change to site content drops the cached window.QS payload, so the public
 * site shows an edit on the next request.
 */
trait BustsSiteCache
{
    protected static function bootBustsSiteCache(): void
    {
        static::saved(fn () => ContentBuilder::forget());
        static::deleted(fn () => ContentBuilder::forget());
    }
}
