<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BillingSoft') — Billing Software</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">
    @php $activeCompany = \App\Support\CurrentCompany::get(); $allCompanies = \App\Models\Company::orderBy('company_name')->get(); @endphp

    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="font-bold text-xl text-slate-900 hidden sm:inline">BillingSoft</span>
                    </a>

                    {{-- Company Switcher --}}
                    <div class="relative" x-data="{ open: false }" id="company-switcher">
                        <button type="button" onclick="document.getElementById('company-dropdown').classList.toggle('hidden')"
                            class="flex items-center gap-2 px-3 py-2 bg-indigo-50 border border-indigo-200 rounded-xl hover:bg-indigo-100 transition text-sm">
                            @if($activeCompany->getLogoUrl())
                                <img src="{{ $activeCompany->getLogoUrl() }}" class="w-6 h-6 object-contain rounded" alt="">
                            @else
                                <span class="w-6 h-6 rounded bg-indigo-600 text-white text-xs font-bold flex items-center justify-center">{{ strtoupper(substr($activeCompany->company_name, 0, 1)) }}</span>
                            @endif
                            <span class="font-medium text-indigo-900 max-w-[120px] truncate hidden sm:inline">{{ $activeCompany->company_name }}</span>
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="company-dropdown" class="hidden absolute left-0 mt-2 w-64 bg-white border border-slate-200 rounded-xl shadow-lg z-50 py-2">
                            <p class="px-4 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Switch Company</p>
                            @foreach($allCompanies as $co)
                                <form action="{{ route('companies.switch') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="company_id" value="{{ $co->id }}">
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-slate-50 transition text-left
                                        {{ $activeCompany->id === $co->id ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700' }}">
                                        @if($co->getLogoUrl())
                                            <img src="{{ $co->getLogoUrl() }}" class="w-8 h-8 object-contain rounded border border-slate-200" alt="">
                                        @else
                                            <span class="w-8 h-8 rounded-lg bg-slate-200 text-slate-600 text-sm font-bold flex items-center justify-center">{{ strtoupper(substr($co->company_name, 0, 1)) }}</span>
                                        @endif
                                        <span class="font-medium truncate">{{ $co->company_name }}</span>
                                        @if($activeCompany->id === $co->id)
                                            <svg class="w-4 h-4 text-indigo-600 ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </button>
                                </form>
                            @endforeach
                            <div class="border-t border-slate-100 mt-1 pt-1">
                                <a href="{{ route('companies.index') }}" class="block px-4 py-2 text-sm text-indigo-600 hover:bg-indigo-50 font-medium">Manage Companies</a>
                                <a href="{{ route('companies.create') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">+ Add New Company</a>
                            </div>
                        </div>
                    </div>

                    <div class="hidden md:flex items-center gap-1">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Dashboard</a>
                        <a href="{{ route('customers.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('customers.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Customers</a>
                        <a href="{{ route('products.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('products.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Products</a>
                        <a href="{{ route('invoices.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('invoices.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Invoices</a>
                        <a href="{{ route('payments.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('payments.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Payments</a>
                        <a href="{{ route('companies.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('companies.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Companies</a>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('products.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Product
                    </a>
                    <a href="{{ route('invoices.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-violet-600 rounded-lg hover:from-indigo-700 hover:to-violet-700 shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Invoice
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <script>
    document.addEventListener('click', function(e) {
        const switcher = document.getElementById('company-switcher');
        const dropdown = document.getElementById('company-dropdown');
        if (switcher && dropdown && !switcher.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
    </script>
    @stack('scripts')
</body>
</html>
