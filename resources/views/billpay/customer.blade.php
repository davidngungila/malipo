@extends('layouts.app')

@section('title', 'Customer Control Numbers - MUSARIS System')

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
                <li><span class="text-gray-700 font-medium">Customer Control Numbers</span></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Customer Control Numbers</h1>
                <p class="text-gray-600 mt-1">Manage control numbers assigned to customers</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Assign to Customer
                </button>
            </div>
        </div>
    </div>

    <!-- Customer Control Numbers Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <h3 class="text-sm font-medium text-gray-500">Total Assigned</h3>
            <p class="text-2xl font-bold text-gray-900">8,734</p>
            <p class="text-xs text-green-600 mt-1">+234 this week</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <h3 class="text-sm font-medium text-gray-500">Customers with Numbers</h3>
            <p class="text-2xl font-bold text-gray-900">3,156</p>
            <p class="text-xs text-blue-600 mt-1">82% of active customers</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <h3 class="text-sm font-medium text-gray-500">Avg. per Customer</h3>
            <p class="text-2xl font-bold text-gray-900">2.8</p>
            <p class="text-xs text-yellow-600 mt-1">Stable distribution</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <h3 class="text-sm font-medium text-gray-500">Used Today</h3>
            <p class="text-2xl font-bold text-gray-900">156</p>
            <p class="text-xs text-purple-600 mt-1">+8% from yesterday</p>
        </div>
    </div>

    <!-- Customer Control Numbers Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Customer Control Numbers</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Showing 1-10 of 8,734</span>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Control Numbers</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Used</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-gray-600 font-medium">JD</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">John Doe</div>
                                    <div class="text-sm text-gray-500">+255 712 345 678</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="space-y-1">
                                <div class="text-xs">BP20250416001</div>
                                <div class="text-xs">BP20250415023</div>
                                <div class="text-xs text-gray-400">+2 more</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 125,000</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-16 10:30</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-green-600 hover:text-green-900">View</button>
                                <button class="text-blue-600 hover:text-blue-900">Manage</button>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-gray-600 font-medium">JS</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Jane Smith</div>
                                    <div class="text-sm text-gray-500">+255 755 234 567</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="space-y-1">
                                <div class="text-xs">BP20250416002</div>
                                <div class="text-xs">BP20250414015</div>
                                <div class="text-xs">BP20250413008</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 87,500</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-15 14:20</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-green-600 hover:text-green-900">View</button>
                                <button class="text-blue-600 hover:text-blue-900">Manage</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
