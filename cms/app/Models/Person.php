<?php

namespace App\Models;

use App\Models\Concerns\BustsSiteCache;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use BustsSiteCache;

    protected $table = 'people';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'role' => 'array',
            'bio' => 'array',
            'active' => 'boolean',
        ];
    }
}
