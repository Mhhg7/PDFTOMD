<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Support\FormSpec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $kind = $request->query('kind');
        $q = Submission::query()->latest();
        if ($kind && isset(FormSpec::KINDS[$kind])) {
            $q->where('kind', $kind);
        }
        if ($request->query('unread')) {
            $q->where('is_read', false);
        }

        return view('admin.submissions.index', [
            'items' => $q->paginate(30)->withQueryString(),
            'kind' => $kind,
            'kinds' => FormSpec::KINDS,
            'unread' => Submission::query()->where('is_read', false)->selectRaw('kind, count(*) as n')->groupBy('kind')->pluck('n', 'kind'),
        ]);
    }

    public function show(Submission $submission)
    {
        if (! $submission->is_read) {
            $submission->forceFill(['is_read' => true])->save();
        }

        return view('admin.submissions.show', ['s' => $submission]);
    }

    public function toggle(Submission $submission)
    {
        $submission->forceFill(['is_read' => ! $submission->is_read])->save();

        return back()->with('ok', $submission->is_read ? 'Marked as read.' : 'Marked as unread.');
    }

    public function destroy(Submission $submission)
    {
        if ($submission->attachment) {
            Storage::disk('local')->delete($submission->attachment);
        }
        $submission->delete();

        return redirect()->route('admin.submissions.index')->with('ok', 'Message deleted.');
    }

    public function download(Submission $submission)
    {
        abort_unless($submission->attachment && Storage::disk('local')->exists($submission->attachment), 404);

        return Storage::disk('local')->download($submission->attachment, $submission->attachment_name ?: basename($submission->attachment));
    }

    public function export(Request $request)
    {
        $kind = $request->query('kind');
        abort_unless(isset(FormSpec::KINDS[$kind]), 404);
        $fields = array_keys(FormSpec::KINDS[$kind]['fields']);
        $name = 'alqawsan-'.$kind.'-'.date('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($kind, $fields) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // lets Excel read Arabic as UTF-8
            fputcsv($out, array_merge(['ID', 'Received'], array_map(fn ($f) => FormSpec::fieldLabel($kind, $f), $fields), ['Language', 'Attachment']));
            Submission::query()->where('kind', $kind)->orderBy('id')->chunk(200, function ($rows) use ($out, $fields) {
                foreach ($rows as $s) {
                    $cells = [$s->id, $s->created_at?->format('Y-m-d H:i')];
                    foreach ($fields as $f) {
                        $v = (string) ($s->data[$f] ?? '');
                        $cells[] = preg_match('/^[=+\-@\t\r]/', $v) ? "'".$v : $v; // no spreadsheet formulas
                    }
                    $cells[] = $s->data['lang'] ?? '';
                    $cells[] = $s->attachment_name ?? '';
                    fputcsv($out, $cells);
                }
            });
            fclose($out);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
