<?php

namespace App\Models;

use App\Models\Concerns\BustsSiteCache;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    use BustsSiteCache;

    protected $table = 'stats';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'label' => 'array',
            'sub' => 'array',
            'chip_text' => 'array',
            'active' => 'boolean',
        ];
    }
}
