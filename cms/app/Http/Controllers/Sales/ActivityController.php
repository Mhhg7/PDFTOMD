<?php

namespace App\Http\Controllers\Sales;

use App\Models\SalesActivity;

class ActivityController extends SalesController
{
    public function index()
    {
        return view('sales.activity', ['items' => SalesActivity::query()->with('user')->latest('id')->paginate(50)]);
    }
}
