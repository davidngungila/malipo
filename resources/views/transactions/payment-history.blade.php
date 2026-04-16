@extends('layouts.app')

@section('title', 'Payment History - MUSARIS System')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><span class="text-gray-400">Transactions</span></li>
                <li><span class="text-gray-400">/</span></li>
                <li><span class="text-gray-700 font-medium">Payment History</span></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Payment History</h1>
                <p class="text-gray-600 mt-1">Complete history of all payment transactions</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Export Payment History
                </button>
            </div>
        </div>
    </div>

    <!-- Payment History Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <h3 class="text-sm font-medium text-gray-500">Total Payments</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 45.2M</p>
            <p class="text-xs text-green-600 mt-1">12,543 transactions</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <h3 class="text-sm font-medium text-gray-500">This Month</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 8.7M</p>
            <p class="text-xs text-blue-600 mt-1">2,341 transactions</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <h3 class="text-sm font-medium text-gray-500">Avg. Payment</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 3,603</p>
            <p class="text-xs text-yellow-600 mt-1">+5.2% from last month</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <h3 class="text-sm font-medium text-gray-500">Success Rate</h3>
            <p class="text-2xl font-bold text-gray-900">98.7%</p>
            <p class="text-xs text-purple-600 mt-1">164 failed this month</p>
        </div>
    </div>

    <!-- Payment History Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Payment Transactions History</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Showing 1-10 of 12,543</span>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#PAY001</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">John Doe</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 5,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">USSD</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Electricity Bill</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-16 11:23</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-green-600 hover:text-green-900">View</button>
                                <button class="text-blue-600 hover:text-blue-900">Receipt</button>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#PAY002</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Jane Smith</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 12,500</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Card</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Water Bill</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-16 10:45</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-green-600 hover:text-green-900">View</button>
                                <button class="text-blue-600 hover:text-blue-900">Receipt</button>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#PAY003</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Mike Johnson</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 8,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Mobile Money</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Internet Bill</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-16 09:30</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-green-600 hover:text-green-900">View</button>
                                <button class="text-blue-600 hover:text-blue-900">Receipt</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
