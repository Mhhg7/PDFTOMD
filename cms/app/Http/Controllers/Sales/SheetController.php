<?php

namespace App\Http\Controllers\Sales;

use App\Models\SalesActivity;
use App\Models\SalesCompany;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** The A4 order sheets from the Figma "order sheet" design, 4 products per page. */
class SheetController extends SalesController
{
    public function show(Request $request, string $company = 'all')
    {
        $prices = $request->boolean('prices') && Gate::allows('sales.prices');
        $companies = SalesCompany::query()->where('active', true)->orderBy('sort')->orderBy('name')
            ->with(['products' => fn ($q) => $q->where('active', true)])->get();
        $all = $companies;
        if ($company !== 'all') {
            $companies = $companies->where('id', (int) $company)->values();
            abort_if($companies->isEmpty(), 404);
        }

        $pages = [];
        foreach ($companies as $c) {
            foreach ($c->products->chunk(4) as $chunk) {
                $pages[] = ['company' => $c, 'products' => $chunk->values()];
            }
        }
        $s = self::settings();
        if ($prices) {
            SalesActivity::log('sheet.prices', 'Order sheet with prices: '.($company === 'all' ? 'all companies' : $companies->first()->name));
        }

        return view('sales.sheet', [
            'pages' => $pages,
            'prices' => $prices,
            'canPrices' => Gate::allows('sales.prices'),
            'company' => $company,
            'companies' => $all,
            's' => $s,
            'qr' => $s['qr_url'] ? $this->qr($s['qr_url']) : null,
        ]);
    }

    private function qr(string $text): string
    {
        $writer = new Writer(new ImageRenderer(new RendererStyle(220, 1), new SvgImageBackEnd));
        $svg = $writer->writeString($text);

        return preg_replace('/^<\?xml[^>]*>\s*/', '', $svg);
    }
}
