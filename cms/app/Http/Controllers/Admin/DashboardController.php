<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Resources;
use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Page;
use App\Models\Submission;
use App\Models\UiString;

class DashboardController extends Controller
{
    public function index()
    {
        $counts = ['pages' => Page::query()->count(), 'text' => UiString::query()->count(), 'media' => Media::query()->count()];
        foreach (Resources::all() as $key => $r) {
            $counts[$key] = $r['model']::query()->count();
        }

        return view('admin.dashboard', [
            'counts' => $counts,
            'unread' => Submission::query()->where('is_read', false)->count(),
            'latest' => Submission::query()->latest()->limit(6)->get(),
            'recentPages' => Page::query()->latest('updated_at')->limit(5)->get(),
        ]);
    }
}
