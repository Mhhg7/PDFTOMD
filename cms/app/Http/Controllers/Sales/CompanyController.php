<?php

namespace App\Http\Controllers\Sales;

use App\Models\SalesActivity;
use App\Models\SalesCompany;
use Illuminate\Http\Request;

class CompanyController extends SalesController
{
    public function index()
    {
        return view('sales.companies', ['companies' => SalesCompany::query()->withCount('products')->orderBy('sort')->orderBy('name')->get()]);
    }

    public function create()
    {
        return view('sales.company', ['c' => new SalesCompany(['active' => true])]);
    }

    public function store(Request $request)
    {
        $c = new SalesCompany;
        $this->fill($request, $c);
        $c->save();
        SalesActivity::log('company.created', $c->name);

        return redirect()->route('sales.companies.index')->with('ok', "{$c->name} added.");
    }

    public function edit(SalesCompany $company)
    {
        return view('sales.company', ['c' => $company]);
    }

    public function update(Request $request, SalesCompany $company)
    {
        $this->fill($request, $company);
        $company->save();
        SalesActivity::log('company.updated', $company->name);

        return redirect()->route('sales.companies.index')->with('ok', "{$company->name} saved.");
    }

    public function destroy(SalesCompany $company)
    {
        if ($company->products()->exists()) {
            return back()->with('err', "{$company->name} still has products. Move or delete them first, or switch the company off.");
        }
        $this->deleteFile($company->logo);
        $company->delete();
        SalesActivity::log('company.deleted', $company->name);

        return redirect()->route('sales.companies.index')->with('ok', "{$company->name} deleted.");
    }

    private function fill(Request $request, SalesCompany $c): void
    {
        $d = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'logo' => self::IMAGE_RULE,
        ]);
        $c->fill(['name' => trim($d['name']), 'sort' => (int) ($d['sort'] ?? 0), 'active' => $request->boolean('active')]);
        if ($request->boolean('remove_logo')) {
            $this->deleteFile($c->logo);
            $c->logo = null;
        }
        $c->logo = $this->storeImage($request->file('logo'), 'logos', $c->logo);
    }
}
