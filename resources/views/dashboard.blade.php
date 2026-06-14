@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php $activeCompany = \App\Support\CurrentCompany::get(); @endphp
<div class="mb-8">
    <h1 class="text-3xl font-bold text-slate-900">Dashboard</h1>
    <p class="text-slate-500 mt-1">Overview for <span class="font-medium text-indigo-600">{{ $activeCompany->company_name }}</span></p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Total Products</p>
                <p class="text-3xl font-bold text-slate-900 mt-1">{{ $stats['products'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>
        <p class="text-xs text-slate-400 mt-3">{{ $stats['services'] }} services · {{ $stats['goods'] }} goods</p>
    </div>

    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Invoices</p>
                <p class="text-3xl font-bold text-slate-900 mt-1">{{ $stats['invoices'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Revenue (Paid)</p>
                <p class="text-3xl font-bold text-slate-900 mt-1">{{ money($stats['revenue']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-indigo-600 to-violet-600 rounded-2xl p-6 shadow-lg">
        <p class="text-sm font-medium text-indigo-100">Quick Action</p>
        <p class="text-white font-semibold mt-1 mb-4">Create a new invoice</p>
        <a href="{{ route('invoices.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-medium rounded-lg transition backdrop-blur">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Invoice
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-900">Recent Invoices</h2>
        <a href="{{ route('invoices.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View all</a>
    </div>
    @if($recentInvoices->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-slate-500">No invoices yet. Create your first invoice!</p>
            <a href="{{ route('invoices.create') }}" class="inline-flex mt-4 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Create Invoice</a>
        </div>
    @else
        <table class="w-full">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($recentInvoices as $invoice)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        <span class="font-medium text-slate-900">{{ $invoice->invoice_number }}</span>
                        <span class="ml-2 inline-flex px-2 py-0.5 text-xs font-medium rounded-full
                            @if($invoice->status === 'paid') bg-emerald-100 text-emerald-700
                            @elseif($invoice->status === 'sent') bg-blue-100 text-blue-700
                            @else bg-slate-100 text-slate-600 @endif">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-600">{{ $invoice->customer_name }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $invoice->issue_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-right font-semibold text-slate-900">{{ money($invoice->total) }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('invoices.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
