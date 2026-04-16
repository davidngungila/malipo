@extends('layouts.app')

@section('title', 'Advanced Dashboard')
@section('content')
<div class="flex-1 flex flex-col min-w-0">
    <div class="p-3 lg:p-6 overflow-auto bg-gray-50" style="height: calc(100vh - 160px);">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Advanced Dashboard</h1>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">Real-time monitoring and analytics</span>
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Total Transactions</h3>
                                    <p class="text-2xl font-bold text-blue-600">1,234</p>
                                    <p class="text-sm text-gray-500">Last 30 days</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Total Revenue</h3>
                                    <p class="text-2xl font-bold text-green-600">TZS 2,456,789</p>
                                    <p class="text-sm text-gray-500">This month</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <i class="fas fa-users text-purple-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Active Users</h3>
                                    <p class="text-2xl font-bold text-purple-600">847</p>
                                    <p class="text-sm text-gray-500">Currently online</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex items-center">
                                <div class="p-3 bg-orange-100 rounded-full">
                                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Avg Response Time</h3>
                                    <p class="text-2xl font-bold text-orange-600">1.2s</p>
                                    <p class="text-sm text-gray-500">Last 24 hours</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="mt-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Activity</h2>
                        <div class="bg-white shadow rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2 mins ago</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Payment</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">John Doe</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">TZS 5,000</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Success</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">5 mins ago</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Payout</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Jane Smith</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">TZS 2,500</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Success</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">8 mins ago</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">Transfer</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Mike Johnson</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">TZS 1,200</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Pending</span>
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
