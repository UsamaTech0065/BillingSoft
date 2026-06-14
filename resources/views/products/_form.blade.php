<div class="space-y-5">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Product Name</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
            placeholder="e.g. Web Design, Laptop, Consulting">
        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Type</label>
        <div class="grid grid-cols-2 gap-3">
            <label class="relative cursor-pointer">
                <input type="radio" name="type" value="good" class="peer sr-only" {{ old('type', $product->type ?? 'good') === 'good' ? 'checked' : '' }}>
                <div class="p-4 border-2 border-slate-200 rounded-xl text-center peer-checked:border-amber-500 peer-checked:bg-amber-50 transition">
                    <svg class="w-8 h-8 mx-auto text-amber-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span class="font-medium text-slate-900">Good</span>
                    <p class="text-xs text-slate-500 mt-0.5">Physical product</p>
                </div>
            </label>
            <label class="relative cursor-pointer">
                <input type="radio" name="type" value="service" class="peer sr-only" {{ old('type', $product->type ?? '') === 'service' ? 'checked' : '' }}>
                <div class="p-4 border-2 border-slate-200 rounded-xl text-center peer-checked:border-violet-500 peer-checked:bg-violet-50 transition">
                    <svg class="w-8 h-8 mx-auto text-violet-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span class="font-medium text-slate-900">Service</span>
                    <p class="text-xs text-slate-500 mt-0.5">Work or consulting</p>
                </div>
            </label>
        </div>
        @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Price</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">$</span>
                <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}" step="0.01" min="0" required
                    class="w-full pl-7 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
            </div>
            @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Unit</label>
            <input type="text" name="unit" value="{{ old('unit', $product->unit ?? 'pcs') }}" required
                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                placeholder="pcs, hr, session">
            @error('unit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Description <span class="text-slate-400 font-normal">(optional)</span></label>
        <textarea name="description" rows="3"
            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none resize-none"
            placeholder="Brief description of the product or service">{{ old('description', $product->description ?? '') }}</textarea>
    </div>
</div>
