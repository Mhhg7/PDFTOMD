<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Shared helpers for the private sales panel. Files go to storage/app/private/sales. */
abstract class SalesController extends Controller
{
    public const IMAGE_RULE = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'];

    protected function storeImage(?UploadedFile $file, string $dir, ?string $old = null): ?string
    {
        if (! $file) {
            return $old;
        }
        $path = $file->storeAs('sales/'.$dir, Str::uuid().'.'.strtolower($file->extension()), 'local');
        $this->deleteFile($old);

        return $path;
    }

    protected function deleteFile(?string $path): void
    {
        if ($path && str_starts_with($path, 'sales/')) {
            Storage::disk('local')->delete($path);
        }
    }

    public static function settings(): array
    {
        return [
            'website' => Setting::get('sales_website', 'AL-Qawsangroup.com'),
            'address' => Setting::get('sales_address', 'Iraq, Baghdad, Qadisiyah District'),
            'phone' => Setting::get('sales_phone', '+964 78 555 99999'),
            'qr_url' => Setting::get('sales_qr_url', 'https://alqawsangroup.com'),
            'currency' => Setting::get('sales_currency', 'IQD'),
        ];
    }

    public static function money($price): string
    {
        if ($price === null || $price === '') {
            return '';
        }
        $s = self::settings();
        $n = (float) $price;

        return number_format($n, fmod($n, 1.0) == 0.0 ? 0 : 2).' '.$s['currency'];
    }
}
