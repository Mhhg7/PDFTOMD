<?php

namespace App\Models;

use App\Models\Concerns\BustsSiteCache;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    use BustsSiteCache;

    protected $table = 'slides';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'heading' => 'array',
            'text' => 'array',
            'button_label' => 'array',
            'active' => 'boolean',
        ];
    }
}
