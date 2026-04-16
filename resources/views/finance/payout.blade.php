@extends('layouts.app')

@section('title', 'Payout Reports - MUSARIS System')

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
                <li><span class="text-gray-700 font-medium">Payout Reports</span></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Payout Reports</h1>
                <p class="text-gray-600 mt-1">Comprehensive payout analysis and reporting</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Generate Report
                </button>
            </div>
        </div>
    </div>

    <!-- Payout Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <h3 class="text-sm font-medium text-gray-500">Total Payouts</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 22.1M</p>
            <p class="text-xs text-green-600 mt-1">+8.3% from last month</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <h3 class="text-sm font-medium text-gray-500">Monthly Payouts</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 4.3M</p>
            <p class="text-xs text-blue-600 mt-1">723 transactions</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <h3 class="text-sm font-medium text-gray-500">Avg. Payout</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 5,744</p>
            <p class="text-xs text-yellow-600 mt-1">+3.1% increase</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <h3 class="text-sm font-medium text-gray-500">Processing Time</h3>
            <p class="text-2xl font-bold text-gray-900">2.3 hrs</p>
            <p class="text-xs text-purple-600 mt-1">-15min improvement</p>
        </div>
    </div>

    <!-- Payout Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payout Trend</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded">
                <p class="text-gray-500">Payout trend chart placeholder</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payout by Method</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded">
                <p class="text-gray-500">Payout methods chart placeholder</p>
            </div>
        </div>
    </div>

    <!-- Payout Breakdown Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Payout Breakdown</h3>
                <div class="flex items-center space-x-2">
                    <select class="px-3 py-1 border border-gray-300 rounded text-sm">
                        <option>This Month</option>
                        <option>Last Month</option>
                        <option>Last Quarter</option>
                        <option>This Year</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payout Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transactions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% of Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Success Rate</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Bank Transfer</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">423</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 2.8M</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">TZS 6,618</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">65.1%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">98.2%</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Mobile Money</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">234</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 1.2M</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">TZS 5,128</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">27.9%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">99.1%</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Cash Pickup</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">66</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 300K</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">TZS 4,545</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">7.0%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">100%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
