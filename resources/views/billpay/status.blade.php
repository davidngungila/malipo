@extends('layouts.app')

@section('title', 'BillPay Status - MUSARIS System')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><span class="text-gray-400">BillPay</span></li>
                <li><span class="text-gray-400">/</span></li>
                <li><span class="text-gray-700 font-medium">BillPay Status</span></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">BillPay Status Dashboard</h1>
                <p class="text-gray-600 mt-1">Monitor BillPay system status and performance</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Refresh Status
                </button>
            </div>
        </div>
    </div>

    <!-- BillPay Status Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <h3 class="text-sm font-medium text-gray-500">Active Controls</h3>
            <p class="text-2xl font-bold text-gray-900">12,891</p>
            <p class="text-xs text-green-600 mt-1">84.6% of total</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <h3 class="text-sm font-medium text-gray-500">Pending Payments</h3>
            <p class="text-2xl font-bold text-gray-900">234</p>
            <p class="text-xs text-blue-600 mt-1">Awaiting processing</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <h3 class="text-sm font-medium text-gray-500">Expired Today</h3>
            <p class="text-2xl font-bold text-gray-900">47</p>
            <p class="text-xs text-yellow-600 mt-1">Requires renewal</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <h3 class="text-sm font-medium text-gray-500">System Health</h3>
            <p class="text-2xl font-bold text-gray-900">98.9%</p>
            <p class="text-xs text-purple-600 mt-1">Excellent performance</p>
        </div>
    </div>

    <!-- Real-time System Monitor -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Real-time System Monitor</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-900">Control Number Generation</span>
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    </div>
                    <div class="text-xs text-gray-500">Last updated: 1 min ago</div>
                    <div class="text-sm text-green-600 mt-1">Operating normally</div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-900">Payment Processing</span>
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    </div>
                    <div class="text-xs text-gray-500">Last updated: 30 sec ago</div>
                    <div class="text-sm text-green-600 mt-1">All systems operational</div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-900">Database Sync</span>
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    </div>
                    <div class="text-xs text-gray-500">Last updated: 2 min ago</div>
                    <div class="text-sm text-green-600 mt-1">Sync complete</div>
                </div>
            </div>
        </div>
    </div>

    <!-- BillPay Status History -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Recent Status Changes</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Live updates</span>
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Control Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Previous Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">BP20250416001</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">3 min ago</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Payment confirmed</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">BP20250415023</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Expired</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">15 min ago</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Expired after 30 days</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
