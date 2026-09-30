<?php

namespace App\Models;

use App\Models\Concerns\BustsSiteCache;
use Illuminate\Database\Eloquent\Model;

class NewsItem extends Model
{
    use BustsSiteCache;

    protected $table = 'news';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'summary' => 'array',
            'body' => 'array',
            'source_text' => 'array',
            'related' => 'array',
            'published' => 'boolean',
        ];
    }
}
