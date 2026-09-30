<?php

namespace App\Models;

use App\Models\Concerns\BustsSiteCache;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use BustsSiteCache;

    protected $table = 'products';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'generic_name' => 'array',
            'form' => 'array',
            'pack_size' => 'array',
            'storage' => 'array',
            'is_example' => 'boolean',
            'active' => 'boolean',
        ];
    }
}
