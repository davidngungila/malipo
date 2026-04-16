@extends('layouts.app')

@section('title', 'Bulk Control Numbers - MUSARIS System')

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
                <li><span class="text-gray-700 font-medium">Bulk Control Numbers</span></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bulk Control Numbers</h1>
                <p class="text-gray-600 mt-1">Generate and manage bulk control numbers for batch processing</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Generate Bulk Numbers
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Generation Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <h3 class="text-sm font-medium text-gray-500">Bulk Generated Today</h3>
            <p class="text-2xl font-bold text-gray-900">1,500</p>
            <p class="text-xs text-green-600 mt-1">50 batches</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <h3 class="text-sm font-medium text-gray-500">Total Bulk Numbers</h3>
            <p class="text-2xl font-bold text-gray-900">45,234</p>
            <p class="text-xs text-blue-600 mt-1">1,847 batches</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <h3 class="text-sm font-medium text-gray-500">Used Today</h3>
            <p class="text-2xl font-bold text-gray-900">823</p>
            <p class="text-xs text-yellow-600 mt-1">54.9% usage rate</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <h3 class="text-sm font-medium text-gray-500">Success Rate</h3>
            <p class="text-2xl font-bold text-gray-900">99.2%</p>
            <p class="text-xs text-purple-600 mt-1">High reliability</p>
        </div>
    </div>

    <!-- Bulk Generation Form -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Generate Bulk Control Numbers</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Control Numbers</label>
                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="100">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bill Type</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option>Electricity</option>
                    <option>Water</option>
                    <option>Gas</option>
                    <option>Internet</option>
                    <option>Mixed</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Amount Range</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option>TZS 1,000 - 10,000</option>
                    <option>TZS 10,000 - 50,000</option>
                    <option>TZS 50,000 - 100,000</option>
                    <option>TZS 100,000+</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
        </div>
        <div class="mt-4">
            <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
                Generate Bulk Numbers
            </button>
        </div>
    </div>

    <!-- Bulk Batches Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Bulk Generation Batches</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Showing 1-10 of 1,847</span>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numbers Generated</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bill Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generated</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">BULK20250416001</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">100</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Electricity</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-16 09:00</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-green-600 hover:text-green-900">View</button>
                                <button class="text-blue-600 hover:text-blue-900">Export</button>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">BULK20250416002</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">250</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Water</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Processing</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-16 08:30</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-green-600 hover:text-green-900">View</button>
                                <button class="text-blue-600 hover:text-blue-900">Export</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
