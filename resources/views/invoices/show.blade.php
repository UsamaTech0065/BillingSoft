@extends('layouts.app')

@section('title', $invoice->invoice_number)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Invoices
            </a>
            <h1 class="text-3xl font-bold text-slate-900">{{ $invoice->invoice_number }}</h1>
            <p class="text-slate-500 mt-1">Created {{ $invoice->created_at->format('M d, Y') }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            @if($invoice->payment_status !== 'paid')
                <button type="button" onclick="openPayModal()"
                    class="btn-pay-smart inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-xl shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Pay Smart
                </button>
            @endif
            <a href="{{ route('invoices.edit', $invoice) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 border border-slate-200 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Invoice
            </a>
            <a href="{{ route('invoices.preview', $invoice) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 border border-indigo-200 text-indigo-700 text-sm font-medium rounded-xl hover:bg-indigo-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                PDF Preview
            </a>
            <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-sm font-medium rounded-xl hover:from-indigo-700 hover:to-violet-700 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Download PDF
            </a>
            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('Delete invoice {{ $invoice->invoice_number }}? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 border border-red-200 text-red-600 text-sm font-medium rounded-xl hover:bg-red-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Invoice Header Preview -->
        <div class="border-b-4 border-indigo-600 bg-white px-8 py-6">
            <div class="flex justify-between items-center gap-6">
                <div class="flex items-center gap-4">
                    @if($company->getLogoUrl())
                        <img src="{{ $company->getLogoUrl() }}" alt="{{ $company->company_name }}" class="w-16 h-16 object-contain rounded-xl border border-slate-200 p-1">
                    @else
                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white text-2xl font-bold">
                            {{ strtoupper(substr($company->company_name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">{{ $company->company_name }}</h2>
                        @if($company->tagline)
                            <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">{{ $company->tagline }}</p>
                        @endif
                        <p class="text-xs text-slate-500 mt-1">
                            @if($company->email){{ $company->email }}@endif
                            @if($company->phone) &bull; {{ $company->phone }}@endif
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-indigo-600 tracking-widest">INVOICE</p>
                    <span class="inline-flex mt-1 px-3 py-1 bg-indigo-50 text-indigo-700 text-sm font-semibold rounded-full">{{ $invoice->invoice_number }}</span>
                    <p class="text-xs text-slate-500 mt-2">Issued: {{ $invoice->issue_date->format('M d, Y') }}</p>
                    @if($invoice->due_date)
                        <p class="text-xs text-slate-500">Due: {{ $invoice->due_date->format('M d, Y') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Meta strip -->
        <div class="grid grid-cols-2 sm:grid-cols-4 border-b border-slate-100 bg-slate-50">
            <div class="px-6 py-3 border-r border-slate-200">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Issue Date</p>
                <p class="text-sm font-semibold text-slate-900">{{ $invoice->issue_date->format('M d, Y') }}</p>
            </div>
            <div class="px-6 py-3 border-r border-slate-200">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Due Date</p>
                <p class="text-sm font-semibold text-slate-900">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'Upon Receipt' }}</p>
            </div>
            <div class="px-6 py-3 border-r border-slate-200">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Payment</p>
                <span class="text-sm font-semibold
                    @if($invoice->payment_status === 'paid') text-emerald-700
                    @elseif($invoice->payment_status === 'partial') text-amber-700
                    @else text-slate-600 @endif">
                    {{ ucfirst($invoice->payment_status) }}
                </span>
            </div>
            <div class="px-6 py-3">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Currency</p>
                <p class="text-sm font-semibold text-slate-900">{{ $company->getCurrencyLabel() }}</p>
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Bill To</p>
                    <p class="text-lg font-semibold text-slate-900">{{ $invoice->customer_name }}</p>
                    @if($invoice->customer)
                        <a href="{{ route('customers.show', $invoice->customer) }}" class="text-xs text-indigo-600 hover:underline">View customer profile</a>
                    @endif
                    @if($invoice->customer_email)
                        <p class="text-slate-500 text-sm mt-1">{{ $invoice->customer_email }}</p>
                    @endif
                    @if($invoice->customer_address)
                        <p class="text-slate-500 text-sm mt-1 whitespace-pre-line">{{ $invoice->customer_address }}</p>
                    @endif
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Payment Details</p>
                    <p class="text-sm text-slate-600"><span class="font-medium text-slate-800">Payable to:</span> {{ $company->company_name }}</p>
                    @if($company->email)
                        <p class="text-sm text-slate-600 mt-1"><span class="font-medium text-slate-800">Email:</span> {{ $company->email }}</p>
                    @endif
                    @if($company->getAddressLine())
                        <p class="text-sm text-slate-600 mt-1"><span class="font-medium text-slate-800">Address:</span> {{ $company->getAddressLine() }}</p>
                    @endif
                </div>
            </div>

            <table class="w-full mb-8 border border-slate-200 rounded-xl overflow-hidden">
                <thead>
                    <tr class="bg-indigo-600">
                        <th class="py-3 px-4 text-left text-[10px] font-bold text-white uppercase tracking-wider">Description</th>
                        <th class="py-3 px-4 text-left text-[10px] font-bold text-white uppercase tracking-wider">Type</th>
                        <th class="py-3 px-4 text-right text-[10px] font-bold text-white uppercase tracking-wider">Qty</th>
                        <th class="py-3 px-4 text-right text-[10px] font-bold text-white uppercase tracking-wider">Unit Price</th>
                        <th class="py-3 px-4 text-right text-[10px] font-bold text-white uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($invoice->items as $item)
                    <tr class="even:bg-slate-50">
                        <td class="py-4 px-4">
                            <p class="font-medium text-slate-900">{{ $item->product->name }}</p>
                            @if($item->description)
                                <p class="text-sm text-slate-500">{{ $item->description }}</p>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded {{ $item->product->type === 'service' ? 'bg-violet-100 text-violet-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ ucfirst($item->product->type) }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-right text-slate-600">{{ $item->quantity }}</td>
                        <td class="py-4 px-4 text-right text-slate-600">{{ money($item->unit_price, $company) }}</td>
                        <td class="py-4 px-4 text-right font-semibold text-slate-900">{{ money($item->total, $company) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex justify-end">
                <div class="w-72 border border-slate-200 rounded-xl overflow-hidden">
                    <div class="bg-indigo-600 px-4 py-2 text-[10px] font-bold text-white uppercase tracking-wider">Invoice Summary</div>
                    <div class="bg-slate-50 px-4 py-3 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Subtotal</span>
                            <span class="font-medium">{{ money($invoice->subtotal, $company) }}</span>
                        </div>
                        @if($invoice->tax_rate > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Tax ({{ number_format($invoice->tax_rate, $company) }}%)</span>
                            <span class="font-medium">{{ money($invoice->tax_amount, $company) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-sm pt-2">
                            <span class="text-slate-500">Total</span>
                            <span class="font-medium">{{ money($invoice->total, $company) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Amount Paid</span>
                            <span class="font-medium text-emerald-600">{{ money($invoice->amount_paid, $company) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-3 border-t-2 border-indigo-600">
                            <span class="text-slate-900">Balance Due</span>
                            <span class="text-indigo-600">{{ money($invoice->getBalanceDue(), $company) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment section --}}
            @if($invoice->payment_status !== 'paid' || $invoice->payments->isNotEmpty())
            <div class="mt-8 border border-slate-200 rounded-xl overflow-hidden">
                <div class="px-5 py-4 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                    <h3 class="font-semibold text-slate-900">Payments</h3>
                    <div class="flex flex-wrap gap-2">
                        @if($invoice->payment_status !== 'paid' && $invoice->customer && $invoice->customer->advance_balance > 0)
                            <button type="button" onclick="document.getElementById('advance-modal').classList.remove('hidden')"
                                class="btn-advance px-3 py-1.5 text-xs font-semibold rounded-lg">
                                Apply Advance ({{ money(min($invoice->customer->advance_balance, $invoice->getBalanceDue()), $company) }} avail.)
                            </button>
                        @endif
                        @if($invoice->payment_status !== 'paid')
                            <button type="button" onclick="openPayModal()"
                                class="btn-pay-smart px-3 py-1.5 text-xs font-semibold rounded-lg">
                                + Register Payment
                            </button>
                        @endif
                    </div>
                </div>
                @if($invoice->payments->isEmpty())
                    <p class="p-5 text-sm text-slate-500">No payments recorded yet.</p>
                @else
                    <table class="w-full">
                        <thead class="bg-white">
                            <tr class="text-left text-[10px] font-bold text-slate-400 uppercase">
                                <th class="px-5 py-2">Date</th>
                                <th class="px-5 py-2">Method</th>
                                <th class="px-5 py-2">Reference</th>
                                <th class="px-5 py-2 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($invoice->payments as $payment)
                            <tr>
                                <td class="px-5 py-3 text-sm">{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td class="px-5 py-3 text-sm">{{ $payment->getMethodLabel() }}</td>
                                <td class="px-5 py-3 text-sm text-slate-500">{{ $payment->reference ?? '—' }}</td>
                                <td class="px-5 py-3 text-sm text-right font-semibold">{{ money($payment->amount, $company) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            @endif

            @if($invoice->notes)
            <div class="mt-8 pt-6 border-t border-slate-100">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Notes</p>
                <p class="text-slate-600 text-sm whitespace-pre-line">{{ $invoice->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Pay Smart Modal --}}
<div id="pay-modal" class="modal-overlay hidden">
    <div class="modal-backdrop" onclick="closePayModal()"></div>
    <div class="modal-box">
        <button type="button" onclick="closePayModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color:#dcfce7;">
                <svg class="w-5 h-5" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-slate-900">Pay Smart</h3>
                <p class="text-sm text-slate-500">Balance due: <strong style="color:#16a34a;">{{ money($invoice->getBalanceDue(), $company) }}</strong></p>
            </div>
        </div>
        @if(!$invoice->customer_id)
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800 mb-4">
                Link this invoice to a customer (Edit Invoice) to record payments.
            </div>
            <div class="flex gap-3">
                <a href="{{ route('invoices.edit', $invoice) }}" class="flex-1 text-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700">Edit Invoice</a>
                <button type="button" onclick="closePayModal()" class="px-4 py-2.5 border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50">Cancel</button>
            </div>
        @else
        <form id="pay-form" action="{{ route('invoices.payments.store', $invoice) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Amount Received *</label>
                <input type="number" name="amount" id="pay-amount" step="0.01" min="0.01" max="{{ $invoice->getBalanceDue() }}" value="{{ $invoice->getBalanceDue() }}" required
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Payment Method *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="method" value="cash" class="peer sr-only" checked>
                        <div class="p-3 border-2 border-slate-200 rounded-xl text-center peer-checked:border-green-500 peer-checked:bg-green-50 transition">
                            <span class="text-sm font-medium">Cash</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="method" value="online" class="peer sr-only">
                        <div class="p-3 border-2 border-slate-200 rounded-xl text-center peer-checked:border-green-500 peer-checked:bg-green-50 transition">
                            <span class="text-sm font-medium">Online</span>
                        </div>
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Payment Date *</label>
                <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Reference</label>
                <input type="text" name="reference" placeholder="Receipt #, transaction ID..." class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm resize-none focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="submit" class="btn-pay-smart flex-1 px-4 py-3 text-sm font-semibold rounded-xl">Record Payment</button>
                <button type="button" onclick="clearPayForm()" class="px-4 py-3 border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50">Clear</button>
                <button type="button" onclick="closePayModal()" class="px-4 py-3 border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50">Cancel</button>
            </div>
        </form>
        @endif
    </div>
</div>

{{-- Apply Advance Modal --}}
@if($invoice->customer && $invoice->customer->advance_balance > 0 && $invoice->payment_status !== 'paid')
<div id="advance-modal" class="modal-overlay hidden">
    <div class="modal-backdrop" onclick="document.getElementById('advance-modal').classList.add('hidden')"></div>
    <div class="modal-box">
            <h3 class="text-lg font-semibold text-slate-900 mb-1">Apply Advance Payment</h3>
            <p class="text-sm text-slate-500 mb-5">
                Customer advance: <strong class="text-amber-600">{{ money($invoice->customer->advance_balance, $company) }}</strong>
                · Balance due: <strong>{{ money($invoice->getBalanceDue(), $company) }}</strong>
            </p>
            <form action="{{ route('invoices.apply-advance', $invoice) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Amount to Apply *</label>
                    <input type="number" name="amount" step="0.01" min="0.01"
                        max="{{ min($invoice->customer->advance_balance, $invoice->getBalanceDue()) }}"
                        value="{{ min($invoice->customer->advance_balance, $invoice->getBalanceDue()) }}" required
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 outline-none">
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="this.closest('form').querySelector('[name=amount]').value={{ $invoice->getBalanceDue() }}"
                        class="px-3 py-1 text-xs bg-slate-100 rounded-lg hover:bg-slate-200">Full Balance</button>
                    <button type="button" onclick="this.closest('form').querySelector('[name=amount]').value={{ $invoice->customer->advance_balance }}"
                        class="px-3 py-1 text-xs bg-slate-100 rounded-lg hover:bg-slate-200">All Advance</button>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-advance flex-1 px-4 py-2.5 text-sm font-medium rounded-xl">Apply Advance</button>
                    <button type="button" onclick="this.closest('form').reset(); this.closest('form').querySelector('[name=amount]').value={{ min($invoice->customer->advance_balance, $invoice->getBalanceDue()) }}" class="px-4 py-2.5 border border-slate-200 text-slate-600 text-sm rounded-xl hover:bg-slate-50">Clear</button>
                    <button type="button" onclick="document.getElementById('advance-modal').classList.add('hidden')" class="px-4 py-2.5 border border-slate-200 text-slate-600 text-sm rounded-xl hover:bg-slate-50">Cancel</button>
                </div>
            </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
const payBalance = {{ $invoice->getBalanceDue() }};
function openPayModal() {
    document.getElementById('pay-modal').classList.remove('hidden');
}
function closePayModal() {
    document.getElementById('pay-modal').classList.add('hidden');
}
function clearPayForm() {
    const form = document.getElementById('pay-form');
    if (!form) return;
    form.reset();
    const amount = document.getElementById('pay-amount');
    if (amount) amount.value = payBalance.toFixed(2);
}
</script>
@endpush
