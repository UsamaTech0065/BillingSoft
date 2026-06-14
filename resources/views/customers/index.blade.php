@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Customers</h1>
        <p class="text-slate-500 mt-1">Manage customers and advance payments</p>
    </div>
    <a href="{{ route('customers.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 shadow-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Customer
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6">
    <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customers..." class="flex-1 px-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        <button type="submit" class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-200">Search</button>
    </form>
</div>

@if($customers->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
        <h3 class="text-lg font-semibold text-slate-900">No customers yet</h3>
        <p class="text-slate-500 mt-1">Add your first customer to use in invoices.</p>
        <a href="{{ route('customers.create') }}" class="inline-flex mt-4 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg">Add Customer</a>
    </div>
@else
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Invoices</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase">Advance Balance</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($customers as $customer)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 font-medium text-slate-900">{{ $customer->name }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500">
                        {{ $customer->email ?? '—' }}
                        @if($customer->phone)<br>{{ $customer->phone }}@endif
                    </td>
                    <td class="px-6 py-4 text-slate-500">{{ $customer->invoices_count }}</td>
                    <td class="px-6 py-4 text-right">
                        @if($customer->advance_balance > 0)
                            <span class="font-semibold text-emerald-600">{{ money($customer->advance_balance) }}</span>
                        @else
                            <span class="text-slate-400">$0.00</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('customers.show', $customer) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View</a>
                        <a href="{{ route('customers.edit', $customer) }}" class="text-slate-500 hover:text-slate-700 text-sm font-medium ml-3">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $customers->withQueryString()->links() }}</div>
@endif
@endsection
