<?php

namespace App\Support;

use App\Models\Area;
use App\Models\Event;
use App\Models\FormRoute;
use App\Models\GalleryItem;
use App\Models\GroupCompany;
use App\Models\Hub;
use App\Models\JobListing;
use App\Models\NavSection;
use App\Models\NewsItem;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Person;
use App\Models\Product;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Slide;
use App\Models\Stat;
use App\Models\UiString;
use Illuminate\Support\Facades\Cache;

/**
 * Turns the database into the window.QS object that public/site.js renders.
 * The shape matches the original hand-written content.js, plus the collections
 * the dashboard manages (partners, products, events, gallery, jobs, people).
 */
class ContentBuilder
{
    public const CACHE_KEY = 'site.qs.v1';

    public const REGIONS = [
        'north-africa' => 'regionNorthAfrica',
        'europe' => 'regionEurope',
        'middle-east' => 'regionMiddleEast',
        'asia' => 'regionAsia',
        'americas' => 'regionAmericas',
    ];

    public static function get(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => (new self)->build());
    }

    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function json(array $qs): string
    {
        return json_encode($qs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }

    public function build(): array
    {
        $ui = ['en' => [], 'ar' => []];
        foreach (UiString::query()->orderBy('id')->get() as $s) {
            $ui['en'][$s->key] = (string) $s->en;
            $ui['ar'][$s->key] = (string) ($s->ar !== null && $s->ar !== '' ? $s->ar : $s->en);
        }
        $L = fn (string $k) => ['en' => $ui['en'][$k] ?? '', 'ar' => $ui['ar'][$k] ?? ''];

        $pages = Page::query()->orderBy('sort')->orderBy('id')->get();
        $news = NewsItem::query()->where('published', true)->orderByDesc('date')->orderBy('sort')->get();
        $known = $pages->pluck('slug')->merge($news->pluck('slug'))->flip();

        $out = [];
        foreach ($pages as $p) {
            $out[$p->slug] = array_filter([
                'sec' => $p->section ?: null,
                't' => $p->title,
                'lead' => $p->lead ?? ['en' => '', 'ar' => ''],
                'icon' => $p->icon ?: null,
                'date' => $p->date ?: null,
                'img' => $p->image ?: null,
                'parent' => $p->parent_slug && isset($known[$p->parent_slug]) ? $p->parent_slug : null,
                'hidden' => $p->hidden ?: null,
                'noCta' => $p->no_cta ?: null,
                'blocks' => array_values(array_map([$this, 'block'], $p->blocks ?? [])),
                'rel' => $this->slugs($p->related, $known),
            ], fn ($v) => $v !== null);
        }
        foreach ($news as $n) {
            $blocks = [['type' => 'text', 'p' => $n->body ?? []]];
            if ($n->source_url) {
                $blocks[] = ['type' => 'source', 'text' => $n->source_text ?? ['en' => '', 'ar' => ''], 'href' => $n->source_url];
            }
            $out[$n->slug] = array_filter([
                'sec' => 'media', 'hidden' => true, 'parent' => isset($known['media-news']) ? 'media-news' : null,
                'date' => $n->date, 't' => $n->title, 'lead' => $n->summary ?? ['en' => '', 'ar' => ''],
                'img' => $n->image ?: null, 'blocks' => $blocks, 'rel' => $this->slugs($n->related, $known),
            ], fn ($v) => $v !== null);
        }

        $nav = [];
        foreach (NavSection::query()->orderBy('sort')->get() as $s) {
            $kids = $pages->where('section', $s->code)->where('hidden', false)->pluck('slug')->values()->all();
            $nav[] = $kids ? ['id' => $s->code, 't' => $s->title, 'kids' => $kids] : ['id' => $s->code, 't' => $s->title];
        }

        $hubs = Hub::query()->orderBy('sort')->get();
        $settings = Setting::query()->get()->pluck('value', 'key')->all();

        return [
            'TBC' => 'TBC',
            'UI' => $ui,
            'NAV' => $nav,
            'PAGES' => $out,
            'STATS' => Stat::query()->where('active', true)->orderBy('sort')->get()->map(fn ($s) => [
                'icon' => $s->icon, 'v' => $s->value, 'label' => $s->label, 'sub' => $s->sub,
                'chip' => $s->chip_text ? ['icon' => $s->chip_icon ?: 'check', 't' => $s->chip_text] : null,
            ])->all(),
            'STATS_BANNER' => $L('statsBanner'),
            'HUBS' => $hubs->pluck('gov_code')->all(),
            'HUB_INFO' => $hubs->mapWithKeys(fn ($h) => [$h->gov_code => ['address' => $h->address, 'phone' => $h->phone]])->all(),
            'SERVICES' => Service::query()->where('active', true)->orderBy('sort')->get()->map(fn ($s) => [
                'id' => $s->page_slug, 'icon' => $s->icon, 't' => $s->title, 'd' => $s->summary,
            ])->all(),
            'AREAS' => Area::query()->where('active', true)->orderBy('sort')->get()->map(fn ($a) => [
                'id' => $a->code, 'icon' => $a->icon, 't' => $a->title,
            ])->all(),
            'NEWS' => $news->map(fn ($n) => [
                'id' => $n->slug, 'date' => $n->date, 't' => $n->title, 'd' => $n->summary, 'img' => $n->image,
            ])->all(),
            'FORMS' => FormRoute::query()->get()->mapWithKeys(fn ($f) => [$f->kind => ['team' => $f->team, 'when' => $f->reply_time]])->all(),
            'SLIDES' => Slide::query()->where('active', true)->orderBy('sort')->get()->map(fn ($s) => [
                'h' => $s->heading, 'p' => $s->text, 'a' => ['href' => $s->button_link ?: 'contact-inquiry', 't' => $s->button_label],
            ])->all(),
            'GROUP' => GroupCompany::query()->where('active', true)->orderBy('sort')->get()->map(fn ($g) => [
                't' => $g->name, 'd' => $g->description, 'logo' => $g->logo,
            ])->all(),
            'REGIONS' => collect(self::REGIONS)->map(fn ($key, $id) => ['id' => $id, 't' => $L($key)])->values()->all(),
            'PARTNERS' => Partner::query()->where('active', true)->orderBy('sort')->get()->map(fn ($p) => [
                'name' => $p->name, 'sub' => $p->subtitle, 'region' => $p->region, 'logo' => $p->logo,
                'page' => $p->page_slug && isset($known[$p->page_slug]) ? $p->page_slug : null, 'url' => $p->website,
            ])->all(),
            'PRODUCTS' => Product::query()->where('active', true)->orderBy('sort')->get()->map(fn ($p) => [
                'id' => $p->id, 'name' => $p->name, 'generic' => $p->generic_name, 'form' => $p->form, 'pack' => $p->pack_size,
                'partner' => $p->partner, 'area' => $p->area_code, 'reg' => $p->registration_no, 'storage' => $p->storage,
                'img' => $p->image, 'leaflet' => $p->leaflet, 'example' => $p->is_example,
            ])->all(),
            'EVENTS' => Event::query()->where('published', true)->orderBy('date')->orderBy('sort')->get()->map(fn ($e) => [
                't' => $e->title, 'date' => $e->date, 'where' => $e->location, 'd' => $e->description,
            ])->all(),
            'GALLERY' => GalleryItem::query()->where('active', true)->orderBy('sort')->get()->map(fn ($g) => [
                'img' => $g->image, 'cap' => $g->caption,
            ])->all(),
            'JOBS' => JobListing::query()->where('active', true)->orderBy('sort')->get()->map(fn ($j) => [
                't' => $j->title, 'where' => $j->location, 'type' => $j->type, 'd' => $j->description,
            ])->all(),
            'PEOPLE' => Person::query()->where('active', true)->orderBy('sort')->get()->map(fn ($p) => [
                'name' => $p->name, 'role' => $p->role, 'img' => $p->photo, 'bio' => $p->bio,
            ])->all(),
            'SETTINGS' => [
                'phone' => $settings['phone'] ?? '',
                'email' => $settings['email'] ?? '',
                'website' => $settings['website_url'] ?? '',
                'linkedin' => $settings['linkedin_url'] ?? '',
                'maps' => $settings['maps_url'] ?? '',
                'logo' => $settings['logo'] ?? 'assets/logos/alqawsan-horizontal-color.png',
                'aboutPhoto' => $settings['about_photo'] ?? null,
                'pending' => (bool) ($settings['contact_pending'] ?? false),
                'footerLinks' => $this->slugs($settings['footer_links'] ?? [], $known) ?? [],
                'year' => (int) date('Y'),
            ],
        ];
    }

    private function slugs($list, $known): ?array
    {
        if (! is_array($list) || ! $list) {
            return null;
        }

        return array_values(array_filter($list, fn ($s) => is_string($s) && isset($known[$s])));
    }

    /** Facts with an empty value render as a "to be confirmed" badge. */
    private function block(array $b): array
    {
        if (($b['type'] ?? '') === 'facts') {
            $b['items'] = array_map(function ($x) {
                $v = $x['v'] ?? null;
                $x['v'] = (! is_array($v) || (($v['en'] ?? '') === '' && ($v['ar'] ?? '') === '')) ? 'TBC' : $v;

                return $x;
            }, $b['items'] ?? []);
        }

        return $b;
    }
}
