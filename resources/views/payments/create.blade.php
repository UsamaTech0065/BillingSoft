@extends('layouts.app')

@section('title', 'Add Payment')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('payments.index') }}" class="text-sm text-slate-500 hover:text-slate-700">← Back to Payments</a>
        <h1 class="text-3xl font-bold text-slate-900 mt-2">Add Payment</h1>
        <p class="text-slate-500 mt-1">Record invoice payment or customer advance</p>
    </div>

    <form action="{{ route('payments.store') }}" method="POST" id="payment-form" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Payment Type *</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="cursor-pointer">
                    <input type="radio" name="payment_type" value="invoice" class="peer sr-only" checked onchange="togglePaymentType()">
                    <div class="p-4 border-2 border-slate-200 rounded-xl text-center peer-checked:border-green-500 peer-checked:bg-green-50">
                        <span class="font-medium text-slate-900">Invoice Payment</span>
                        <p class="text-xs text-slate-500 mt-1">Pay against an invoice</p>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="payment_type" value="advance" class="peer sr-only" onchange="togglePaymentType()">
                    <div class="p-4 border-2 border-slate-200 rounded-xl text-center peer-checked:border-amber-500 peer-checked:bg-amber-50">
                        <span class="font-medium text-slate-900">Advance Payment</span>
                        <p class="text-xs text-slate-500 mt-1">Customer credit for later</p>
                    </div>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Customer *</label>
            <select name="customer_id" id="customer_id" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">— Select customer —</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}@if($customer->advance_balance > 0) (Advance: {{ money($customer->advance_balance) }})@endif
                    </option>
                @endforeach
            </select>
            @error('customer_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div id="invoice-select-wrap">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Invoice *</label>
            <select name="invoice_id" id="invoice_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">— Select invoice —</option>
                @foreach($invoices as $inv)
                    <option value="{{ $inv->id }}" data-balance="{{ $inv->getBalanceDue() }}" {{ old('invoice_id') == $inv->id ? 'selected' : '' }}>
                        {{ $inv->invoice_number }} — {{ $inv->customer_name }} — Due: {{ money($inv->getBalanceDue()) }}
                    </option>
                @endforeach
            </select>
            @error('invoice_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Amount *</label>
            <input type="number" name="amount" id="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required
                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Payment Method *</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="cursor-pointer">
                    <input type="radio" name="method" value="cash" class="peer sr-only" checked>
                    <div class="p-3 border-2 border-slate-200 rounded-xl text-center peer-checked:border-green-500 peer-checked:bg-green-50">
                        <span class="text-sm font-medium">Cash</span>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="method" value="online" class="peer sr-only">
                    <div class="p-3 border-2 border-slate-200 rounded-xl text-center peer-checked:border-green-500 peer-checked:bg-green-50">
                        <span class="text-sm font-medium">Online</span>
                    </div>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Payment Date *</label>
            <input type="date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required
                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Reference</label>
            <input type="text" name="reference" value="{{ old('reference') }}" placeholder="Receipt #, transaction ID..."
                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
            <textarea name="notes" rows="2" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm resize-none focus:ring-2 focus:ring-indigo-500 outline-none">{{ old('notes') }}</textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-pay-smart flex-1 px-4 py-3 text-sm font-semibold rounded-xl">Record Payment</button>
            <button type="button" onclick="clearPaymentForm()" class="px-4 py-3 border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50">Clear</button>
            <a href="{{ route('payments.index') }}" class="px-4 py-3 border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 flex items-center">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function togglePaymentType() {
    const isAdvance = document.querySelector('[name=payment_type]:checked').value === 'advance';
    const wrap = document.getElementById('invoice-select-wrap');
    const invoiceSelect = document.getElementById('invoice_id');
    wrap.style.display = isAdvance ? 'none' : 'block';
    invoiceSelect.required = !isAdvance;
    if (isAdvance) invoiceSelect.value = '';
}
function clearPaymentForm() {
    document.getElementById('payment-form').reset();
    document.querySelector('[name=payment_type][value=invoice]').checked = true;
    togglePaymentType();
}
document.getElementById('invoice_id')?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (opt && opt.dataset.balance) {
        document.getElementById('amount').value = parseFloat(opt.dataset.balance).toFixed(2);
    }
});
togglePaymentType();
</script>
@endpush
