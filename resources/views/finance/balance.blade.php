@extends('layouts.app')

@section('title', 'Account Balance - MUSARIS System')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><span class="text-gray-400">Finance & Reports</span></li>
                <li><span class="text-gray-400">/</span></li>
                <li><span class="text-gray-700 font-medium">Account Balance</span></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Account Balance</h1>
                <p class="text-gray-600 mt-1">Current account balances and financial overview</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Export Balance Report
                </button>
            </div>
        </div>
    </div>

    <!-- Balance Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <h3 class="text-sm font-medium text-gray-500">Current Balance</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 23.1M</p>
            <p class="text-xs text-green-600 mt-1">Available funds</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <h3 class="text-sm font-medium text-gray-500">Pending Credits</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 8.7M</p>
            <p class="text-xs text-blue-600 mt-1">Awaiting clearance</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <h3 class="text-sm font-medium text-gray-500">Pending Debits</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 4.3M</p>
            <p class="text-xs text-yellow-600 mt-1">Scheduled payouts</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <h3 class="text-sm font-medium text-gray-500">Net Balance</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 27.5M</p>
            <p class="text-xs text-purple-600 mt-1">After pending</p>
        </div>
    </div>

    <!-- Balance Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Balance Trend</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded">
                <p class="text-gray-500">Balance trend chart placeholder</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Balance Distribution</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded">
                <p class="text-gray-500">Balance distribution chart placeholder</p>
            </div>
        </div>
    </div>

    <!-- Account Details -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Account Details</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Primary Account</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Account Number:</span>
                            <span class="text-sm font-medium">MUSARIS-001</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Account Type:</span>
                            <span class="text-sm font-medium">Business Account</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Currency:</span>
                            <span class="text-sm font-medium">TZS</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Status:</span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Transaction Limits</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Daily Limit:</span>
                            <span class="text-sm font-medium">TZS 5M</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Monthly Limit:</span>
                            <span class="text-sm font-medium">TZS 50M</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Used Today:</span>
                            <span class="text-sm font-medium text-green-600">TZS 2.1M</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Used This Month:</span>
                            <span class="text-sm font-medium text-blue-600">TZS 23.4M</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Recent Balance Transactions</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Last 7 days</span>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance After</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-16 14:23</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Payment from John Doe</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Credit</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+TZS 15,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 23,115,000</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-16 13:45</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Payout to Jane Smith</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Debit</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">-TZS 8,500</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 23,100,000</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-16 12:30</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Payment from Mike Johnson</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Credit</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+TZS 25,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 23,108,500</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
