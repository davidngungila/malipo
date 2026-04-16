@extends('layouts.app')

@section('title', 'Revenue Reports - MUSARIS System')

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
                <li><span class="text-gray-700 font-medium">Revenue Reports</span></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Revenue Reports</h1>
                <p class="text-gray-600 mt-1">Comprehensive revenue analysis and reporting</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Generate Report
                </button>
            </div>
        </div>
    </div>

    <!-- Revenue Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <h3 class="text-sm font-medium text-gray-500">Total Revenue</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 45.2M</p>
            <p class="text-xs text-green-600 mt-1">+12.5% from last month</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <h3 class="text-sm font-medium text-gray-500">Monthly Revenue</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 8.7M</p>
            <p class="text-xs text-blue-600 mt-1">+8.3% growth</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <h3 class="text-sm font-medium text-gray-500">Daily Average</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 1.5M</p>
            <p class="text-xs text-yellow-600 mt-1">247 transactions/day</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <h3 class="text-sm font-medium text-gray-500">Revenue per Customer</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 11,742</p>
            <p class="text-xs text-purple-600 mt-1">+3.2% increase</p>
        </div>
    </div>

    <!-- Revenue Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Trend</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded">
                <p class="text-gray-500">Revenue trend chart placeholder</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue by Payment Method</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded">
                <p class="text-gray-500">Payment methods revenue chart placeholder</p>
            </div>
        </div>
    </div>

    <!-- Revenue Breakdown Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Revenue Breakdown</h3>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transactions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Transaction</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% of Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Growth</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Electricity Bills</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">3,847</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 18.5M</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">TZS 4,806</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">40.9%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+15.2%</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Water Bills</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2,156</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 12.1M</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">TZS 5,617</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">26.8%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+8.7%</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Internet Services</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1,892</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 8.9M</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">TZS 4,698</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">19.7%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+12.3%</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Gas Bills</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1,234</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 5.7M</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">TZS 4,622</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">12.6%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">-2.1%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
