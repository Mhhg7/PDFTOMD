<?php

namespace App\Models;

use App\Models\Concerns\BustsSiteCache;
use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    use BustsSiteCache;

    protected $table = 'gallery_items';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'caption' => 'array',
            'active' => 'boolean',
        ];
    }
}
