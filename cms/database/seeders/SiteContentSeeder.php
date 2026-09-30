<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\FormRoute;
use App\Models\GalleryItem;
use App\Models\GroupCompany;
use App\Models\Hub;
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
use App\Support\ContentBuilder;
use Illuminate\Database\Seeder;

/**
 * Loads the launch content (data/site.json, exported from the original
 * content.js, plus data/ui-extra.json) into the database. Safe to re-run: every
 * table it fills is emptied first. Form submissions, media and users are untouched.
 */
class SiteContentSeeder extends Seeder
{
    /** Dashboard grouping for interface strings, by key. Unlisted keys go to "general". */
    private const GROUPS = [
        'header' => ['skip', 'brand', 'brandAlt', 'logoAlt', 'cta', 'search', 'searchLabel', 'searchPh', 'searchNone', 'menu', 'close', 'mainNav', 'home', 'tagline'],
        'home' => ['ctaTitle', 'ctaText', 'ctaContact', 'servicesIntro', 'partnersIntro', 'areasIntro', 'newsIntro', 'aboutTeaserH', 'aboutTeaserText', 'aboutPhotoCap', 'keyFigures', 'statsBanner', 'prev', 'next', 'slide', 'newsAll', 'draftSlot', 'draftSlotText', 'partnerSlot'],
        'footer' => ['footerAbout', 'group', 'quick', 'services', 'contact', 'rights', 'privacy', 'terms', 'sitemap', 'fLocation', 'fEmail', 'fCall', 'newsTitle', 'newsText', 'newsPh', 'subscribe', 'newsPrivacy', 'newsDone', 'newsPreview', 'toTop', 'address', 'hours'],
        'map' => ['mapTitle', 'mapHint', 'mapHub', 'mapHq', 'mapServed', 'mapCapital', 'mapDistance', 'mapKm', 'mapHere', 'mapGovs', 'mapHubs', 'hubContacts'],
        'forms' => ['formRequired', 'formPreview', 'formLive', 'formSending', 'formError', 'formSent', 'formReply', 'formAgain', 'send', 'choose'],
        'products' => ['allAreas', 'example', 'registered', 'productSearch', 'productSearchPh', 'thProduct', 'thGeneric', 'thForm', 'thPartner', 'thArea', 'thStatus', 'productsNone', 'productsSource', 'pdTitle', 'pdBrand', 'pdGeneric', 'pdForm', 'pdPack', 'pdArea', 'pdMaker', 'pdReg', 'pdStorage', 'pdLeaflet', 'pdFrom', 'pdDownload'],
        'pages' => ['breadcrumb', 'inSection', 'related', 'readMore', 'openSource', 'eventsEmpty', 'eventsEmptyText', 'galleryNote', 'jobsEmpty', 'jobsEmptyText', 'applyNow', 'sitemapGeneral', 'tempsTitle', 'tempsCold', 'tempsRoom', 'tempsCap', 'partnersNote', 'officeAddress', 'officePhone', 'officeEmail', 'officeHours', 'officeWebsite', 'openMaps', 'groupCompanies', 'groupFigAria', 'groupFigMain1', 'groupFigMain2', 'groupFigB', 'groupFigC1', 'groupFigC2', 'notFound', 'notFoundText', 'backHome'],
    ];

