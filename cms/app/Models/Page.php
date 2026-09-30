<?php

namespace App\Models;

use App\Models\Concerns\BustsSiteCache;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use BustsSiteCache;

    protected $table = 'pages';

    protected $guarded = [];

    /** Pages the site code links to by slug. They can be edited but not deleted. */
    public const SYSTEM = [
        'about-who', 'partners-list', 'partners-siphat', 'partners-join', 'products-areas', 'products-list',
        'products-detail', 'media-news', 'careers-apply', 'contact-inquiry', 'sitemap', 'privacy', 'terms',
    ];

    public function isSystem(): bool
    {
        return in_array($this->slug, self::SYSTEM, true);
    }

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'lead' => 'array',
            'blocks' => 'array',
            'related' => 'array',
            'hidden' => 'boolean',
            'no_cta' => 'boolean',
        ];
    }
}
