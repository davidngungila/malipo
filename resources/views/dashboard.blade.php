@extends('layouts.app')

@section('title', 'Dashboard Overview - MUSARIS System')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="#" class="text-gray-500 hover:text-gray-700">Home</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><span class="text-gray-700 font-medium">Dashboard</span></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Overview</h1>
                <p class="text-gray-600 mt-1">Welcome to MUSARIS Payment & Collection System</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <span class="text-sm text-gray-500">Last updated: {{ now()->format('M j, Y H:i') }}</span>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-green-600 font-bold text-lg">T</span>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Transactions</h3>
                    <p class="text-2xl font-bold text-gray-900">12,543</p>
                    <p class="text-xs text-green-600 mt-1">+12.5% from last month</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 font-bold text-lg">$</span>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Revenue</h3>
                    <p class="text-2xl font-bold text-gray-900">TZS 45.2M</p>
                    <p class="text-xs text-blue-600 mt-1">+8.3% from last month</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <span class="text-yellow-600 font-bold text-lg">C</span>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Active Customers</h3>
                    <p class="text-2xl font-bold text-gray-900">3,847</p>
                    <p class="text-xs text-yellow-600 mt-1">+156 new this month</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-purple-600 font-bold text-lg">P</span>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Pending Payments</h3>
                    <p class="text-2xl font-bold text-gray-900">234</p>
                    <p class="text-xs text-purple-600 mt-1">Requires attention</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Trend</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded">
                <p class="text-gray-500">Revenue chart placeholder</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Methods</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded">
                <p class="text-gray-500">Payment methods chart placeholder</p>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
                <a href="#" class="text-sm text-green-600 hover:text-green-700">View all</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#TRX001</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">John Doe</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">TZS 5,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">USSD</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-16</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#TRX002</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Jane Smith</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">TZS 12,500</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Card</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-16</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#TRX003</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Mike Johnson</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">TZS 8,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">USSD</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-15</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    </div>
@endsection
