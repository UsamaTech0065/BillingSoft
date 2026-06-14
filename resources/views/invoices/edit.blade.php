@extends('layouts.app')

@section('title', 'Edit ' . $invoice->invoice_number)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('invoices.show', $invoice) }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Invoice
        </a>
        <h1 class="text-3xl font-bold text-slate-900">Edit Invoice</h1>
        <p class="text-slate-500 mt-1">{{ $invoice->invoice_number }}</p>
    </div>

    <form action="{{ route('invoices.update', $invoice) }}" method="POST" id="invoice-form">
        @csrf @method('PUT')

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-5">Customer Details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @include('invoices._customer-fields', [
                    'customers' => $customers,
                    'selectedCustomerId' => $invoice->customer_id,
                    'customerName' => $invoice->customer_name,
                    'customerEmail' => $invoice->customer_email,
                    'customerAddress' => $invoice->customer_address,
                ])
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Issue Date *</label>
                    <input type="date" name="issue_date" value="{{ old('issue_date', $invoice->issue_date->format('Y-m-d')) }}" required
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Due Date</label>
                    <input type="date" name="due_date" value="{{ old('due_date', $invoice->due_date?->format('Y-m-d')) }}"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Status *</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                        @foreach(['draft', 'sent', 'paid', 'cancelled'] as $status)
                            <option value="{{ $status }}" {{ old('status', $invoice->status) === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 mb-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-slate-900">Line Items</h2>
                <button type="button" id="add-item-btn" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Item
                </button>
            </div>
            <div id="line-items" class="space-y-4"></div>
            @error('items') <p class="text-red-500 text-sm mt-3">{{ $message }}</p> @enderror
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tax Rate (%)</label>
                    <input type="number" name="tax_rate" id="tax-rate" value="{{ old('tax_rate', $invoice->tax_rate) }}" step="0.01" min="0" max="100"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
                    <textarea name="notes" rows="2"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none resize-none">{{ old('notes', $invoice->notes) }}</textarea>
                </div>
            </div>
            <div class="mt-6 pt-6 border-t border-slate-100">
                <div class="flex justify-end">
                    <div class="w-64 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Subtotal</span>
                            <span id="subtotal-display" class="font-medium text-slate-900">$0.00</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Tax</span>
                            <span id="tax-display" class="font-medium text-slate-900">$0.00</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-2 border-t border-slate-200">
                            <span class="text-slate-900">Total</span>
                            <span id="total-display" class="text-indigo-600">$0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="flex-1 px-4 py-3 bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-violet-700 shadow-sm transition">
                Update Invoice
            </button>
            <a href="{{ route('invoices.show', $invoice) }}" class="px-4 py-3 border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 transition">Cancel</a>
        </div>
    </form>
</div>

@include('invoices._service-modal')
@endsection

@push('scripts')
@include('invoices._invoice-scripts', [
    'services' => $services,
    'goods' => $goods,
    'existingItems' => old('items', $invoice->items->map(fn($item) => [
        'product_id' => $item->product_id,
        'quantity' => $item->quantity,
        'unit_price' => $item->unit_price,
    ])->values()),
])
@endpush
