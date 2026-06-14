@extends('layouts.app')

@section('title', 'Payments')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Payments</h1>
        <p class="text-slate-500 mt-1">All invoice payments and customer advances</p>
    </div>
    <a href="{{ route('payments.create') }}" class="btn-pay-smart inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-xl shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Payment
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <p class="text-xs font-bold text-slate-400 uppercase">Invoice Payments</p>
        <p class="text-2xl font-bold text-slate-900 mt-1">{{ money($totalReceived) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <p class="text-xs font-bold text-slate-400 uppercase">Advance Payments</p>
        <p class="text-2xl font-bold text-amber-600 mt-1">{{ money($totalAdvance) }}</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3">
        <select name="type" class="px-4 py-2 border border-slate-200 rounded-xl text-sm bg-white">
            <option value="">All Types</option>
            <option value="invoice" {{ request('type') === 'invoice' ? 'selected' : '' }}>Invoice Payments</option>
            <option value="advance" {{ request('type') === 'advance' ? 'selected' : '' }}>Advance Payments</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-xl">Filter</button>
    </form>
</div>

@if($payments->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
        <h3 class="text-lg font-semibold text-slate-900">No payments yet</h3>
        <p class="text-slate-500 mt-1">Record a payment from an invoice or add one here.</p>
        <a href="{{ route('payments.create') }}" class="btn-pay-smart inline-flex mt-4 px-4 py-2 text-sm font-medium rounded-lg">Add Payment</a>
    </div>
@else
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Method</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Invoice</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($payments as $payment)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 text-sm">{{ $payment->payment_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('customers.show', $payment->customer) }}" class="font-medium text-indigo-600 hover:underline">{{ $payment->customer->name }}</a>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $payment->isAdvance() ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                            {{ $payment->isAdvance() ? 'Advance' : 'Invoice' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $payment->getMethodLabel() }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($payment->invoice)
                            <a href="{{ route('invoices.show', $payment->invoice) }}" class="text-indigo-600 hover:underline">{{ $payment->invoice->invoice_number }}</a>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right font-semibold text-slate-900">{{ money($payment->amount) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $payments->withQueryString()->links() }}</div>
@endif
@endsection
