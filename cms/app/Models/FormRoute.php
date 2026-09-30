<?php

namespace App\Models;

use App\Models\Concerns\BustsSiteCache;
use Illuminate\Database\Eloquent\Model;

class FormRoute extends Model
{
    use BustsSiteCache;

    protected $table = 'form_routes';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'team' => 'array',
            'reply_time' => 'array',
        ];
    }
}
