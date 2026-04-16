@extends('layouts.app')

@section('title', 'Account Statement - MUSARIS System')

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
                <li><span class="text-gray-700 font-medium">Account Statement</span></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Account Statement</h1>
                <p class="text-gray-600 mt-1">Detailed account statement and transaction history</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Download Statement
                </button>
            </div>
        </div>
    </div>

    <!-- Statement Period Selection -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statement Period</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" value="2025-04-01">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" value="2025-04-16">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statement Type</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option>Detailed Statement</option>
                    <option>Summary Statement</option>
                    <option>Tax Statement</option>
                </select>
            </div>
        </div>
        <div class="mt-4">
            <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                Generate Statement
            </button>
        </div>
    </div>

    <!-- Statement Summary -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statement Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500">Opening Balance</h4>
                <p class="text-xl font-bold text-gray-900">TZS 18.7M</p>
                <p class="text-xs text-gray-500 mt-1">April 1, 2025</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500">Total Credits</h4>
                <p class="text-xl font-bold text-green-600">+TZS 8.9M</p>
                <p class="text-xs text-gray-500 mt-1">2,341 transactions</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500">Total Debits</h4>
                <p class="text-xl font-bold text-red-600">-TZS 4.5M</p>
                <p class="text-xs text-gray-500 mt-1">723 transactions</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500">Closing Balance</h4>
                <p class="text-xl font-bold text-gray-900">TZS 23.1M</p>
                <p class="text-xs text-gray-500 mt-1">April 16, 2025</p>
            </div>
        </div>
    </div>

    <!-- Statement Transactions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Transaction Details</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">April 1-16, 2025</span>
                    <span class="text-sm text-gray-500">3,064 transactions</span>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-16</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#TXN001</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Payment from John Doe</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Payment</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">TZS 15,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 23,115,000</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-16</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#TXN002</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Payout to Jane Smith</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Payout</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">TZS 8,500</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 23,100,000</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-16</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#TXN003</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Payment from Mike Johnson</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Payment</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">TZS 25,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 23,125,000</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-15</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#TXN004</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">System Fee Deduction</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Fee</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">TZS 450</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 23,100,000</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
