<?php

namespace App\Models;

use App\Models\Concerns\BustsSiteCache;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use BustsSiteCache;

    protected $table = 'areas';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'active' => 'boolean',
        ];
    }
}
