<div class="sm:col-span-2">
    <label class="block text-sm font-medium text-slate-700 mb-1.5">Select Customer</label>
    <select id="customer-select" name="customer_id"
        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
        <option value="">— Select existing customer or enter manually below —</option>
        @foreach($customers as $cust)
            <option value="{{ $cust->id }}"
                data-name="{{ $cust->name }}"
                data-email="{{ $cust->email }}"
                data-address="{{ $cust->address }}"
                {{ old('customer_id', $selectedCustomerId ?? null) == $cust->id ? 'selected' : '' }}>
                {{ $cust->name }}@if($cust->email) — {{ $cust->email }}@endif
            </option>
        @endforeach
        <option value="__new__">+ Add new customer...</option>
    </select>
</div>
<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">Customer Name *</label>
    <input type="text" name="customer_name" id="customer-name" value="{{ old('customer_name', $customerName ?? '') }}" required
        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
    @error('customer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>
<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
    <input type="email" name="customer_email" id="customer-email" value="{{ old('customer_email', $customerEmail ?? '') }}"
        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
</div>
<div class="sm:col-span-2">
    <label class="block text-sm font-medium text-slate-700 mb-1.5">Address</label>
    <textarea name="customer_address" id="customer-address" rows="2"
        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none resize-none">{{ old('customer_address', $customerAddress ?? '') }}</textarea>
</div>

@push('scripts')
<script>
(function() {
    const select = document.getElementById('customer-select');
    if (!select) return;

    select.addEventListener('change', function() {
        if (this.value === '__new__') {
            window.location.href = '{{ route("customers.create") }}';
            return;
        }
        const opt = this.options[this.selectedIndex];
        if (opt && opt.dataset.name) {
            document.getElementById('customer-name').value = opt.dataset.name || '';
            document.getElementById('customer-email').value = opt.dataset.email || '';
            document.getElementById('customer-address').value = opt.dataset.address || '';
        }
    });
})();
</script>
@endpush
