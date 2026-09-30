<?php

namespace App\Models;

use App\Models\Concerns\BustsSiteCache;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use BustsSiteCache;

    protected $table = 'partners';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'subtitle' => 'array',
            'active' => 'boolean',
        ];
    }
}
