<div class="space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <h2 class="text-lg font-semibold text-slate-900 mb-5">Company Logo</h2>
        <div class="flex flex-col sm:flex-row items-start gap-6">
            <div id="logo-preview" class="w-32 h-32 rounded-2xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden bg-slate-50 shrink-0">
                @if(isset($company) && $company->getLogoUrl())
                    <img src="{{ $company->getLogoUrl() }}" alt="Logo" class="max-w-full max-h-full object-contain">
                @else
                    <div class="text-center p-4">
                        <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-xs text-slate-400 mt-2">No logo</p>
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <input type="file" name="logo" id="logo-input" accept="image/*"
                    class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                <p class="text-xs text-slate-400 mt-2">PNG, JPG, GIF or SVG. Max 2MB.</p>
                @if(isset($company) && $company->logo_path)
                    <label class="inline-flex items-center gap-2 mt-3 text-sm text-red-600 cursor-pointer">
                        <input type="checkbox" name="remove_logo" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                        Remove current logo
                    </label>
                @endif
                @error('logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <h2 class="text-lg font-semibold text-slate-900 mb-5">Currency &amp; Formatting</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-2">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Currency *</label>
                <select name="currency_code" id="currency_code" required
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-indigo-500 outline-none">
                    @foreach(config('currencies') as $code => $info)
                        <option value="{{ $code }}" data-symbol="{{ $info['symbol'] }}"
                            {{ old('currency_code', $company->currency_code ?? 'USD') === $code ? 'selected' : '' }}>
                            {{ $code }} — {{ $info['name'] }}
                        </option>
                    @endforeach
                </select>
                @error('currency_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Currency Symbol *</label>
                <input type="text" name="currency_symbol" id="currency_symbol" maxlength="10"
                    value="{{ old('currency_symbol', $company->currency_symbol ?? '$') }}" required
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                @error('currency_symbol') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Decimal Places *</label>
                <select name="decimal_places" id="decimal_places" required
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-indigo-500 outline-none">
                    @foreach([0, 1, 2, 3, 4] as $dp)
                        <option value="{{ $dp }}" {{ (int) old('decimal_places', $company->decimal_places ?? 2) === $dp ? 'selected' : '' }}>
                            {{ $dp }} {{ $dp === 1 ? 'decimal' : 'decimals' }} (e.g. {{ number_format(1234.5, $dp) }})
                        </option>
                    @endforeach
                </select>
                @error('decimal_places') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Symbol Position *</label>
                <select name="currency_position" id="currency_position" required
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="before" {{ old('currency_position', $company->currency_position ?? 'before') === 'before' ? 'selected' : '' }}>Before amount ($1,234.56)</option>
                    <option value="after" {{ old('currency_position', $company->currency_position ?? 'before') === 'after' ? 'selected' : '' }}>After amount (1,234.56 $)</option>
                </select>
                @error('currency_position') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
                <div class="bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3 text-sm text-indigo-800">
                    Preview: <strong id="currency-preview">{{ isset($company) ? $company->formatMoney(1234.56) : '$1,234.56' }}</strong>
                    <span class="text-indigo-500 text-xs ml-2">Used on invoices, payments, products &amp; PDF</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <h2 class="text-lg font-semibold text-slate-900 mb-5">Company Information</h2>
        <div class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Company Name *</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $company->company_name ?? '') }}" required
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    @error('company_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tagline</label>
                    <input type="text" name="tagline" value="{{ old('tagline', $company->tagline ?? '') }}"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $company->email ?? '') }}"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $company->phone ?? '') }}"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Website</label>
                    <input type="text" name="website" value="{{ old('website', $company->website ?? '') }}"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tax ID / VAT</label>
                    <input type="text" name="tax_id" value="{{ old('tax_id', $company->tax_id ?? '') }}"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Street Address</label>
                    <textarea name="address" rows="2"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none resize-none">{{ old('address', $company->address ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">City</label>
                    <input type="text" name="city" value="{{ old('city', $company->city ?? '') }}"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">State</label>
                    <input type="text" name="state" value="{{ old('state', $company->state ?? '') }}"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">ZIP</label>
                    <input type="text" name="zip" value="{{ old('zip', $company->zip ?? '') }}"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Country</label>
                    <input type="text" name="country" value="{{ old('country', $company->country ?? '') }}"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Invoice Footer Message</label>
                    <textarea name="footer_message" rows="2"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none resize-none">{{ old('footer_message', $company->footer_message ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateCurrencyPreview() {
    const symbol = document.getElementById('currency_symbol')?.value || '$';
    const decimals = parseInt(document.getElementById('decimal_places')?.value || '2', 10);
    const position = document.getElementById('currency_position')?.value || 'before';
    const amount = (1234.56).toFixed(decimals);
    const formatted = Number(amount).toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
    const preview = position === 'after' ? formatted + ' ' + symbol : symbol + formatted;
    const el = document.getElementById('currency-preview');
    if (el) el.textContent = preview;
}
document.getElementById('currency_code')?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (opt?.dataset.symbol) {
        document.getElementById('currency_symbol').value = opt.dataset.symbol;
    }
    updateCurrencyPreview();
});
['currency_symbol', 'decimal_places', 'currency_position'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', updateCurrencyPreview);
    document.getElementById(id)?.addEventListener('change', updateCurrencyPreview);
});
updateCurrencyPreview();

document.getElementById('logo-input')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(ev) {
        document.getElementById('logo-preview').innerHTML = `<img src="${ev.target.result}" alt="Preview" class="max-w-full max-h-full object-contain">`;
    };
    reader.readAsDataURL(file);
});
</script>
@endpush
