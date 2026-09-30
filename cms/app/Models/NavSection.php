<?php

namespace App\Models;

use App\Models\Concerns\BustsSiteCache;
use Illuminate\Database\Eloquent\Model;

class NavSection extends Model
{
    use BustsSiteCache;

    protected $table = 'nav_sections';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'title' => 'array',
        ];
    }
}
