@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Invoices</h1>
        <p class="text-slate-500 mt-1">Manage and print your invoices</p>
    </div>
    <a href="{{ route('invoices.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 shadow-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Invoice
    </a>
</div>

@if($invoices->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
        <div class="w-16 h-16 rounded-full bg-violet-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-900">No invoices yet</h3>
        <p class="text-slate-500 mt-1">Create your first invoice to get started.</p>
        <a href="{{ route('invoices.create') }}" class="inline-flex mt-4 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Create Invoice</a>
    </div>
@else
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Invoice #</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Payment</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($invoices as $invoice)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4 font-medium text-slate-900">{{ $invoice->invoice_number }}</td>
                    <td class="px-6 py-4 text-slate-600">{{ $invoice->customer_name }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $invoice->issue_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $invoice->items_count }} items</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full
                            @if($invoice->payment_status === 'paid') bg-emerald-100 text-emerald-700
                            @elseif($invoice->payment_status === 'partial') bg-amber-100 text-amber-700
                            @else bg-slate-100 text-slate-600 @endif">
                            {{ ucfirst($invoice->payment_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right font-semibold text-slate-900">{{ money($invoice->total) }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('invoices.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View</a>
                            <a href="{{ route('invoices.edit', $invoice) }}" class="text-slate-600 hover:text-slate-900 text-sm font-medium">Edit</a>
                            <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank" class="text-slate-500 hover:text-slate-700 text-sm font-medium">PDF</a>
                            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline" onsubmit="return confirm('Delete invoice {{ $invoice->invoice_number }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $invoices->links() }}</div>
@endif
@endsection
