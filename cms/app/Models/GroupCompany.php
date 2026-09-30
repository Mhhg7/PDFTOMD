<?php

namespace App\Models;

use App\Models\Concerns\BustsSiteCache;
use Illuminate\Database\Eloquent\Model;

class GroupCompany extends Model
{
    use BustsSiteCache;

    protected $table = 'group_companies';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
            'active' => 'boolean',
        ];
    }
}
