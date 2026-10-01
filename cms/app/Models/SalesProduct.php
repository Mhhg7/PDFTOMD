<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesProduct extends Model
{
    protected $guarded = [];

    /** Prices are left out of any array or JSON form; read them explicitly. */
    protected $hidden = ['price'];

    protected function casts(): array
    {
        return ['active' => 'boolean', 'price' => 'decimal:2'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(SalesCompany::class, 'company_id');
    }
}