    public function run(): void
    {
        $d = json_decode(file_get_contents(__DIR__.'/data/site.json'), true, 512, JSON_THROW_ON_ERROR);
        $extra = json_decode(file_get_contents(__DIR__.'/data/ui-extra.json'), true, 512, JSON_THROW_ON_ERROR);

        foreach ([UiString::class, NavSection::class, Page::class, Slide::class, Stat::class, Service::class, Area::class,
            Partner::class, Product::class, NewsItem::class, GalleryItem::class, Person::class, GroupCompany::class,
            Hub::class, FormRoute::class] as $m) {
            $m::query()->delete();
        }

        $group = [];
        foreach (self::GROUPS as $g => $keys) {
            foreach ($keys as $k) {
                $group[$k] = $g;
            }
        }
        $ui = [];
        foreach ($d['UI']['en'] as $k => $en) {
            if ($k === 'theme') {
                continue;
            }
            $ui[$k] = ['en' => $en, 'ar' => $d['UI']['ar'][$k] ?? ''];
        }
        foreach ($extra as $k => $v) {
            $ui[$k] = $v;
        }
        foreach ($ui as $k => $v) {
            $g = $group[$k] ?? (str_starts_with($k, 'form.') || str_starts_with($k, 'field.') || str_starts_with($k, 'opt.') ? 'forms'
                : (str_starts_with($k, 'region') ? 'pages' : 'general'));
            UiString::query()->create(['key' => $k, 'group' => $g, 'en' => $v['en'], 'ar' => $v['ar']]);
        }

        foreach ($d['NAV'] as $i => $n) {
            NavSection::query()->create(['code' => $n['id'], 'title' => $n['t'], 'sort' => $i]);
        }

        // Page order follows the menu; pages outside the menu keep their file order after it.
        $order = [];
        foreach ($d['NAV'] as $n) {
            foreach ($n['kids'] ?? [] as $k) {
                $order[$k] = count($order);
            }
        }
        $i = count($order);
        foreach ($d['PAGES'] as $slug => $p) {
            if ($slug === 'media-siphat') {
                continue; // becomes a news article below
            }
            Page::query()->create([
                'slug' => $slug,
                'section' => $p['sec'] ?? null,
                'title' => $p['t'],
                'lead' => $p['lead'] ?? null,
                'icon' => $p['icon'] ?? null,
                'date' => $p['date'] ?? null,
                'parent_slug' => $p['parent'] ?? null,
                'hidden' => (bool) ($p['hidden'] ?? false),
                'no_cta' => (bool) ($p['noCta'] ?? false),
                'blocks' => array_map([$this, 'block'], $p['blocks']),
                'related' => $p['rel'] ?? null,
                'sort' => $order[$slug] ?? $i++,
            ]);
        }

        foreach ($d['SLIDES'] as $i => $s) {
            Slide::query()->create(['heading' => $s['h'], 'text' => $s['p'], 'button_label' => $s['a']['t'], 'button_link' => $s['a']['href'], 'sort' => $i]);
        }
        foreach ($d['STATS'] as $i => $s) {
            Stat::query()->create([
                'icon' => $s['icon'], 'value' => $s['v'], 'label' => $s['label'], 'sub' => $s['sub'] ?? null,
                'chip_icon' => $s['chip']['icon'] ?? null, 'chip_text' => $s['chip']['t'] ?? null, 'sort' => $i,
            ]);
        }
        foreach ($d['SERVICES'] as $i => $s) {
            Service::query()->create(['page_slug' => $s['id'], 'icon' => $s['icon'], 'title' => $s['t'], 'summary' => $s['d'], 'sort' => $i]);
        }
        foreach ($d['AREAS'] as $i => $a) {
            Area::query()->create(['code' => $a['id'], 'icon' => $a['icon'], 'title' => $a['t'], 'sort' => $i]);
        }
        foreach ($d['GROUP'] as $i => $g) {
            GroupCompany::query()->create(['name' => $g['t'], 'description' => $g['d'], 'sort' => $i]);
        }
        foreach ($d['FORMS'] as $kind => $f) {
            FormRoute::query()->create(['kind' => $kind, 'team' => $f['team'], 'reply_time' => $f['when']]);
        }
        FormRoute::query()->create([
            'kind' => 'newsletter',
            'team' => ['en' => 'Marketing', 'ar' => 'فريق التسويق'],
            'reply_time' => ['en' => 'with the next issue', 'ar' => 'مع العدد القادم'],
        ]);
        foreach ($d['HUBS'] as $i => $id) {
            Hub::query()->create([
                'gov_code' => $id, 'sort' => $i,
                'address' => $id === 'IQ-BG' ? $ui['address'] : null,
            ]);
        }

        $siphat = $d['PAGES']['media-siphat'];
        $src = collect($siphat['blocks'])->firstWhere('type', 'source');
        NewsItem::query()->create([
            'slug' => 'media-siphat',
            'date' => $siphat['date'],
            'title' => $siphat['t'],
            'summary' => $siphat['lead'],
            'body' => collect($siphat['blocks'])->firstWhere('type', 'text')['p'],
            'source_text' => $src['text'] ?? null,
            'source_url' => $src['href'] ?? null,
            'related' => $siphat['rel'] ?? null,
        ]);

        Partner::query()->create([
            'name' => 'SIPHAT', 'subtitle' => ['en' => 'Tunisia, since 2025', 'ar' => 'تونس، منذ 2025'],
            'region' => 'north-africa', 'page_slug' => 'partners-siphat', 'sort' => 0,
        ]);

        // The product list is supplied at content upload; until then eight
        // example rows (one per therapeutic area) show the table layout.
        foreach ($d['AREAS'] as $i => $a) {
            Product::query()->create([
                'name' => ['en' => 'Product name', 'ar' => 'اسم المنتج'],
                'generic_name' => ['en' => 'Generic name', 'ar' => 'الاسم العلمي'],
                'form' => ['en' => 'Dosage form, strength', 'ar' => 'الشكل الصيدلاني والتركيز'],
                'partner' => 'Partner',
                'area_code' => $a['id'],
                'is_example' => true,
                'sort' => $i,
            ]);
        }

        $captions = [
            ['Head office, Baghdad', 'المكتب الرئيسي، بغداد'], ['Warehouse', 'المستودع'], ['Cold room, 2 to 8 °C', 'غرفة التبريد 2 إلى 8 °م'],
            ['Delivery fleet', 'أسطول التوزيع'], ['Scientific meeting', 'لقاء علمي'], ['SIPHAT signing', 'توقيع اتفاقية SIPHAT'],
        ];
        foreach ($captions as $i => [$en, $ar]) {
            GalleryItem::query()->create(['caption' => ['en' => $en, 'ar' => $ar], 'sort' => $i]);
        }

        $people = collect($d['PAGES']['about-leadership']['blocks'])->firstWhere('type', 'people')['items'];
        foreach ($people as $i => $role) {
            Person::query()->create(['role' => $role, 'sort' => $i]);
        }

        foreach ([
            'phone' => '+964 XXX XXX XXXX',
            'email' => 'info@alqawsangroup.com',
            'website_url' => 'https://alqawsangroup.com',
            'linkedin_url' => 'https://www.linkedin.com/company/al-qawsan-group',
            'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Qadisiyah+District+Baghdad+Iraq',
            'logo' => 'assets/logos/alqawsan-horizontal-color.png',
            'about_photo' => null,
            'contact_pending' => true,
            'footer_links' => ['about-who', 'partners-list', 'products-areas', 'media-news', 'careers-why', 'contact-office'],
            'meta_description' => 'Al-Qawsan Scientific Bureau: pharmaceutical distribution and scientific promotion across all 18 governorates of Iraq since 2009.',
            'notify_email' => null,
        ] as $k => $v) {
            if (! Setting::query()->whereKey($k)->exists()) {
                Setting::put($k, $v);
            }
        }

        ContentBuilder::forget();
    }

    /** People now come from the People list; facts store "TBC" as an empty value. */
    private function block(array $b): array
    {
        if ($b['type'] === 'people') {
            return ['type' => 'people'];
        }
        if ($b['type'] === 'facts') {
            $b['items'] = array_map(fn ($x) => ['k' => $x['k'], 'v' => is_array($x['v']) ? $x['v'] : ['en' => '', 'ar' => '']], $b['items']);
        }

        return $b;
    }
}
