<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesActivity extends Model
{
    protected $table = 'sales_activity';

    public const UPDATED_AT = null;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $action, string $subject, ?string $details = null): void
    {
        static::query()->create(['user_id' => auth()->id(), 'action' => $action, 'subject' => mb_substr($subject, 0, 250), 'details' => $details]);
    }
}
