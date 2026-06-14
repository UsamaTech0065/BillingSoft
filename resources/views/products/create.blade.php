@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Products
        </a>
        <h1 class="text-3xl font-bold text-slate-900">Add Product</h1>
        <p class="text-slate-500 mt-1">Create a new good or service</p>
    </div>

    <form action="{{ route('products.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        @csrf
        @include('products._form')
        <div class="mt-8 flex gap-3">
            <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">Save Product</button>
            <a href="{{ route('products.index') }}" class="px-4 py-2.5 border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 transition">Cancel</a>
        </div>
    </form>
</div>
@endsection
