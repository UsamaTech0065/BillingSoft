@extends('layouts.app')

@section('title', 'Companies')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Companies</h1>
        <p class="text-slate-500 mt-1">Switch between companies — each has its own products and invoices</p>
    </div>
    <a href="{{ route('companies.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 shadow-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Company
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    @foreach($companies as $company)
    <div class="bg-white rounded-2xl border-2 shadow-sm overflow-hidden transition
        {{ $activeCompany->id === $company->id ? 'border-indigo-500 ring-2 ring-indigo-100' : 'border-slate-200 hover:border-slate-300' }}">
        <div class="p-6">
            <div class="flex items-start gap-4">
                @if($company->getLogoUrl())
                    <img src="{{ $company->getLogoUrl() }}" alt="{{ $company->company_name }}" class="w-14 h-14 object-contain rounded-xl border border-slate-200 p-1 shrink-0">
                @else
                    <div class="w-14 h-14 rounded-xl bg-indigo-600 flex items-center justify-center text-white text-xl font-bold shrink-0">
                        {{ strtoupper(substr($company->company_name, 0, 1)) }}
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-semibold text-slate-900 truncate">{{ $company->company_name }}</h3>
                        @if($activeCompany->id === $company->id)
                            <span class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide bg-indigo-100 text-indigo-700 rounded-full shrink-0">Active</span>
                        @endif
                    </div>
                    @if($company->tagline)
                        <p class="text-xs text-indigo-600 font-medium mt-0.5">{{ $company->tagline }}</p>
                    @endif
                    <p class="text-xs text-slate-500 mt-2">{{ $company->products_count }} products · {{ $company->invoices_count }} invoices · {{ $company->getCurrencyLabel() }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 mt-5 pt-5 border-t border-slate-100">
                @if($activeCompany->id !== $company->id)
                    <form action="{{ route('companies.switch') }}" method="POST">
                        @csrf
                        <input type="hidden" name="company_id" value="{{ $company->id }}">
                        <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                            Switch to this company
                        </button>
                    </form>
                @endif
                <a href="{{ route('companies.edit', $company) }}" class="px-3 py-1.5 border border-slate-200 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-50 transition">
                    Edit
                </a>
                @if($companies->count() > 1)
                    <form action="{{ route('companies.destroy', $company) }}" method="POST" onsubmit="return confirm('Delete {{ $company->company_name }}? All its products and invoices will be removed.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 border border-red-200 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition">Delete</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
