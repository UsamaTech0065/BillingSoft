<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Support\CurrentCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount(['products', 'invoices'])->orderBy('company_name')->get();
        $activeCompany = CurrentCompany::get();

        return view('companies.index', compact('companies', 'activeCompany'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'currency_code' => 'required|string|size:3',
            'currency_symbol' => 'required|string|max:10',
            'decimal_places' => 'required|integer|min:0|max:4',
            'currency_position' => 'required|in:before,after',
            'footer_message' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        unset($validated['logo']);

        $company = Company::create($validated);
        CurrentCompany::set($company);

        return redirect()->route('companies.index')
            ->with('success', "Company \"{$company->company_name}\" created and activated.");
    }

    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'currency_code' => 'required|string|size:3',
            'currency_symbol' => 'required|string|max:10',
            'decimal_places' => 'required|integer|min:0|max:4',
            'currency_position' => 'required|in:before,after',
            'footer_message' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg|max:2048',
            'remove_logo' => 'nullable|boolean',
        ]);

        if ($request->boolean('remove_logo') && $company->logo_path) {
            Storage::disk('public')->delete($company->logo_path);
            $validated['logo_path'] = null;
        }

        if ($request->hasFile('logo')) {
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        unset($validated['logo'], $validated['remove_logo']);

        $company->update($validated);

        return redirect()->route('companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function switch(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        CurrentCompany::set($validated['company_id']);
        $company = Company::find($validated['company_id']);

        return redirect()->back()
            ->with('success', "Switched to {$company->company_name}.");
    }

    public function destroy(Company $company)
    {
        if (Company::count() <= 1) {
            return back()->with('error', 'Cannot delete the only company.');
        }

        if ($company->logo_path) {
            Storage::disk('public')->delete($company->logo_path);
        }

        $company->delete();

        if (CurrentCompany::id() === $company->id) {
            CurrentCompany::set(Company::first());
        }

        return redirect()->route('companies.index')
            ->with('success', 'Company deleted successfully.');
    }
}
