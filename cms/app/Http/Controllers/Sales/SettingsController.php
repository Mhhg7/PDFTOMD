<?php

namespace App\Http\Controllers\Sales;

use App\Models\SalesActivity;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends SalesController
{
    public function edit()
    {
        return view('sales.settings', ['s' => self::settings()]);
    }

    public function update(Request $request)
    {
        $d = $request->validate([
            'website' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:160'],
            'phone' => ['nullable', 'string', 'max:60'],
            'qr_url' => ['nullable', 'url:http,https', 'max:500'],
            'currency' => ['required', 'string', 'max:10'],
        ]);
        foreach ($d as $k => $v) {
            Setting::put('sales_'.$k, $v === null ? '' : trim($v));
        }
        SalesActivity::log('settings.updated', 'Order sheet settings');

        return back()->with('ok', 'Order sheet settings saved.');
    }
}
