@extends('layouts.app')

@section('title', 'Edit ' . $company->company_name)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('companies.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Companies
        </a>
        <h1 class="text-3xl font-bold text-slate-900">Edit Company</h1>
        <p class="text-slate-500 mt-1">{{ $company->company_name }}</p>
    </div>

    <form action="{{ route('companies.update', $company) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('companies._form', ['company' => $company])
        <div class="mt-6">
            <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-violet-700 shadow-sm transition">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
