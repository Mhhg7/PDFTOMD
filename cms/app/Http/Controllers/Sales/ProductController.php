<?php

namespace App\Http\Controllers\Sales;

use App\Models\SalesActivity;
use App\Models\SalesCompany;
use App\Models\SalesProduct;
use Illuminate\Http\Request;

class ProductController extends SalesController
{
    public function create(Request $request)
    {
        return $this->form(new SalesProduct(['company_id' => (int) $request->query('company'), 'active' => true]));
    }

    public function store(Request $request)
    {
        $p = new SalesProduct;
        $this->fill($request, $p);
        $p->save();
        SalesActivity::log('product.created', $p->brand_name.' '.$p->dose, $this->priceNote(null, $p->price));

        return redirect()->route('sales.home', ['company' => $p->company_id])->with('ok', "{$p->brand_name} added.");
    }

    public function edit(SalesProduct $product)
    {
        return $this->form($product);
    }

    public function update(Request $request, SalesProduct $product)
    {
        $oldPrice = $product->price;
        $this->fill($request, $product);
        $changed = array_keys($product->getDirty());
        $product->save();
        if ($changed) {
            SalesActivity::log('product.updated', $product->brand_name.' '.$product->dose,
                'Changed: '.implode(', ', array_map(fn ($f) => str_replace('_', ' ', $f), $changed)).$this->priceNote($oldPrice, $product->price));
        }

        return redirect()->route('sales.home', ['company' => $product->company_id])->with('ok', "{$product->brand_name} saved.");
    }

    public function destroy(SalesProduct $product)
    {
        $this->deleteFile($product->photo);
        $product->delete();
        SalesActivity::log('product.deleted', $product->brand_name.' '.$product->dose);

        return redirect()->route('sales.home', ['company' => $product->company_id])->with('ok', "{$product->brand_name} deleted.");
    }

    private function form(SalesProduct $p)
    {
        return view('sales.product', ['p' => $p, 'companies' => SalesCompany::query()->orderBy('sort')->orderBy('name')->get()]);
    }

    private function fill(Request $request, SalesProduct $p): void
    {
        $d = $request->validate([
            'company_id' => ['required', 'exists:sales_companies,id'],
            'brand_name' => ['required', 'string', 'max:120'],
            'active_ingredient' => ['nullable', 'string', 'max:300'],
            'dose' => ['nullable', 'string', 'max:120'],
            'dosage_form' => ['nullable', 'string', 'max:120'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'photo' => self::IMAGE_RULE,
        ]);
        $p->fill([
            'company_id' => (int) $d['company_id'],
            'brand_name' => trim($d['brand_name']),
            'active_ingredient' => trim((string) ($d['active_ingredient'] ?? '')) ?: null,
            'dose' => trim((string) ($d['dose'] ?? '')) ?: null,
            'dosage_form' => trim((string) ($d['dosage_form'] ?? '')) ?: null,
            'price' => isset($d['price']) && $d['price'] !== '' ? $d['price'] : null,
            'notes' => trim((string) ($d['notes'] ?? '')) ?: null,
            'sort' => (int) ($d['sort'] ?? 0),
            'active' => $request->boolean('active'),
        ]);
        if ($request->boolean('remove_photo')) {
            $this->deleteFile($p->photo);
            $p->photo = null;
        }
        $p->photo = $this->storeImage($request->file('photo'), 'photos', $p->photo);
    }

    private function priceNote($old, $new): string
    {
        if ((string) $old === (string) $new) {
            return '';
        }

        return '. Price: '.($old === null ? 'none' : self::money($old)).' → '.($new === null ? 'none' : self::money($new));
    }
}
