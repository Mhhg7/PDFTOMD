<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Blocks;
use App\Admin\Catalog;
use App\Admin\Fields;
use App\Http\Controllers\Controller;
use App\Models\NavSection;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $pages = Page::query()->orderBy('sort')->get();
        $sections = NavSection::query()->orderBy('sort')->get();

        return view('admin.pages.index', ['pages' => $pages, 'sections' => $sections]);
    }

    public function create()
    {
        return $this->form(new Page(['blocks' => [['type' => 'text', 'p' => []]], 'section' => null]));
    }

    public function store(Request $request)
    {
        $page = new Page;
        $this->fill($request, $page);
        $page->save();

        return redirect()->route('admin.pages.edit', $page)->with('ok', 'Page created.');
    }

    public function edit(Page $page)
    {
        return $this->form($page);
    }

    public function update(Request $request, Page $page)
    {
        $this->fill($request, $page);
        $page->save();

        return redirect()->route('admin.pages.edit', $page)->with('ok', 'Page saved. The site shows the change now.');
    }

    public function destroy(Page $page)
    {
        abort_if($page->isSystem(), 403, 'This page is linked from the site itself and cannot be deleted. Hide it instead.');
        $page->delete();

        return redirect()->route('admin.pages.index')->with('ok', 'Page deleted.');
    }

    private function form(Page $page)
    {
        return view('admin.pages.form', [
            'page' => $page,
            'sections' => NavSection::query()->orderBy('sort')->get(),
            'pages' => Fields::pageChoices(),
            'schema' => Blocks::schema(),
            'icons' => Catalog::PICKABLE,
        ]);
    }

    private function fill(Request $request, Page $page): void
    {
        $request->validate([
            'slug' => $page->exists ? ['prohibited'] : ['required', 'string', 'max:80', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/', 'unique:pages,slug', 'unique:news,slug', Rule::notIn(['home', 'main'])],
            'title.en' => ['required', 'string', 'max:300'],
            'title.ar' => ['nullable', 'string', 'max:300'],
            'lead.en' => ['nullable', 'string', 'max:2000'],
            'lead.ar' => ['nullable', 'string', 'max:2000'],
            'section' => ['nullable', 'exists:nav_sections,code'],
            'parent_slug' => ['nullable', 'exists:pages,slug'],
            'icon' => ['nullable', Rule::in(Catalog::PICKABLE)],
            'date' => ['nullable', 'string', 'max:40'],
            'image' => ['nullable', 'string', 'max:500', 'regex:~^/?(uploads|assets)/[\w./-]+$~', 'not_regex:/\.\./'],
            'related' => ['nullable', 'array', 'max:6'],
            'related.*' => ['string', 'exists:pages,slug'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'blocks' => ['required', 'json'],
        ], [], ['title.en' => 'English title']);

        if (! $page->exists) {
            $page->slug = $request->input('slug');
        }
        $page->title = Fields::cleanValue('l10n', $request->input('title'));
        $page->lead = Fields::cleanValue('l10n_area', $request->input('lead'));
        $page->section = $request->input('section') ?: null;
        $page->parent_slug = $request->input('parent_slug') !== $page->slug ? ($request->input('parent_slug') ?: null) : null;
        $page->icon = $request->input('icon') ?: null;
        $page->date = Fields::cleanValue('text', $request->input('date'));
        $page->image = Fields::cleanValue('image', $request->input('image'));
        $page->hidden = $request->boolean('hidden');
        $page->no_cta = $request->boolean('no_cta');
        $page->related = Fields::cleanValue('pages', array_values(array_diff((array) $request->input('related', []), [$page->slug])));
        $page->sort = (int) $request->input('sort', 0);
        $page->blocks = Blocks::clean(json_decode($request->input('blocks'), true));
    }
}
