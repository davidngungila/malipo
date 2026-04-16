@extends('layouts.app')

@section('title', 'Payment History')
@section('content')
<div class="flex-1 flex flex-col min-w-0">
    <div class="p-3 lg:p-6 overflow-auto bg-gray-50" style="height: calc(100vh - 160px);">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Payment History</h1>
                        <div class="flex items-center space-x-4">
                            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-download mr-2"></i>Export CSV
                            </button>
                            <div class="flex items-center space-x-2">
                                <input type="date" class="border border-gray-300 rounded-lg px-3 py-2" placeholder="Start Date">
                                <span class="text-gray-500">to</span>
                                <input type="date" class="border border-gray-300 rounded-lg px-3 py-2" placeholder="End Date">
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="">All Status</option>
                                <option value="SUCCESS">Success</option>
                                <option value="PROCESSING">Processing</option>
                                <option value="PENDING">Pending</option>
                                <option value="FAILED">Failed</option>
                            </select>
                        </div>

                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                            <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="">All Types</option>
                                <option value="PAYMENT">Payment</option>
                                <option value="PAYOUT">Payout</option>
                                <option value="TRANSFER">Transfer</option>
                            </select>
                        </div>

                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Min Amount</label>
                            <input type="number" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="0" min="0">
                        </div>

                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Amount</label>
                            <input type="number" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="0" min="0">
                        </div>
                    </div>

                    <!-- Payment History Table -->
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <input type="checkbox" class="rounded border-gray-300">
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <input type="checkbox" class="rounded border-gray-300">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2026-04-16 09:30</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">FEEDTAN123456789</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Payment</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">John Doe</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 5,000</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Success</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">View</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <input type="checkbox" class="rounded border-gray-300">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2026-04-16 09:25</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">FEEDTAN987654321</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Payout</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Jane Smith</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 2,500</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Processing</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">View</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <input type="checkbox" class="rounded border-gray-300">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2026-04-16 09:20</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">FEEDTAN456789123</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">Transfer</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Mike Johnson</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 1,200</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Failed</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">View</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="flex items-center justify-between mt-6">
                        <div class="text-sm text-gray-700">
                            Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">47</span> results
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg bg-white text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg bg-white text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
