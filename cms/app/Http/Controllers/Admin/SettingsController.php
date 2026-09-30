<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Fields;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public static function fields(): array
    {
        return [
            'Contact details' => [
                ['phone', 'text', 'Main phone', ['help' => 'Shown in the footer, the menu and the head office page.']],
                ['email', 'email', 'Main email'],
                ['contact_pending', 'bool', 'Mark phone, email and hours “to be confirmed”', ['help' => 'Untick once the details are final.']],
                ['maps_url', 'url', 'Google Maps link'],
                ['website_url', 'url', 'Website address'],
                ['linkedin_url', 'url', 'LinkedIn page'],
            ],
            'Brand and photos' => [
                ['logo', 'image', 'Logo', ['help' => 'Horizontal lockup, PNG with a transparent background. Never flipped for Arabic.']],
                ['about_photo', 'image', 'Home page photo', ['help' => 'Beside “About us” on the home page. The caption is the aboutPhotoCap text.']],
            ],
            'Footer' => [
                ['footer_links', 'pages', 'Quick links', ['help' => 'Pages listed under Quick links in the footer.']],
            ],
            'Search engines and alerts' => [
                ['meta_description', 'text', 'Site description', ['help' => 'The summary search engines show, up to 160 characters.']],
                ['notify_email', 'email', 'Default alert email', ['help' => 'Receives an email for each form sent, unless the form has its own address under Form routing.']],
            ],
        ];
    }

    public function edit()
    {
        $values = Setting::query()->get()->pluck('value', 'key')->all();

        return view('admin.settings', ['groups' => self::fields(), 'values' => $values, 'pages' => Fields::pageChoices()]);
    }

    public function update(Request $request)
    {
        $all = array_merge(...array_values(self::fields()));
        $request->validate(Fields::rules($all, null, null));
        foreach ($all as [$name, $type]) {
            $v = $type === 'bool' ? $request->boolean($name) : $request->input($name);
            Setting::put($name, Fields::cleanValue($type, $v));
        }

        return back()->with('ok', 'Settings saved.');
    }
}
