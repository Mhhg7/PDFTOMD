<?php

namespace Database\Seeders;

use App\Models\SalesCompany;
use App\Models\SalesProduct;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Loads the private sales catalog from database/seeders/data/sales/catalog.json
 * (taken from the Figma order sheets). That folder is not in git because the
 * repository is public; it ships only in the private project zip. Without it
 * this seeder does nothing. It never overwrites an existing catalog.
 */
class SalesSeeder extends Seeder
{
    public function run(): void
    {
        $dir = __DIR__.'/data/sales';
        if (! is_file($dir.'/catalog.json')) {
            $this->command?->warn('No private sales catalog found (database/seeders/data/sales). The sales panel starts empty.');

            return;
        }
        if (SalesCompany::query()->exists()) {
            $this->command?->info('Sales catalog already has companies; left as it is.');

            return;
        }
        $d = json_decode(file_get_contents($dir.'/catalog.json'), true, 512, JSON_THROW_ON_ERROR);
        $copy = function (?string $rel, string $to) use ($dir) {
            if (! $rel || ! is_file($dir.'/'.$rel) || str_contains($rel, '..')) {
                return null;
            }
            $path = 'sales/'.$to.'/'.basename($rel);
            Storage::disk('local')->put($path, file_get_contents($dir.'/'.$rel));

            return $path;
        };

        $ids = [];
        foreach ($d['companies'] as $i => $c) {
            $ids[$c['key']] = SalesCompany::query()->create(['name' => $c['name'], 'logo' => $copy($c['logo'] ?? null, 'logos'), 'sort' => $i])->id;
        }
        $sort = [];
        foreach ($d['products'] as $p) {
            $cid = $ids[$p['company']];
            SalesProduct::query()->create([
                'company_id' => $cid,
                'brand_name' => $p['brand_name'],
                'active_ingredient' => $p['active_ingredient'] ?: null,
                'dose' => $p['dose'] ?: null,
                'dosage_form' => $p['dosage_form'] ?: null,
                'photo' => $copy($p['photo'] ?? null, 'photos'),
                'sort' => $sort[$cid] = ($sort[$cid] ?? -1) + 1,
            ]);
        }
        foreach (($d['footer'] ?? []) as $k => $v) {
            if (Setting::get('sales_'.$k) === null) {
                Setting::put('sales_'.$k, $v);
            }
        }
        $this->command?->info(count($d['products']).' products loaded into the sales catalog.');
    }
}
