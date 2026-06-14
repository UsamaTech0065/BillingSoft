@extends('layouts.app')

@section('title', 'Add Customer')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('customers.index') }}" class="text-sm text-slate-500 hover:text-slate-700">← Back to Customers</a>
        <h1 class="text-3xl font-bold text-slate-900 mt-2">Add Customer</h1>
    </div>
    <form action="{{ route('customers.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        @csrf
        @include('customers._form')
        <button type="submit" class="w-full mt-6 px-4 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700">Save Customer</button>
    </form>
</div>
@endsection
