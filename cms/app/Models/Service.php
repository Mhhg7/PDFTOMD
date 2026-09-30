<?php

namespace App\Models;

use App\Models\Concerns\BustsSiteCache;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use BustsSiteCache;

    protected $table = 'services';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'summary' => 'array',
            'active' => 'boolean',
        ];
    }
}
