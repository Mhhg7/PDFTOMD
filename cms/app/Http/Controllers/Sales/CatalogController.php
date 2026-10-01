<?php

namespace App\Http\Controllers\Sales;

use App\Models\SalesCompany;
use App\Models\SalesProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CatalogController extends SalesController
{
    public function index(Request $request)
    {
        $edit = Gate::allows('sales.edit');
        $companies = SalesCompany::query()->when(! $edit, fn ($q) => $q->where('active', true))
            ->withCount(['products' => fn ($q) => $edit ? $q : $q->where('active', true)])
            ->orderBy('sort')->orderBy('name')->get();
        $company = $companies->firstWhere('id', (int) $request->query('company')) ?? $companies->first();
        $q = trim((string) $request->query('q'));

        $products = collect();
        if ($company) {
            $products = $company->products()->when(! $edit, fn ($x) => $x->where('active', true))->get();
        }
        if ($q !== '') {
            // Search runs across every company.
            $needle = mb_strtolower($q);
            $products = SalesProduct::query()->with('company')
                ->when(! $edit, fn ($x) => $x->where('active', true)->whereHas('company', fn ($c) => $c->where('active', true)))
                ->orderBy('brand_name')->get()
                ->filter(fn ($p) => str_contains(mb_strtolower($p->brand_name.' '.$p->active_ingredient.' '.$p->dose.' '.$p->dosage_form), $needle))
                ->values();
        }

        return view('sales.catalog', [
            'companies' => $companies,
            'company' => $q === '' ? $company : null,
            'products' => $products,
            'q' => $q,
            'view' => $request->query('view') === 'table' ? 'table' : 'grid',
            'prices' => Gate::allows('sales.prices'),
            'edit' => $edit,
        ]);
    }
}
