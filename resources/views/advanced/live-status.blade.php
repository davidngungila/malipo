@extends('layouts.app')

@section('title', 'Live Payment Status')
@section('content')
<div class="flex-1 flex flex-col min-w-0">
    <div class="p-3 lg:p-6 overflow-auto bg-gray-50" style="height: calc(100vh - 160px);">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Live Payment Status</h1>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">Real-time payment monitoring</span>
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-sm text-green-600 font-medium">System Online</span>
                        </div>
                    </div>

                    <!-- Status Overview -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Active Payments</h3>
                                    <p class="text-3xl font-bold text-green-600">24</p>
                                    <p class="text-sm text-gray-500">Currently processing</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex items-center">
                                <div class="p-3 bg-yellow-100 rounded-full">
                                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Pending Payments</h3>
                                    <p class="text-3xl font-bold text-yellow-600">12</p>
                                    <p class="text-sm text-gray-500">Awaiting confirmation</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex items-center">
                                <div class="p-3 bg-red-100 rounded-full">
                                    <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Failed Payments</h3>
                                    <p class="text-3xl font-bold text-red-600">3</p>
                                    <p class="text-sm text-gray-500">Last 24 hours</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live Transactions Feed -->
                    <div class="mt-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Live Transaction Feed</h2>
                        <div class="bg-white shadow rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Just now</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">FEEDTAN123456789</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Payment</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">TZS 5,000</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Success</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">1 min ago</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">FEEDTAN987654321</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Payout</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">TZS 2,500</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Processing</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2 mins ago</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">FEEDTAN456789123</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Payment</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">TZS 1,000</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Failed</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
