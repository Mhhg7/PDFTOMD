<?php

namespace App\Admin;

/**
 * Page block types for the page editor. "fields" blocks are edited in the
 * dashboard; "widget" blocks draw a section from another list (key figures,
 * partners, products...) and carry no text of their own.
 */
class Blocks
{
    public static function schema(): array
    {
        $h = ['n' => 'h', 't' => 'l10n', 'l' => 'Heading'];
        $td = [['n' => 't', 't' => 'l10n', 'l' => 'Title'], ['n' => 'd', 't' => 'l10n_area', 'l' => 'Text']];

        return [
            'text' => ['label' => 'Text', 'fields' => [$h, ['n' => 'p', 't' => 'paragraphs', 'l' => 'Paragraphs']]],
            'list' => ['label' => 'Checklist', 'fields' => [$h, ['n' => 'items', 't' => 'lines', 'l' => 'Points']]],
            'features' => ['label' => 'Feature cards', 'fields' => [$h, ['n' => 'items', 't' => 'items', 'l' => 'Cards', 'sub' => array_merge([['n' => 'icon', 't' => 'icon', 'l' => 'Icon']], $td)]]],
            'steps' => ['label' => 'Numbered steps', 'fields' => [$h, ['n' => 'items', 't' => 'items', 'l' => 'Steps', 'sub' => $td]]],
            'values' => ['label' => 'Value cards', 'fields' => [$h, ['n' => 'items', 't' => 'items', 'l' => 'Cards', 'sub' => $td]]],
            'pair' => ['label' => 'Two highlight cards', 'fields' => [['n' => 'items', 't' => 'items', 'l' => 'Cards', 'max' => 2, 'sub' => $td]]],
            'timeline' => ['label' => 'Timeline', 'fields' => [['n' => 'items', 't' => 'items', 'l' => 'Milestones', 'sub' => array_merge([['n' => 'y', 't' => 'text', 'l' => 'Year']], $td)]]],
            'facts' => ['label' => 'Fact list', 'fields' => [['n' => 'items', 't' => 'items', 'l' => 'Facts', 'sub' => [['n' => 'k', 't' => 'l10n', 'l' => 'Label'], ['n' => 'v', 't' => 'l10n', 'l' => 'Value (empty shows “to be confirmed”)']]]]],
            'image' => ['label' => 'Photo', 'fields' => [$h, ['n' => 'src', 't' => 'image', 'l' => 'Photo'], ['n' => 'cap', 't' => 'l10n', 'l' => 'Caption']]],
            'note' => ['label' => 'Note', 'fields' => [['n' => 'text', 't' => 'l10n_area', 'l' => 'Note text']]],
            'source' => ['label' => 'Source line', 'fields' => [['n' => 'text', 't' => 'l10n', 'l' => 'Source text'], ['n' => 'href', 't' => 'url', 'l' => 'Link']]],
            'form' => ['label' => 'Form', 'fields' => [['n' => 'kind', 't' => 'select', 'l' => 'Which form', 'choices' => ['partner' => 'Partnership request', 'inquiry' => 'Inquiry', 'medical' => 'Medical information', 'apply' => 'Job application']]]],
            'stats' => ['label' => 'Key figures', 'widget' => 'Shows the key figure cards. Edit them under Key figures.'],
            'people' => ['label' => 'Leadership cards', 'widget' => 'Shows the people from Leadership.'],
            'group' => ['label' => 'Group companies', 'widget' => 'Shows the group diagram and companies from Group companies.'],
            'partners' => ['label' => 'Partners by region', 'widget' => 'Shows the partners from Partners, grouped by region.'],
            'areas' => ['label' => 'Therapeutic areas', 'widget' => 'Shows the areas from Therapeutic areas.'],
            'products' => ['label' => 'Product table', 'widget' => 'Shows the searchable table from Products.'],
            'productDetail' => ['label' => 'Product page fields', 'widget' => 'Shows the list of fields each product page carries.'],
            'news' => ['label' => 'News cards', 'widget' => 'Shows all published articles from News.'],
            'events' => ['label' => 'Events', 'widget' => 'Shows published events, or an empty state.'],
            'gallery' => ['label' => 'Photo gallery', 'widget' => 'Shows the photos from Gallery.'],
            'jobs' => ['label' => 'Open positions', 'widget' => 'Shows open positions, or an empty state.'],
            'map' => ['label' => 'Iraq coverage map', 'widget' => 'The interactive map with the governorate buttons.'],
            'hubs' => ['label' => 'Hub cards', 'widget' => 'Shows the hubs from Hubs and branches.'],
            'temps' => ['label' => 'Storage ranges chart', 'widget' => 'The cold-chain temperature chart.'],
            'office' => ['label' => 'Head office contacts', 'widget' => 'Address, phone, email and hours from Settings and Interface text.'],
            'sitemap' => ['label' => 'Sitemap', 'widget' => 'Lists every page in the menu.'],
        ];
    }

    /** Keeps only known block types and fields, cleaned by type. */
    public static function clean($blocks): array
    {
        $schema = self::schema();
        $out = [];
        foreach (is_array($blocks) ? $blocks : [] as $b) {
            $type = is_array($b) ? ($b['type'] ?? null) : null;
            if (! is_string($type) || ! isset($schema[$type])) {
                continue;
            }
            $clean = ['type' => $type];
            foreach ($schema[$type]['fields'] ?? [] as $f) {
                $v = Fields::cleanValue($f['t'], $b[$f['n']] ?? null, $f);
                if ($v !== null) {
                    $clean[$f['n']] = $v;
                }
            }
            $out[] = $clean;
        }

        return $out;
    }
}
