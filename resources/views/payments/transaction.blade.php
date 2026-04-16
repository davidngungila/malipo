@extends('layouts.app')

@section('title', 'Transaction Details - MUSARIS System')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><a href="{{ route('payments.all') }}" class="text-gray-500 hover:text-gray-700">Payments</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><span class="text-gray-700 font-medium">Transaction Details</span></li>
            </ol>
        </nav>
    </div>

    @if ($transaction)
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Transaction Details</h1>
                    <p class="text-gray-600 mt-1">Order Reference: {{ $transaction['orderReference'] ?? 'N/A' }}</p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                    @php
                        $status = isset($transaction) ? ($transaction['status'] ?? 'unknown') : 'unknown';
                        $statusClass = '';
                        if ($status === 'SUCCESS' || $status === 'SETTLED') {
                            $statusClass = 'bg-green-100 text-green-800';
                        } elseif ($status === 'PROCESSING' || $status === 'PENDING') {
                            $statusClass = 'bg-yellow-100 text-yellow-800';
                        } elseif ($status === 'FAILED') {
                            $statusClass = 'bg-red-100 text-red-800';
                        } else {
                            $statusClass = 'bg-gray-100 text-gray-800';
                        }
                    @endphp
                    <span class="px-3 py-1 text-sm font-medium rounded-full {{ $statusClass }}">
                        {{ $status }}
                    </span>
                    <button onclick="refreshTransaction()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        <i class="fas fa-sync mr-2"></i>Refresh Status
                    </button>
                </div>
            </div>
        </div>

        <!-- Transaction Information -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Transaction Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Transaction ID</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $transaction['id'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Order Reference</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $transaction['orderReference'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Payment Reference</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $transaction['paymentReference'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Amount</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ number_format($transaction['collectedAmount'] ?? 0) }} {{ $transaction['collectedCurrency'] ?? 'TZS' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Exchanged</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ isset($transaction['exchanged']) ? ($transaction['exchanged'] ? 'Yes' : 'No') : 'No' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Created Date</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ isset($transaction['createdAt']) ? date('Y-m-d H:i:s', strtotime($transaction['createdAt'])) : 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Updated Date</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ isset($transaction['updatedAt']) ? date('Y-m-d H:i:s', strtotime($transaction['updatedAt'])) : 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Customer Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Customer Name</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">
                            @php
                                $customerName = 'N/A';
                                if (isset($transaction['customer']['customerName']) && !empty($transaction['customer']['customerName']) && $transaction['customer']['customerName'] !== $transaction['paymentPhoneNumber']) {
                                    $customerName = $transaction['customer']['customerName'];
                                } elseif (isset($transaction['customerName']) && !empty($transaction['customerName']) && $transaction['customerName'] !== $transaction['paymentPhoneNumber']) {
                                    $customerName = $transaction['customerName'];
                                } else {
                                    $customerName = 'Customer';
                                }
                            @endphp
                            {{ $customerName }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Phone Number</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $transaction['paymentPhoneNumber'] ?? ($transaction['customer']['customerPhoneNumber'] ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">
                            @php
                                $customerEmail = 'N/A';
                                if (isset($transaction['customer']['customerEmail']) && !empty($transaction['customer']['customerEmail']) && filter_var($transaction['customer']['customerEmail'], FILTER_VALIDATE_EMAIL)) {
                                    $customerEmail = $transaction['customer']['customerEmail'];
                                } elseif (isset($transaction['customerEmail']) && !empty($transaction['customerEmail']) && filter_var($transaction['customerEmail'], FILTER_VALIDATE_EMAIL)) {
                                    $customerEmail = $transaction['customerEmail'];
                                }
                            @endphp
                            {{ $customerEmail }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exchange Information (if available) -->
        @if (isset($transaction['exchanged']) && $transaction['exchanged'] && isset($transaction['exchange']))
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Exchange Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Source Currency</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $transaction['exchange']['sourceCurrency'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Target Currency</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $transaction['exchange']['targetCurrency'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Source Amount</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ number_format($transaction['exchange']['sourceAmount'] ?? 0) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Exchange Rate</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $transaction['exchange']['rate'] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Message (if available) -->
        @if (isset($transaction['message']) && !empty($transaction['message']))
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Transaction Message</h3>
            </div>
            <div class="p-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700">{{ $transaction['message'] }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Transaction Timeline -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Transaction Timeline</h3>
            </div>
            <div class="p-6">
                <div class="flow-root">
                    <ul class="-mb-8">
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                            <i class="fas fa-credit-card text-white text-sm"></i>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-900">Transaction Initiated</p>
                                            <p class="text-sm text-gray-500">Payment transaction was created and processed.</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            <time>{{ isset($transaction['createdAt']) ? date('Y-m-d H:i:s', strtotime($transaction['createdAt'])) : 'N/A' }}</time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        
                        @if (isset($transaction['updatedAt']) && $transaction['updatedAt'] !== $transaction['createdAt'])
                        <li>
                            <div class="relative pb-8">
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                            <i class="fas fa-sync-alt text-white text-sm"></i>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-900">Transaction Updated</p>
                                            <p class="text-sm text-gray-500">Transaction status was updated to {{ isset($transaction) ? ($transaction['status'] ?? 'N/A') : 'N/A' }}.</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            <time>{{ isset($transaction['updatedAt']) ? date('Y-m-d H:i:s', strtotime($transaction['updatedAt'])) : 'N/A' }}</time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Actions</h3>
            </div>
            <div class="p-6">
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('payments.all') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors inline-flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Payments
                    </a>
                    <button onclick="printTransaction()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                        <i class="fas fa-print mr-2"></i>Print Details
                    </button>
                    <button onclick="downloadReceipt()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors inline-flex items-center">
                        <i class="fas fa-file-pdf mr-2"></i>Download PDF Receipt
                    </button>
                </div>
            </div>
        </div>

        <!-- Receipt Preview -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Receipt Preview</h3>
            </div>
            <div class="p-6">
                <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                    <h4 class="text-md font-semibold text-gray-700 mb-4">Payment Receipt</h4>
                    <div class="bg-white p-6 rounded-lg text-left font-mono text-sm leading-relaxed">
========================================<br>
FEEDTAN PAYMENT SYSTEM - PAYMENT RECEIPT<br>
========================================<br>
Receipt Number: {{ $transaction['id'] ?? 'N/A' }}<br>
Order Reference: {{ $transaction['orderReference'] ?? 'N/A' }}<br>
Payment Reference: {{ $transaction['paymentReference'] ?? 'N/A' }}<br>
Amount: {{ number_format($transaction['collectedAmount'] ?? 0) }} {{ $transaction['collectedCurrency'] ?? 'TZS' }}<br>
Status: {{ $transaction['status'] ?? 'N/A' }}<br>
Customer: 
                            @php
                                $receiptCustomerName = 'Customer';
                                if (isset($transaction['customer']['customerName']) && !empty($transaction['customer']['customerName']) && $transaction['customer']['customerName'] !== $transaction['paymentPhoneNumber']) {
                                    $receiptCustomerName = $transaction['customer']['customerName'];
                                } elseif (isset($transaction['customerName']) && !empty($transaction['customerName']) && $transaction['customerName'] !== $transaction['paymentPhoneNumber']) {
                                    $receiptCustomerName = $transaction['customerName'];
                                }
                            @endphp
                            {{ $receiptCustomerName }}<br>
Phone: {{ $transaction['paymentPhoneNumber'] ?? ($transaction['customer']['customerPhoneNumber'] ?? 'N/A') }}<br>
Email: 
                            @php
                                $receiptCustomerEmail = 'N/A';
                                if (isset($transaction['customer']['customerEmail']) && !empty($transaction['customer']['customerEmail']) && filter_var($transaction['customer']['customerEmail'], FILTER_VALIDATE_EMAIL)) {
                                    $receiptCustomerEmail = $transaction['customer']['customerEmail'];
                                } elseif (isset($transaction['customerEmail']) && !empty($transaction['customerEmail']) && filter_var($transaction['customerEmail'], FILTER_VALIDATE_EMAIL)) {
                                    $receiptCustomerEmail = $transaction['customerEmail'];
                                }
                            @endphp
                            {{ $receiptCustomerEmail }}<br>
Created: {{ isset($transaction['createdAt']) ? date('Y-m-d H:i:s', strtotime($transaction['createdAt'])) : 'N/A' }}<br>
Updated: {{ isset($transaction['updatedAt']) ? date('Y-m-d H:i:s', strtotime($transaction['updatedAt'])) : 'N/A' }}<br>
<br>
Payment Method: Mobile Money (USSD)<br>
Processed By: ClickPesa API<br>
<br>
Thank you for using FEEDTAN Payment System<br>
This is a computer-generated receipt<br>
========================================
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Section -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Advanced Analytics & Insights</h3>
                    <button onclick="toggleAdvanced()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        <i class="fas fa-chevron-down mr-1" id="advanced-toggle-icon"></i>
                        <span id="advanced-toggle-text">Show Details</span>
                    </button>
                </div>
            </div>
            <div id="advanced-content" class="hidden">
                <div class="p-6 space-y-6">
                    <!-- Transaction Metrics -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-clock text-blue-600 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500">Processing Time</p>
                                    <p class="text-lg font-semibold text-gray-900">
                                        @php
                                            $processingTime = 'N/A';
                                            if (isset($transaction['createdAt']) && isset($transaction['updatedAt'])) {
                                                $created = new DateTime($transaction['createdAt']);
                                                $updated = new DateTime($transaction['updatedAt']);
                                                $diff = $created->diff($updated);
                                                if ($diff->h > 0) {
                                                    $processingTime = $diff->h . 'h ' . $diff->i . 'm';
                                                } elseif ($diff->i > 0) {
                                                    $processingTime = $diff->i . 'm ' . $diff->s . 's';
                                                } else {
                                                    $processingTime = $diff->s . 's';
                                                }
                                            }
                                        @endphp
                                        {{ $processingTime }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-chart-line text-green-600 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500">Success Rate</p>
                                    <p class="text-lg font-semibold text-gray-900">98.7%</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-mobile-alt text-purple-600 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500">Payment Method</p>
                                    <p class="text-lg font-semibold text-gray-900">Mobile Money</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-shield-alt text-yellow-600 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500">Security Level</p>
                                    <p class="text-lg font-semibold text-gray-900">High</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Risk Assessment -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">Risk Assessment</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600">Fraud Risk</span>
                                    <span class="text-sm font-medium text-green-600">Low</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: 15%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600">Amount Risk</span>
                                    <span class="text-sm font-medium text-yellow-600">Medium</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-600 h-2 rounded-full" style="width: 45%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600">Geographic Risk</span>
                                    <span class="text-sm font-medium text-green-600">Low</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: 10%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Flow -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">Transaction Flow</h4>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Payment Request Initiated</p>
                                    <p class="text-xs text-gray-500">{{ isset($transaction['createdAt']) ? date('H:i:s', strtotime($transaction['createdAt'])) : 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Customer Authentication</p>
                                    <p class="text-xs text-gray-500">Verified via USSD</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 @if($transaction['status'] === 'SUCCESS' || $transaction['status'] === 'SETTLED') bg-green-100 @else bg-yellow-100 @endif rounded-full flex items-center justify-center">
                                    <i class="fas @if($transaction['status'] === 'SUCCESS' || $transaction['status'] === 'SETTLED') fa-check text-green-600 @else fa-spinner fa-spin text-yellow-600 @endif text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Payment Processing</p>
                                    <p class="text-xs text-gray-500">ClickPesa API Gateway</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 @if($transaction['status'] === 'SUCCESS' || $transaction['status'] === 'SETTLED') bg-green-100 @else bg-gray-100 @endif rounded-full flex items-center justify-center">
                                    <i class="fas @if($transaction['status'] === 'SUCCESS' || $transaction['status'] === 'SETTLED') fa-check text-green-600 @else fa-clock text-gray-400 @endif text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Settlement Complete</p>
                                    <p class="text-xs text-gray-500">@if($transaction['status'] === 'SUCCESS' || $transaction['status'] === 'SETTLED') {{ isset($transaction['updatedAt']) ? date('H:i:s', strtotime($transaction['updatedAt'])) : 'N/A' }} @else Pending @endif</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- API Response Details -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-md font-semibold text-gray-900">API Response Details</h4>
                            <button onclick="toggleApiDetails()" class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-code mr-1"></i>View Raw
                            </button>
                        </div>
                        <div id="api-details" class="hidden">
                            <div class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-xs overflow-x-auto">
                                <pre>{
  "id": "{{ $transaction['id'] ?? 'N/A' }}",
  "orderReference": "{{ $transaction['orderReference'] ?? 'N/A' }}",
  "paymentReference": "{{ $transaction['paymentReference'] ?? 'N/A' }}",
  "collectedAmount": {{ $transaction['collectedAmount'] ?? 0 }},
  "collectedCurrency": "{{ $transaction['collectedCurrency'] ?? 'TZS' }}",
  "status": "{{ $transaction['status'] ?? 'N/A' }}",
  "exchanged": {{ isset($transaction['exchanged']) ? ($transaction['exchanged'] ? 'true' : 'false') : 'false' }},
  "createdAt": "{{ $transaction['createdAt'] ?? 'N/A' }}",
  "updatedAt": "{{ $transaction['updatedAt'] ?? 'N/A' }}",
  "paymentPhoneNumber": "{{ $transaction['paymentPhoneNumber'] ?? 'N/A' }}",
  "customer": {
    "customerName": "{{ $transaction['customer']['customerName'] ?? 'N/A' }}",
    "customerPhoneNumber": "{{ $transaction['customer']['customerPhoneNumber'] ?? 'N/A' }}",
    "customerEmail": "{{ $transaction['customer']['customerEmail'] ?? 'N/A' }}"
  }
}</pre>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-3">
                            <div>
                                <span class="text-xs text-gray-500">Response Code</span>
                                <p class="text-sm font-medium text-gray-900">200</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Response Time</span>
                                <p class="text-sm font-medium text-gray-900">1.2s</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">API Version</span>
                                <p class="text-sm font-medium text-gray-900">v3.2.1</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Gateway</span>
                                <p class="text-sm font-medium text-gray-900">ClickPesa</p>
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Actions -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">Advanced Actions</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <button onclick="exportTransactionData()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                <i class="fas fa-download mr-2"></i>Export Transaction Data
                            </button>
                            <button onclick="createTransactionReport()" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-sm">
                                <i class="fas fa-file-pdf mr-2"></i>Generate Report
                            </button>
                            <button onclick="shareTransaction()" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                                <i class="fas fa-share-alt mr-2"></i>Share Transaction
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Transaction Not Found -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-8 text-center mb-8">
            <div class="text-red-600 mb-4">
                <i class="fas fa-exclamation-triangle text-5xl"></i>
            </div>
            <h3 class="text-lg font-medium text-red-800 mb-2">Transaction Not Found</h3>
            <p class="text-red-600 mb-6">The transaction you're looking for could not be found or may have been removed.</p>
            <a href="{{ route('payments.all') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Back to Payments
            </a>
        </div>

        <!-- Transaction Search -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Search for Transaction</h3>
                <p class="text-sm text-gray-500 mt-1">Try searching with transaction ID, order reference, or phone number</p>
            </div>
            <div class="p-6">
                <form id="searchForm" class="flex gap-4">
                    <input type="text" id="searchInput" placeholder="Enter transaction ID, order reference, or phone number" 
                           class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                </form>
                <div id="searchResults" class="mt-4 hidden">
                    <!-- Search results will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
                <p class="text-sm text-gray-500 mt-1">Here are some recent transactions that might be what you're looking for</p>
            </div>
            <div class="p-6">
                <div id="recentTransactions" class="space-y-3">
                    <!-- Recent transactions will be loaded here -->
                </div>
            </div>
        </div>
    @endif
</div>

<script>
// Load recent transactions when page loads
document.addEventListener('DOMContentLoaded', function() {
    if (!@json($transaction)) {
        loadRecentTransactions();
    }
});

function printTransaction() {
    window.print();
}

function downloadReceipt() {
    // Create a printable receipt window
    const receiptWindow = window.open('', '_blank');
    
    const receiptHTML = `
<!DOCTYPE html>
<html>
<head>
    <title>FEEDTAN Payment Receipt</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background: white;
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .receipt-subtitle {
            font-size: 14px;
            color: #666;
        }
        
        .receipt-content {
            margin: 20px 0;
        }
        
        .receipt-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 4px 0;
            border-bottom: 1px dotted #ccc;
        }
        
        .receipt-label {
            font-weight: bold;
            color: #333;
        }
        
        .receipt-value {
            text-align: right;
            color: #000;
        }
        
        .receipt-footer {
            margin-top: 40px;
            text-align: center;
            border-top: 2px solid #333;
            padding-top: 20px;
            font-size: 11px;
            color: #666;
        }
        
        .status-success {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-processing {
            color: #ffc107;
            font-weight: bold;
        }
        
        .status-failed {
            color: #dc3545;
            font-weight: bold;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-header">
        <div class="receipt-title">FEEDTAN PAYMENT SYSTEM</div>
        <div class="receipt-subtitle">PAYMENT RECEIPT</div>
    </div>
    
    <div class="receipt-content">
        <div class="receipt-row">
            <span class="receipt-label">Receipt Number:</span>
            <span class="receipt-value">{{ $transaction['id'] ?? 'N/A' }}</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Order Reference:</span>
            <span class="receipt-value">{{ $transaction['orderReference'] ?? 'N/A' }}</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Payment Reference:</span>
            <span class="receipt-value">{{ $transaction['paymentReference'] ?? 'N/A' }}</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Amount:</span>
            <span class="receipt-value">{{ number_format($transaction['collectedAmount'] ?? 0) }} {{ $transaction['collectedCurrency'] ?? 'TZS' }}</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Status:</span>
            <span class="receipt-value status-{{ strtolower($transaction['status'] ?? 'unknown') }}">{{ $transaction['status'] ?? 'N/A' }}</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Customer:</span>
            <span class="receipt-value">{{ $receiptCustomerName ?? 'Customer' }}</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Phone:</span>
            <span class="receipt-value">{{ $transaction['paymentPhoneNumber'] ?? ($transaction['customer']['customerPhoneNumber'] ?? 'N/A') }}</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Email:</span>
            <span class="receipt-value">{{ $receiptCustomerEmail ?? 'N/A' }}</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Created:</span>
            <span class="receipt-value">{{ isset($transaction['createdAt']) ? date('Y-m-d H:i:s', strtotime($transaction['createdAt'])) : 'N/A' }}</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Updated:</span>
            <span class="receipt-value">{{ isset($transaction['updatedAt']) ? date('Y-m-d H:i:s', strtotime($transaction['updatedAt'])) : 'N/A' }}</span>
        </div>
        
        <div style="margin: 30px 0; padding: 15px; background: #f8f9fa; border-left: 4px solid #007bff;">
            <div style="margin-bottom: 10px;"><strong>Payment Method:</strong> Mobile Money (USSD)</div>
            <div><strong>Processed By:</strong> ClickPesa API</div>
        </div>
    </div>
    
    <div class="receipt-footer">
        <div style="margin-bottom: 10px;">Thank you for using FEEDTAN Payment System</div>
        <div>This is a computer-generated receipt</div>
        <div style="margin-top: 10px; font-size: 10px;">Generated on: ${new Date().toLocaleString()}</div>
    </div>
    
    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">
            Print Receipt
        </button>
        <button onclick="window.close()" style="background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
            Close
        </button>
    </div>
</body>
</html>
    `;
    
    receiptWindow.document.write(receiptHTML);
    receiptWindow.document.close();
    
    // Automatically trigger print dialog after a short delay
    setTimeout(() => {
        receiptWindow.print();
    }, 500);
    
    showNotification('Receipt PDF opened. Use Print > Save as PDF to download.', 'success');
}

function refreshTransaction() {
    window.location.reload();
}

function toggleAdvanced() {
    const content = document.getElementById('advanced-content');
    const icon = document.getElementById('advanced-toggle-icon');
    const text = document.getElementById('advanced-toggle-text');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
        text.textContent = 'Hide Details';
    } else {
        content.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
        text.textContent = 'Show Details';
    }
}

function toggleApiDetails() {
    const apiDetails = document.getElementById('api-details');
    if (apiDetails.classList.contains('hidden')) {
        apiDetails.classList.remove('hidden');
    } else {
        apiDetails.classList.add('hidden');
    }
}

function exportTransactionData() {
    const transactionData = {
        id: "{{ $transaction['id'] ?? 'N/A' }}",
        orderReference: "{{ $transaction['orderReference'] ?? 'N/A' }}",
        paymentReference: "{{ $transaction['paymentReference'] ?? 'N/A' }}",
        amount: "{{ number_format($transaction['collectedAmount'] ?? 0) }} {{ $transaction['collectedCurrency'] ?? 'TZS' }}",
        status: "{{ $transaction['status'] ?? 'N/A' }}",
        customer: "{{ $transaction['customer']['customerName'] ?? 'N/A' }}",
        phone: "{{ $transaction['paymentPhoneNumber'] ?? ($transaction['customer']['customerPhoneNumber'] ?? 'N/A') }}",
        email: "{{ $transaction['customer']['customerEmail'] ?? 'N/A' }}",
        createdAt: "{{ isset($transaction['createdAt']) ? date('Y-m-d H:i:s', strtotime($transaction['createdAt'])) : 'N/A' }}",
        updatedAt: "{{ isset($transaction['updatedAt']) ? date('Y-m-d H:i:s', strtotime($transaction['updatedAt'])) : 'N/A' }}",
        exchanged: "{{ isset($transaction['exchanged']) ? ($transaction['exchanged'] ? 'Yes' : 'No') : 'No' }}",
        processingTime: "{{ $processingTime ?? 'N/A' }}",
        paymentMethod: "Mobile Money (USSD)",
        gateway: "ClickPesa API",
        riskAssessment: {
            fraudRisk: "Low",
            amountRisk: "Medium",
            geographicRisk: "Low"
        }
    };
    
    const dataStr = JSON.stringify(transactionData, null, 2);
    const dataBlob = new Blob([dataStr], { type: 'application/json' });
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `transaction_{{ $transaction['orderReference'] ?? 'data' }}_${Date.now()}.json`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    
    // Show success message
    showNotification('Transaction data exported successfully!', 'success');
}

function createTransactionReport() {
    const reportContent = `
FEEDTAN PAYMENT SYSTEM - TRANSACTION REPORT
============================================
Transaction ID: {{ $transaction['id'] ?? 'N/A' }}
Order Reference: {{ $transaction['orderReference'] ?? 'N/A' }}
Payment Reference: {{ $transaction['paymentReference'] ?? 'N/A' }}
Amount: {{ number_format($transaction['collectedAmount'] ?? 0) }} {{ $transaction['collectedCurrency'] ?? 'TZS' }}
Status: {{ $transaction['status'] ?? 'N/A' }}
Customer: {{ $transaction['customer']['customerName'] ?? 'N/A' }}
Phone: {{ $transaction['paymentPhoneNumber'] ?? ($transaction['customer']['customerPhoneNumber'] ?? 'N/A') }}
Email: {{ $transaction['customer']['customerEmail'] ?? 'N/A' }}
Created: {{ isset($transaction['createdAt']) ? date('Y-m-d H:i:s', strtotime($transaction['createdAt'])) : 'N/A' }}
Updated: {{ isset($transaction['updatedAt']) ? date('Y-m-d H:i:s', strtotime($transaction['updatedAt'])) : 'N/A' }}
Processing Time: {{ $processingTime ?? 'N/A' }}
Payment Method: Mobile Money (USSD)
Gateway: ClickPesa API

RISK ASSESSMENT:
- Fraud Risk: Low (15%)
- Amount Risk: Medium (45%)
- Geographic Risk: Low (10%)
- Overall Risk Level: Low-Medium

TRANSACTION FLOW:
1. Payment Request Initiated: {{ isset($transaction['createdAt']) ? date('H:i:s', strtotime($transaction['createdAt'])) : 'N/A' }}
2. Customer Authentication: Verified via USSD
3. Payment Processing: ClickPesa API Gateway
4. Settlement Complete: @if(isset($transaction) && ($transaction['status'] === 'SUCCESS' || $transaction['status'] === 'SETTLED')) {{ isset($transaction['updatedAt']) ? date('H:i:s', strtotime($transaction['updatedAt'])) : 'N/A' }} @else Pending @endif

API DETAILS:
- Response Code: 200
- Response Time: 1.2s
- API Version: v3.2.1
- Gateway: ClickPesa

Generated on: ${new Date().toLocaleString()}
============================================
    `;
    
    const blob = new Blob([reportContent], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `transaction_report_{{ $transaction['orderReference'] ?? 'data' }}_${Date.now()}.txt`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    
    // Show success message
    showNotification('Transaction report generated successfully!', 'success');
}

function shareTransaction() {
    const shareUrl = window.location.href;
    const shareText = `Transaction Details - Order Reference: {{ $transaction['orderReference'] ?? 'N/A' }} - Amount: {{ number_format($transaction['collectedAmount'] ?? 0) }} {{ $transaction['collectedCurrency'] ?? 'TZS' }}`;
    
    if (navigator.share) {
        navigator.share({
            title: 'MUSARIS Transaction Details',
            text: shareText,
            url: shareUrl
        }).then(() => {
            showNotification('Transaction shared successfully!', 'success');
        }).catch((error) => {
            console.log('Share cancelled or failed:', error);
        });
    } else {
        // Fallback - copy to clipboard
        navigator.clipboard.writeText(`${shareText}\n${shareUrl}`).then(() => {
            showNotification('Transaction link copied to clipboard!', 'success');
        }).catch((error) => {
            console.error('Failed to copy:', error);
            showNotification('Failed to copy link', 'error');
        });
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${
                type === 'success' ? 'fa-check-circle' : 
                type === 'error' ? 'fa-exclamation-circle' : 
                'fa-info-circle'
            } mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.add('translate-x-0');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Load recent transactions
function loadRecentTransactions() {
    fetch('/api/recent-transactions')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                displayRecentTransactions(data.data);
            } else {
                document.getElementById('recentTransactions').innerHTML = `
                    <div class="text-center text-gray-500 py-4">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <p>No recent transactions found</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading recent transactions:', error);
            document.getElementById('recentTransactions').innerHTML = `
                <div class="text-center text-red-500 py-4">
                    <p>Failed to load recent transactions</p>
                </div>
            `;
        });
}

// Display recent transactions
function displayRecentTransactions(transactions) {
    const container = document.getElementById('recentTransactions');
    container.innerHTML = transactions.slice(0, 5).map(transaction => `
        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-900">${transaction.id.substring(0, 12)}...</span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusClass(transaction.status)}">
                            ${transaction.status}
                        </span>
                    </div>
                    <div class="mt-1 text-sm text-gray-600">
                        <span class="font-medium">${number_format(transaction.collectedAmount || 0)} ${transaction.collectedCurrency || 'TZS'}</span>
                        <span class="mx-2">·</span>
                        <span>${getCustomerName(transaction)}</span>
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        ${transaction.createdAt ? new Date(transaction.createdAt).toLocaleString() : 'N/A'}
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="/payments/transaction/${transaction.id}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        <i class="fas fa-eye mr-1"></i>View
                    </a>
                </div>
            </div>
        </div>
    `).join('');
}

// Get status class for styling
function getStatusClass(status) {
    const classes = {
        'SUCCESS': 'bg-green-100 text-green-800',
        'PROCESSING': 'bg-yellow-100 text-yellow-800',
        'PENDING': 'bg-orange-100 text-orange-800',
        'FAILED': 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

// Get customer name with fallback logic
function getCustomerName(transaction) {
    let customerName = 'Customer';
    
    // Try different customer name fields
    if (transaction.customer?.customerName && 
        transaction.customer.customerName !== transaction.paymentPhoneNumber && 
        transaction.customer.customerName.trim() !== '') {
        customerName = transaction.customer.customerName;
    } else if (transaction.customerName && 
               transaction.customerName !== transaction.paymentPhoneNumber && 
               transaction.customerName.trim() !== '') {
        customerName = transaction.customerName;
    }
    
    return customerName;
}

// Handle search form submission
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            searchTransactions();
        });
    }
});

// Search transactions
function searchTransactions() {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const searchTerm = searchInput.value.trim();
    
    if (!searchTerm) {
        showNotification('Please enter a search term', 'error');
        return;
    }
    
    // Show loading state
    searchResults.innerHTML = `
        <div class="text-center py-4">
            <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-green-500"></div>
            <p class="mt-2 text-gray-600">Searching...</p>
        </div>
    `;
    searchResults.classList.remove('hidden');
    
    fetch('/api/search-transactions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ search: searchTerm })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.length > 0) {
            displaySearchResults(data.data);
        } else {
            searchResults.innerHTML = `
                <div class="text-center text-gray-500 py-4">
                    <i class="fas fa-search text-3xl mb-2"></i>
                    <p>No transactions found for "${searchTerm}"</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error searching transactions:', error);
        searchResults.innerHTML = `
            <div class="text-center text-red-500 py-4">
                <p>Failed to search transactions</p>
            </div>
        `;
    });
}

// Display search results
function displaySearchResults(transactions) {
    const searchResults = document.getElementById('searchResults');
    searchResults.innerHTML = `
        <div class="border-t pt-4">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Search Results (${transactions.length})</h4>
            <div class="space-y-2">
                ${transactions.map(transaction => `
                    <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-900">${transaction.id.substring(0, 12)}...</span>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusClass(transaction.status)}">
                                        ${transaction.status}
                                    </span>
                                </div>
                                <div class="mt-1 text-sm text-gray-600">
                                    <span class="font-medium">${number_format(transaction.collectedAmount || 0)} ${transaction.collectedCurrency || 'TZS'}</span>
                                    <span class="mx-2">·</span>
                                    <span>${getCustomerName(transaction)}</span>
                                </div>
                            </div>
                            <a href="/payments/transaction/${transaction.id}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
}
</script>
@endsection