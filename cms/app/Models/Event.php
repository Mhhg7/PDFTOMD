<?php

namespace App\Models;

use App\Models\Concerns\BustsSiteCache;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use BustsSiteCache;

    protected $table = 'events';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'location' => 'array',
            'description' => 'array',
            'published' => 'boolean',
        ];
    }
}
