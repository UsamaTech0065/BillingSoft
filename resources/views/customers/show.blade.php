@extends('layouts.app')

@section('title', $customer->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8">
        <div>
            <a href="{{ route('customers.index') }}" class="text-sm text-slate-500 hover:text-slate-700">← Customers</a>
            <h1 class="text-3xl font-bold text-slate-900 mt-2">{{ $customer->name }}</h1>
            @if($customer->email)<p class="text-slate-500">{{ $customer->email }}</p>@endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('customers.edit', $customer) }}" class="px-4 py-2 border border-slate-200 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50">Edit</a>
            <button type="button" onclick="document.getElementById('advance-modal').classList.remove('hidden')"
                class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-xl hover:bg-emerald-700">
                + Add Advance Payment
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <p class="text-xs font-bold text-slate-400 uppercase">Advance Balance</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ money($customer->advance_balance) }}</p>
            <p class="text-xs text-slate-500 mt-1">Available to apply on invoices</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <p class="text-xs font-bold text-slate-400 uppercase">Total Invoices</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $customer->invoices->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <p class="text-xs font-bold text-slate-400 uppercase">Phone</p>
            <p class="text-lg font-semibold text-slate-900 mt-1">{{ $customer->phone ?? '—' }}</p>
        </div>
    </div>

    @if($customer->address || $customer->notes)
    <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-8">
        @if($customer->address)<p class="text-sm text-slate-600"><strong>Address:</strong> {{ $customer->address }}</p>@endif
        @if($customer->notes)<p class="text-sm text-slate-600 mt-2"><strong>Notes:</strong> {{ $customer->notes }}</p>@endif
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="font-semibold text-slate-900">Payment History</h2>
        </div>
        @if($customer->payments->isEmpty())
            <p class="p-6 text-slate-500 text-sm">No payments recorded yet.</p>
        @else
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Invoice</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($customer->payments as $payment)
                    <tr>
                        <td class="px-6 py-3 text-sm">{{ $payment->payment_date->format('M d, Y') }}</td>
                        <td class="px-6 py-3">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $payment->isAdvance() ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $payment->isAdvance() ? 'Advance' : 'Invoice Payment' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-sm text-slate-600">{{ $payment->getMethodLabel() }}</td>
                        <td class="px-6 py-3 text-sm">
                            @if($payment->invoice)
                                <a href="{{ route('invoices.show', $payment->invoice) }}" class="text-indigo-600 hover:underline">{{ $payment->invoice->invoice_number }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right font-semibold text-slate-900">{{ money($payment->amount) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if($customer->invoices->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="font-semibold text-slate-900">Recent Invoices</h2>
        </div>
        <table class="w-full">
            <tbody class="divide-y divide-slate-100">
                @foreach($customer->invoices as $inv)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3"><a href="{{ route('invoices.show', $inv) }}" class="font-medium text-indigo-600">{{ $inv->invoice_number }}</a></td>
                    <td class="px-6 py-3 text-sm text-slate-500">{{ $inv->issue_date->format('M d, Y') }}</td>
                    <td class="px-6 py-3">
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full
                            @if($inv->payment_status === 'paid') bg-emerald-100 text-emerald-700
                            @elseif($inv->payment_status === 'partial') bg-amber-100 text-amber-700
                            @else bg-slate-100 text-slate-600 @endif">
                            {{ ucfirst($inv->payment_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-right font-semibold">{{ money($inv->total) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Advance Payment Modal --}}
<div id="advance-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/50" onclick="document.getElementById('advance-modal').classList.add('hidden')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 relative">
            <h3 class="text-lg font-semibold text-slate-900 mb-1">Add Advance Payment</h3>
            <p class="text-sm text-slate-500 mb-5">Record advance credit for {{ $customer->name }}</p>
            <form action="{{ route('customers.advance.store', $customer) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Amount *</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Payment Method *</label>
                    <select name="method" required class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="cash">Cash</option>
                        <option value="online">Online</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Payment Date *</label>
                    <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" required class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Reference</label>
                    <input type="text" name="reference" placeholder="Transaction ID, receipt #..." class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-xl hover:bg-emerald-700">Record Advance</button>
                    <button type="button" onclick="document.getElementById('advance-modal').classList.add('hidden')" class="px-4 py-2.5 border border-slate-200 text-slate-600 text-sm rounded-xl">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
