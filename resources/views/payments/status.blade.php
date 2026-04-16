@extends('layouts.app')

@section('title', 'Payment Status - FEEDTAN System')

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
                <li><span class="text-gray-700 font-medium">Payment Status</span></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Payment Status Monitoring</h1>
                <p class="text-gray-600 mt-1">Real-time payment transaction status and analytics</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-green-600 font-medium">API Active</span>
                </div>
                <button onclick="exportStatusReport()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-file-pdf mr-2"></i>Export Report
                </button>
            </div>
        </div>
    </div>

    @php
        // Load payment data for statistics
        $payments = [];
        try {
            require_once public_path('config.php');
            require_once public_path('ClickPesaAPI.php');
            
            $config = include(public_path('config.php'));
            $api = new \ClickPesaAPI($config);
            
            // Get recent payments for status analysis
            $response = $api->queryAllPayments(['limit' => 100]);
            if (isset($response['data']) && is_array($response['data'])) {
                $payments = $response['data'];
            }
        } catch (Exception $e) {
            // Handle API error gracefully
            $payments = [];
        }
        
        // Calculate statistics
        $completedCount = 0;
        $pendingCount = 0;
        $failedCount = 0;
        $todayVolume = 0;
        $successCount = 0;
        
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        
        foreach ($payments as $payment) {
            $status = $payment['status'] ?? '';
            $amount = $payment['collectedAmount'] ?? 0;
            $createdAt = isset($payment['createdAt']) ? new DateTime($payment['createdAt']) : null;
            
            // Count by status
            if ($status === 'SUCCESS' || $status === 'SETTLED') {
                $completedCount++;
                $successCount++;
            } elseif ($status === 'PROCESSING' || $status === 'PENDING') {
                $pendingCount++;
            } elseif ($status === 'FAILED') {
                $failedCount++;
            }
            
            // Calculate today's volume
            if ($createdAt && $createdAt >= $today) {
                $todayVolume += $amount;
            }
        }
        
        // Format volume
        $volumeFormatted = $todayVolume >= 1000000 ? number_format($todayVolume / 1000000, 1) . 'M' : number_format($todayVolume / 1000, 1) . 'K';
    @endphp

    <!-- Status Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-gray-500">Completed</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $completedCount }}</p>
                    <p class="text-xs text-green-600 mt-1">Total</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-gray-500">Pending</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</p>
                    <p class="text-xs text-yellow-600 mt-1">Processing</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-gray-500">Failed</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $failedCount }}</p>
                    <p class="text-xs text-red-600 mt-1">Total</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-chart-line text-purple-500 text-2xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-gray-500">Today's Volume</h3>
                    <p class="text-2xl font-bold text-gray-900">TZS {{ $volumeFormatted }}</p>
                    <p class="text-xs text-purple-600 mt-1">{{ count($payments) }} transactions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Check Form -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Check Payment Status</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Search and filter payment transactions</span>
                </div>
            </div>
        </div>
        <div class="p-6">
            <form id="statusForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Search Options -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Search Options</h3>
                        
                        <div>
                            <label for="transactionId" class="block text-sm font-medium text-gray-700 mb-2">Transaction ID</label>
                            <div class="relative">
                                <input type="text" id="transactionId" name="transactionId" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                       placeholder="Enter transaction ID (e.g., FEEDTAN123456789)">
                                <button type="button" onclick="clearTransactionId()" 
                                        class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="phoneNumber" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <div class="relative">
                                <input type="tel" id="phoneNumber" name="phoneNumber" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                       placeholder="255712345678">
                                <button type="button" onclick="clearPhoneNumber()" 
                                        class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Format: 255712345678 (Tanzania numbers only)</p>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <div class="relative">
                                <input type="email" id="email" name="email" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                       placeholder="customer@example.com">
                                <button type="button" onclick="clearEmail()" 
                                        class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="dateRange" class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                            <div class="flex space-x-2">
                                <input type="date" id="startDate" name="startDate" 
                                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                       placeholder="Start Date">
                                <input type="date" id="endDate" name="endDate" 
                                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                       placeholder="End Date">
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" onclick="searchByStatus('SUCCESS')" 
                                    class="bg-green-100 text-green-800 px-4 py-3 rounded-lg hover:bg-green-200 transition-colors">
                                <i class="fas fa-check-circle mr-2"></i>Success Payments
                            </button>
                            
                            <button type="button" onclick="searchByStatus('FAILED')" 
                                    class="bg-red-100 text-red-800 px-4 py-3 rounded-lg hover:bg-red-200 transition-colors">
                                <i class="fas fa-times-circle mr-2"></i>Failed Payments
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" onclick="searchByDateRange('today')" 
                                    class="bg-blue-100 text-blue-800 px-4 py-3 rounded-lg hover:bg-blue-200 transition-colors">
                                <i class="fas fa-calendar-day mr-2"></i>Today
                            </button>
                            
                            <button type="button" onclick="searchByDateRange('week')" 
                                    class="bg-purple-100 text-purple-800 px-4 py-3 rounded-lg hover:bg-purple-200 transition-colors">
                                <i class="fas fa-calendar-week mr-2"></i>This Week
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search Button -->
                <div class="flex justify-center">
                    <button type="submit" form="statusForm" 
                            class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-search mr-2"></i>Check Payment Status
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Results -->
    <div id="statusResults" class="hidden">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Search Results</h3>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm text-gray-600">Found <span id="resultCount">0</span> transactions</span>
                <div class="flex space-x-2">
                    <button onclick="exportResults()" class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-download mr-1"></i>Export
                    </button>
                    <button onclick="refreshResults()" class="text-green-600 hover:text-green-800 text-sm">
                        <i class="fas fa-sync mr-1"></i>Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Results Table -->
                        <div class="bg-white shadow rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="resultsTable">
                                        <!-- Results will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="flex items-center justify-between mt-6">
                            <div class="text-sm text-gray-700">
                                Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">0</span> results
                            </div>
                            <div class="flex items-center space-x-2">
                                <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg bg-white text-gray-700 hover:bg-gray-50" id="prevPage">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg bg-white text-gray-700 hover:bg-gray-50" id="nextPage">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let isLoading = false;

        function clearTransactionId() {
            document.getElementById('transactionId').value = '';
        }

        function clearPhoneNumber() {
            document.getElementById('phoneNumber').value = '';
        }

        function clearEmail() {
            document.getElementById('email').value = '';
        }

        function searchByStatus(status) {
            document.getElementById('transactionId').value = '';
            document.getElementById('phoneNumber').value = '';
            document.getElementById('email').value = '';
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            
            // Add hidden status filter
            const statusFilter = document.createElement('input');
            statusFilter.type = 'hidden';
            statusFilter.name = 'status';
            statusFilter.value = status;
            document.getElementById('statusForm').appendChild(statusFilter);
            
            document.getElementById('statusForm').submit();
        }

        function searchByDateRange(range) {
            const today = new Date();
            let startDate, endDate;

            switch(range) {
                case 'today':
                    startDate = today.toISOString().split('T')[0];
                    endDate = today.toISOString().split('T')[0];
                    break;
                case 'week':
                    const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                    startDate = weekAgo.toISOString().split('T')[0];
                    endDate = today.toISOString().split('T')[0];
                    break;
            }

            document.getElementById('transactionId').value = '';
            document.getElementById('phoneNumber').value = '';
            document.getElementById('email').value = '';
            document.getElementById('startDate').value = startDate;
            document.getElementById('endDate').value = endDate;
            
            document.getElementById('statusForm').submit();
        }

        function exportResults() {
            // Export functionality
            console.log('Exporting results...');
        }

        function refreshResults() {
            // Refresh results
            console.log('Refreshing results...');
            document.getElementById('statusForm').submit();
        }

        // Form submission
        document.getElementById('statusForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (isLoading) return;
            isLoading = true;

            const formData = new FormData(e.target);
            
            // Show loading state
            const resultsDiv = document.getElementById('statusResults');
            const resultsTable = document.getElementById('resultsTable');
            const resultCount = document.getElementById('resultCount');
            
            resultsDiv.classList.remove('hidden');
            resultsTable.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <div class="flex justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
                            <p class="ml-3">Searching payment status...</p>
                        </div>
                    </td>
                </tr>
            `;
            resultCount.textContent = '0';

            // Send search request to API
            fetch('/api/check-payment-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderResults(data.data);
                    updatePagination(data.pagination);
                    resultCount.textContent = data.pagination ? data.pagination.total : '0';
                } else {
                    resultsTable.innerHTML = `
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex items-center">
                                    <div class="p-2 bg-red-100 rounded-full">
                                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-red-800 font-semibold">Search Failed</h4>
                                        <p class="text-sm text-gray-600">${data.message || 'Failed to search payment status'}</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                resultsTable.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex items-center">
                                <div class="p-2 bg-red-100 rounded-full">
                                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-red-800 font-semibold">Error</h4>
                                    <p class="text-sm text-gray-600">Failed to search: ${error.message}</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
            })
            .finally(() => {
                isLoading = false;
            });
        });

        function renderResults(results) {
            const tbody = document.getElementById('resultsTable');
            
            if (!results || results.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3"></i>
                            <p>No payment transactions found</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = results.map(transaction => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${transaction.date}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${transaction.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getTypeClass(transaction.type)}">
                            ${transaction.type}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${transaction.amount}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusClass(transaction.status)}">
                            ${transaction.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <button class="text-blue-600 hover:text-blue-800 font-medium" onclick="viewTransaction('${transaction.id}')">
                            <i class="fas fa-eye mr-1"></i>View
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function getTypeClass(type) {
            const classes = {
                'USSD_PUSH': 'bg-green-100 text-green-800',
                'CARD': 'bg-blue-100 text-blue-800',
                'PAYOUT': 'bg-purple-100 text-purple-800',
                'TRANSFER': 'bg-orange-100 text-orange-800'
            };
            return classes[type] || 'bg-gray-100 text-gray-800';
        }

        function getStatusClass(status) {
            const classes = {
                'SUCCESS': 'bg-green-100 text-green-800',
                'PROCESSING': 'bg-yellow-100 text-yellow-800',
                'PENDING': 'bg-orange-100 text-orange-800',
                'FAILED': 'bg-red-100 text-red-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        }

        function viewTransaction(id) {
            // Navigate to transaction details page
            window.location.href = `/payments/transaction/${id}`;
        }

        function exportStatusReport() {
            const reportContent = `
FEEDTAN PAYMENT SYSTEM - STATUS REPORT
============================================
Generated on: ${new Date().toLocaleString()}
Report Type: Payment Status Analysis

STATUS SUMMARY:
===============
Completed Payments: {{ $completedCount }}
Pending Payments: {{ $pendingCount }}
Failed Payments: {{ $failedCount }}
Today's Volume: TZS {{ $volumeFormatted }}
Total Transactions Analyzed: {{ count($payments) }}

SUCCESS RATE:
=============
${count($payments) > 0 ? round(($completedCount / count($payments)) * 100, 1) : 0}% completion rate
${count($payments) > 0 ? round(($failedCount / count($payments)) * 100, 1) : 0}% failure rate

DETAILED TRANSACTIONS:
======================
${generateStatusTransactionList()}
============================================
This is an auto-generated status report from FEEDTAN Payment System
            `;
            
            // Create blob and download
            const blob = new Blob([reportContent], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `status_report_${new Date().toISOString().split('T')[0]}.txt`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
            
            showNotification('Status report exported successfully!', 'success');
        }

        function generateStatusTransactionList() {
            let text = '';
            @if ($payments && is_array($payments))
                @foreach (array_slice($payments, 0, 50) as $payment)
                    text += "ID: {{ substr($payment['id'] ?? 'N/A', 0, 12) }}... | Status: {{ $payment['status'] ?? 'N/A' }} | Amount: {{ number_format($payment['collectedAmount'] ?? 0) }} {{ $payment['collectedCurrency'] ?? 'TZS' }} | Date: {{ isset($payment['createdAt']) ? date('Y-m-d H:i', strtotime($payment['createdAt'])) : 'N/A' }}\n";
                @endforeach
            @endif
            return text;
        }

        function showNotification(message, type = 'success') {
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
            
            setTimeout(() => {
                notification.classList.add('translate-x-0');
            }, 100);
            
            setTimeout(() => {
                notification.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        function updatePagination(pagination) {
            if (!pagination) return;
            
            document.querySelector('.text-gray-700').innerHTML = 
                `Showing <span class="font-medium">${pagination.from || 1}</span> to <span class="font-medium">${pagination.to || 10}</span> of <span class="font-medium">${pagination.total || 0}</span> results`;
            
            document.getElementById('prevPage').disabled = pagination.current_page === 1;
            document.getElementById('nextPage').disabled = pagination.current_page === pagination.last_page;
        }

        // Pagination event listeners
        document.getElementById('prevPage').addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                document.getElementById('statusForm').submit();
            }
        });

        document.getElementById('nextPage').addEventListener('click', function() {
            currentPage++;
            document.getElementById('statusForm').submit();
        });
    </script>
@endsection