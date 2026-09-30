<?php

namespace App\Admin;

use App\Models;
use App\Support\ContentBuilder;

/**
 * The dashboard's editable collections. Each entry drives the list page, the
 * edit form and validation (see Fields). Field: [name, type, label, options].
 * Options: required, help, list (show in list), readonly (after create),
 * choices (select), unique.
 */
class Resources
{
    public static function all(): array
    {
        $regions = [];
        foreach (array_keys(ContentBuilder::REGIONS) as $r) {
            $regions[$r] = ucwords(str_replace('-', ' ', $r));
        }

        return [
            'slides' => [
                'model' => Models\Slide::class, 'label' => 'Home slides', 'one' => 'slide', 'icon' => 'image', 'group' => 'Home page',
                'intro' => 'The rotating messages at the top of the home page, beside the Iraq map.',
                'title' => 'heading',
                'fields' => [
                    ['heading', 'l10n', 'Heading', ['required' => true, 'list' => true]],
                    ['text', 'l10n_area', 'Text'],
                    ['button_label', 'l10n', 'Button label', ['required' => true]],
                    ['button_link', 'page', 'Button opens page', ['required' => true]],
                    ['sort', 'number', 'Order', ['list' => true]],
                    ['active', 'bool', 'Show on the site', ['list' => true]],
                ],
            ],
            'stats' => [
                'model' => Models\Stat::class, 'label' => 'Key figures', 'one' => 'figure', 'icon' => 'chartUp', 'group' => 'Home page',
                'intro' => 'The figure cards on the home page and the Who We Are page. The line under them is the “statsBanner” text.',
                'title' => 'label',
                'fields' => [
                    ['value', 'text', 'Figure', ['required' => true, 'list' => true, 'help' => 'For example 18, 25+ or 1,500+']],
                    ['label', 'l10n', 'Label', ['required' => true, 'list' => true]],
                    ['icon', 'icon', 'Icon', ['required' => true]],
                    ['sub', 'l10n', 'Context line'],
                    ['chip_text', 'l10n', 'Fact chip', ['help' => 'Small tag under the figure. Leave empty to hide.']],
                    ['chip_icon', 'icon', 'Fact chip icon'],
                    ['sort', 'number', 'Order', ['list' => true]],
                    ['active', 'bool', 'Show on the site', ['list' => true]],
                ],
            ],
            'services' => [
                'model' => Models\Service::class, 'label' => 'Service cards', 'one' => 'service card', 'icon' => 'fileCheck', 'group' => 'Home page',
                'intro' => 'The service cards on the home page and in the footer. Each opens a page; edit the page itself under Pages.',
                'title' => 'title',
                'fields' => [
                    ['title', 'l10n', 'Title', ['required' => true, 'list' => true]],
                    ['summary', 'l10n_area', 'Summary'],
                    ['icon', 'icon', 'Icon', ['required' => true]],
                    ['page_slug', 'page', 'Opens page', ['required' => true, 'list' => true]],
                    ['sort', 'number', 'Order', ['list' => true]],
                    ['active', 'bool', 'Show on the site', ['list' => true]],
                ],
            ],
            'areas' => [
                'model' => Models\Area::class, 'label' => 'Therapeutic areas', 'one' => 'therapeutic area', 'icon' => 'pill', 'group' => 'Products',
                'intro' => 'Used on the home page, the areas page, product filters and the partnership form.',
                'title' => 'title',
                'fields' => [
                    ['title', 'l10n', 'Name', ['required' => true, 'list' => true]],
                    ['code', 'slug', 'Code', ['required' => true, 'unique' => true, 'list' => true, 'help' => 'Short id in lower case, e.g. cardio. Products refer to it.']],
                    ['icon', 'icon', 'Icon', ['required' => true]],
                    ['sort', 'number', 'Order', ['list' => true]],
                    ['active', 'bool', 'Show on the site', ['list' => true]],
                ],
            ],
            'products' => [
                'model' => Models\Product::class, 'label' => 'Products', 'one' => 'product', 'icon' => 'flask', 'group' => 'Products',
                'intro' => 'The product table. Each real product gets its own page. Rows marked “example” only show the layout; delete them once real products are in.',
                'title' => 'name',
                'fields' => [
                    ['name', 'l10n', 'Brand name', ['required' => true, 'list' => true]],
                    ['generic_name', 'l10n', 'Generic name (INN)', ['list' => true]],
                    ['form', 'l10n', 'Dosage form and strength'],
                    ['pack_size', 'l10n', 'Pack size'],
                    ['area_code', 'area', 'Therapeutic area', ['list' => true]],
                    ['partner', 'text', 'Manufacturer / partner', ['list' => true]],
                    ['registration_no', 'text', 'Ministry of Health registration no.'],
                    ['storage', 'l10n', 'Storage conditions'],
                    ['image', 'image', 'Pack photo'],
                    ['leaflet', 'file', 'Patient leaflet (PDF)'],
                    ['is_example', 'bool', 'Layout example (not a real product)', ['list' => true]],
                    ['sort', 'number', 'Order'],
                    ['active', 'bool', 'Show on the site', ['list' => true]],
                ],
            ],
            'partners' => [
                'model' => Models\Partner::class, 'label' => 'Partners', 'one' => 'partner', 'icon' => 'handshake', 'group' => 'Products',
                'intro' => 'Partner tiles on the home page and the partners page, grouped by region. Add a logo to show it in the tile.',
                'title' => 'name',
                'fields' => [
                    ['name', 'text', 'Company name', ['required' => true, 'list' => true]],
                    ['subtitle', 'l10n', 'Line under the name', ['help' => 'For example “Tunisia, since 2025”.']],
                    ['region', 'select', 'Region', ['required' => true, 'choices' => $regions, 'list' => true]],
                    ['logo', 'image', 'Logo'],
                    ['page_slug', 'page', 'Opens page', ['help' => 'Optional page about this partner.']],
                    ['website', 'url', 'Website', ['help' => 'Used when there is no page.']],
                    ['sort', 'number', 'Order', ['list' => true]],
                    ['active', 'bool', 'Show on the site', ['list' => true]],
                ],
            ],
            'news' => [
                'model' => Models\NewsItem::class, 'label' => 'News', 'one' => 'article', 'icon' => 'globe', 'group' => 'Media',
                'intro' => 'Company news. The newest three appear on the home page; each article has its own page.',
                'title' => 'title', 'order' => ['date', 'desc'],
                'fields' => [
                    ['title', 'l10n', 'Headline', ['required' => true, 'list' => true]],
                    ['slug', 'slug', 'Web address', ['required' => true, 'unique' => true, 'help' => 'Lower case with dashes, e.g. media-new-warehouse. The page opens at #media-new-warehouse.']],
                    ['date', 'text', 'Date', ['required' => true, 'list' => true, 'help' => 'Year and month (2025-07) or a full date (2025-07-14).']],
                    ['summary', 'l10n_area', 'Summary', ['help' => 'Shown on the card and under the headline.']],
                    ['body', 'paragraphs', 'Article text'],
                    ['image', 'image', 'Photo'],
                    ['source_text', 'l10n', 'Source note', ['help' => 'For example “Source: Tunisie Numérique, July 2025.”']],
                    ['source_url', 'url', 'Source link'],
                    ['related', 'pages', 'Related pages'],
                    ['published', 'bool', 'Published', ['list' => true]],
                ],
            ],
            'events' => [
                'model' => Models\Event::class, 'label' => 'Events', 'one' => 'event', 'icon' => 'calendar', 'group' => 'Media',
                'intro' => 'Scientific meetings, CME sessions and conferences. With none published, the events page shows a friendly empty state.',
                'title' => 'title', 'order' => ['date', 'asc'],
                'fields' => [
                    ['title', 'l10n', 'Title', ['required' => true, 'list' => true]],
                    ['date', 'text', 'Date', ['required' => true, 'list' => true, 'help' => 'For example 2026-11-12.']],
                    ['location', 'l10n', 'Place'],
                    ['description', 'l10n_area', 'Description'],
                    ['published', 'bool', 'Published', ['list' => true]],
                ],
            ],
            'gallery' => [
                'model' => Models\GalleryItem::class, 'label' => 'Gallery', 'one' => 'photo', 'icon' => 'image', 'group' => 'Media',
                'intro' => 'Photos on the gallery page. Items without a photo show a navy placeholder with the caption.',
                'title' => 'caption',
                'fields' => [
                    ['image', 'image', 'Photo', ['list' => true]],
                    ['caption', 'l10n', 'Caption', ['required' => true, 'list' => true]],
                    ['sort', 'number', 'Order', ['list' => true]],
                    ['active', 'bool', 'Show on the site', ['list' => true]],
                ],
            ],
            'jobs' => [
                'model' => Models\JobListing::class, 'label' => 'Open positions', 'one' => 'position', 'icon' => 'briefcase', 'group' => 'Company',
                'intro' => 'Vacancies on the careers page. Applicants use the job application form.',
                'title' => 'title',
                'fields' => [
                    ['title', 'l10n', 'Job title', ['required' => true, 'list' => true]],
                    ['location', 'l10n', 'Location', ['list' => true]],
                    ['type', 'l10n', 'Type', ['help' => 'For example Full time.']],
                    ['description', 'l10n_area', 'Description'],
                    ['sort', 'number', 'Order'],
                    ['active', 'bool', 'Open', ['list' => true]],
                ],
            ],
            'people' => [
                'model' => Models\Person::class, 'label' => 'Leadership', 'one' => 'person', 'icon' => 'users', 'group' => 'Company',
                'intro' => 'Cards on the leadership page. Without a name the card shows the role and “to be confirmed”.',
                'title' => 'role',
                'fields' => [
                    ['role', 'l10n', 'Role', ['required' => true, 'list' => true]],
                    ['name', 'l10n', 'Name', ['list' => true]],
                    ['photo', 'image', 'Photo'],
                    ['bio', 'l10n_area', 'Short bio'],
                    ['sort', 'number', 'Order', ['list' => true]],
                    ['active', 'bool', 'Show on the site', ['list' => true]],
                ],
            ],
            'group' => [
                'model' => Models\GroupCompany::class, 'label' => 'Group companies', 'one' => 'company', 'icon' => 'hospital', 'group' => 'Company',
                'intro' => 'Companies of Al-Qawsan Group, on the group page and in the footer.',
                'title' => 'name',
                'fields' => [
                    ['name', 'l10n', 'Name', ['required' => true, 'list' => true]],
                    ['description', 'l10n_area', 'Description'],
                    ['logo', 'image', 'Logo'],
                    ['sort', 'number', 'Order', ['list' => true]],
                    ['active', 'bool', 'Show on the site', ['list' => true]],
                ],
            ],
            'hubs' => [
                'model' => Models\Hub::class, 'label' => 'Hubs and branches', 'one' => 'hub', 'icon' => 'warehouse', 'group' => 'Company',
                'intro' => 'Distribution hubs on the maps and the branches page. Baghdad is the head office.',
                'title' => 'gov_code',
                'fields' => [
                    ['gov_code', 'gov', 'Governorate', ['required' => true, 'unique' => true, 'list' => true]],
                    ['address', 'l10n_area', 'Address'],
                    ['phone', 'text', 'Phone', ['list' => true]],
                    ['sort', 'number', 'Order', ['list' => true]],
                ],
            ],
            'nav' => [
                'model' => Models\NavSection::class, 'label' => 'Menu sections', 'one' => 'menu section', 'icon' => 'menu', 'group' => 'Site',
                'intro' => 'Top-level menu entries. Pages join a section from their own settings.',
                'title' => 'title',
                'fields' => [
                    ['title', 'l10n', 'Menu label', ['required' => true, 'list' => true]],
                    ['code', 'slug', 'Code', ['required' => true, 'unique' => true, 'readonly' => true, 'list' => true]],
                    ['sort', 'number', 'Order', ['list' => true]],
                ],
            ],
            'forms' => [
                'model' => Models\FormRoute::class, 'label' => 'Form routing', 'one' => 'form', 'icon' => 'mail', 'group' => 'Site',
                'intro' => 'Who answers each form, how soon, and where the email alert goes. Messages are always kept in the inbox.',
                'title' => 'kind', 'nocreate' => true,
                'fields' => [
                    ['kind', 'slug', 'Form', ['required' => true, 'readonly' => true, 'list' => true]],
                    ['team', 'l10n', 'Team', ['required' => true, 'list' => true]],
                    ['reply_time', 'l10n', 'Reply time', ['required' => true, 'help' => 'Completes the sentence “… will reply within 2 working days.”']],
                    ['email', 'email', 'Alert email', ['list' => true, 'help' => 'Leave empty to use the default alert email from Settings.']],
                ],
            ],
        ];
    }

    public static function get(string $key): array
    {
        return self::all()[$key] ?? abort(404);
    }
}
