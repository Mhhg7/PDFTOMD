<?php

namespace App\Http\Controllers;

use App\Models\FormRoute;
use App\Models\Setting;
use App\Models\Submission;
use App\Support\FormSpec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class FormController extends Controller
{
    public function store(Request $request, string $kind)
    {
        $spec = FormSpec::KINDS[$kind] ?? abort(404);

        // Honeypot: a filled hidden field means a bot. Answer as if it worked.
        if (filled($request->input('website_url'))) {
            return response()->json(['ok' => true]);
        }

        $rules = array_map(fn ($f) => $f[1], $spec['fields']);
        if (isset($spec['file'])) {
            $rules[$spec['file'][0]] = $spec['file'][2];
        }
        $valid = $request->validate($rules);

        $data = [];
        foreach (array_keys($spec['fields']) as $name) {
            $v = trim((string) ($valid[$name] ?? ''));
            if ($v !== '') {
                $data[$name] = $v;
            }
        }
        $data['lang'] = $request->input('lang') === 'ar' ? 'ar' : 'en';

        $sub = new Submission(['kind' => $kind, 'data' => $data, 'ip' => $request->ip()]);
        if (isset($spec['file']) && $request->hasFile($spec['file'][0])) {
            $file = $request->file($spec['file'][0]);
            $sub->attachment = $file->storeAs('submissions/'.$kind, Str::uuid().'.'.$file->extension(), 'local');
            $sub->attachment_name = Str::limit(preg_replace('/[^\w.\- ]+/u', '_', $file->getClientOriginalName()), 180, '');
        }
        $sub->save();

        $this->notify($sub);

        return response()->json(['ok' => true]);
    }

    private function notify(Submission $sub): void
    {
        $to = FormRoute::query()->where('kind', $sub->kind)->value('email') ?: Setting::get('notify_email');
        if (! $to) {
            return;
        }
        $lines = [FormSpec::label($sub->kind).' #'.$sub->id, ''];
        foreach ($sub->data as $k => $v) {
            $lines[] = FormSpec::fieldLabel($sub->kind, $k).': '.$v;
        }
        $lines[] = '';
        $lines[] = 'Open in the dashboard: '.url('/admin/submissions/'.$sub->id);
        try {
            Mail::raw(implode("\n", $lines), function ($m) use ($to, $sub) {
                $m->to($to)->subject('Website: '.FormSpec::label($sub->kind));
                if (! empty($sub->data['email'])) {
                    $m->replyTo($sub->data['email']);
                }
            });
        } catch (\Throwable $e) {
            Log::warning('Form notification failed: '.$e->getMessage());
        }
    }
}
