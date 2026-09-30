<?php

namespace App\Models;

use App\Models\Concerns\BustsSiteCache;
use Illuminate\Database\Eloquent\Model;

class JobListing extends Model
{
    use BustsSiteCache;

    protected $table = 'jobs_listings';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'location' => 'array',
            'type' => 'array',
            'description' => 'array',
            'active' => 'boolean',
        ];
    }
}
