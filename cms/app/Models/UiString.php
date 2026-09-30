<?php

namespace App\Models;

use App\Models\Concerns\BustsSiteCache;
use Illuminate\Database\Eloquent\Model;

class UiString extends Model
{
    use BustsSiteCache;

    protected $table = 'ui_strings';

    protected $guarded = [];
}
