<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Support\ContentBuilder;

class SiteController extends Controller
{
    public function index()
    {
        return response()
            ->view('site', [
                'qs' => ContentBuilder::json(ContentBuilder::get()),
                'description' => Setting::get('meta_description', ''),
            ])
            ->header('X-Content-Type-Options', 'nosniff');
    }
}
