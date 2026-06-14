@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Products</h1>
        <p class="text-slate-500 mt-1">Manage your goods and services</p>
    </div>
    <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 shadow-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Product
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="flex-1 min-w-[200px] px-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
        <select name="type" class="px-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="">All Types</option>
            <option value="good" {{ request('type') === 'good' ? 'selected' : '' }}>Goods</option>
            <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>Services</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-200 transition">Filter</button>
    </form>
</div>

@if($products->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
        <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-900">No products yet</h3>
        <p class="text-slate-500 mt-1">Add your first good or service to get started.</p>
        <a href="{{ route('products.create') }}" class="inline-flex mt-4 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Add Product</a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($products as $product)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition group">
            <div class="flex items-start justify-between mb-4">
                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full
                    {{ $product->type === 'service' ? 'bg-violet-100 text-violet-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ ucfirst($product->type) }}
                </span>
                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition">
                    <a href="{{ route('products.edit', $product) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-indigo-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg hover:bg-red-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-slate-900">{{ $product->name }}</h3>
            @if($product->description)
                <p class="text-sm text-slate-500 mt-1 line-clamp-2">{{ $product->description }}</p>
            @endif
            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-2xl font-bold text-slate-900">{{ money($product->price) }}</span>
                <span class="text-sm text-slate-400">per {{ $product->unit }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $products->withQueryString()->links() }}
    </div>
@endif
@endsection
