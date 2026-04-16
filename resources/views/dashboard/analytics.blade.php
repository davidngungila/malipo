@extends('layouts.app')

@section('title', 'Analytics Summary - MUSARIS System')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><span class="text-gray-700 font-medium">Analytics</span></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Analytics Summary</h1>
        <p class="text-gray-600 mt-1">Detailed analytics and insights for your payment system</p>
    </div>

    <!-- Analytics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Trends</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Daily Average</span>
                    <span class="text-sm font-medium">TZS 1.5M</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Weekly Growth</span>
                    <span class="text-sm font-medium text-green-600">+15.2%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Monthly Total</span>
                    <span class="text-sm font-medium">TZS 45.2M</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Analytics</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">New Customers</span>
                    <span class="text-sm font-medium">156</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Retention Rate</span>
                    <span class="text-sm font-medium text-green-600">92.3%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Avg. Transaction</span>
                    <span class="text-sm font-medium">TZS 3,600</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">System Performance</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Uptime</span>
                    <span class="text-sm font-medium text-green-600">99.9%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Response Time</span>
                    <span class="text-sm font-medium">1.2s</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Success Rate</span>
                    <span class="text-sm font-medium text-green-600">98.7%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Analytics</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded">
                <p class="text-gray-500">Revenue analytics chart placeholder</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Demographics</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded">
                <p class="text-gray-500">Customer demographics chart placeholder</p>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Performance Metrics</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metric</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Previous</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Change</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trend</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Total Revenue</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">TZS 45.2M</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">TZS 41.7M</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+8.3%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Upward</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Transaction Volume</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">12,543</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">11,142</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+12.5%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Upward</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Active Customers</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">3,847</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">3,691</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+4.2%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Stable</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
