<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesCompany extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function products(): HasMany
    {
        return $this->hasMany(SalesProduct::class, 'company_id')->orderBy('sort')->orderBy('id');
    }
}
