@extends('layouts.app')

@section('title', 'Add Company')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('companies.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Companies
        </a>
        <h1 class="text-3xl font-bold text-slate-900">Add Company</h1>
        <p class="text-slate-500 mt-1">Create a new company with its own products and invoices</p>
    </div>

    <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('companies._form')
        <div class="mt-6">
            <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-violet-700 shadow-sm transition">
                Create Company
            </button>
        </div>
    </form>
</div>
@endsection
