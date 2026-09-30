<?php

namespace App\Models;

use App\Models\Concerns\BustsSiteCache;
use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{
    use BustsSiteCache;

    protected $table = 'hubs';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'address' => 'array',
        ];
    }
}
