<div class="space-y-5">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Name *</label>
        <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}" required
            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email', $customer->email ?? '') }}"
                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Address</label>
        <textarea name="address" rows="2"
            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none resize-none">{{ old('address', $customer->address ?? '') }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
        <textarea name="notes" rows="2"
            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none resize-none">{{ old('notes', $customer->notes ?? '') }}</textarea>
    </div>
</div>
