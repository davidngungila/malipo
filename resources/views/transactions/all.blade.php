@extends('layouts.app')

@section('title', 'All Transactions - MUSARIS System')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><span class="text-gray-400">Collection (Payments)</span></li>
                <li><span class="text-gray-400">/</span></li>
                <li><span class="text-gray-700 font-medium">All Transactions</span></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">All Transactions</h1>
                <p class="text-gray-600 mt-1">View and manage all payment transactions</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Export Report
                </button>
            </div>
        </div>
    </div>

                    <!-- Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select class="w-full border border-gray-300 rounded-lg px-3 py-2" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="SUCCESS">Success</option>
                                <option value="PROCESSING">Processing</option>
                                <option value="PENDING">Pending</option>
                                <option value="FAILED">Failed</option>
                            </select>
                        </div>

                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                            <select class="w-full border border-gray-300 rounded-lg px-3 py-2" id="typeFilter">
                                <option value="">All Types</option>
                                <option value="USSD_PUSH">USSD Push</option>
                                <option value="CARD">Card Payment</option>
                                <option value="PAYOUT">Payout</option>
                                <option value="TRANSFER">Transfer</option>
                            </select>
                        </div>

                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                            <div class="flex space-x-2">
                                <input type="date" class="flex-1 border border-gray-300 rounded-lg px-3 py-2" id="startDate" placeholder="Start Date">
                                <input type="date" class="flex-1 border border-gray-300 rounded-lg px-3 py-2" id="endDate" placeholder="End Date">
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount Range</label>
                            <div class="flex space-x-2">
                                <input type="number" class="flex-1 border border-gray-300 rounded-lg px-3 py-2" id="minAmount" placeholder="Min" min="0">
                                <input type="number" class="flex-1 border border-gray-300 rounded-lg px-3 py-2" id="maxAmount" placeholder="Max" min="0">
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-chart-line text-green-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Total Transactions</h3>
                                    <p class="text-2xl font-bold text-green-600" id="totalTransactions">0</p>
                                    <p class="text-sm text-gray-500">All time</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Total Volume</h3>
                                    <p class="text-2xl font-bold text-blue-600" id="totalVolume">TZS 0</p>
                                    <p class="text-sm text-gray-500">All time</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Success Rate</h3>
                                    <p class="text-2xl font-bold text-purple-600" id="successRate">0%</p>
                                    <p class="text-sm text-gray-500">Last 30 days</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex items-center">
                                <div class="p-3 bg-orange-100 rounded-full">
                                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Avg Processing</h3>
                                    <p class="text-2xl font-bold text-orange-600" id="avgProcessing">0s</p>
                                    <p class="text-sm text-gray-500">Last 24 hours</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transactions Table -->
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <input type="checkbox" class="rounded border-gray-300" id="selectAll">
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="transactionsTable">
                                    <!-- Transactions will be loaded here -->
                                    <tr>
                                        <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                            <div class="flex justify-center">
                                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
                                                <p class="ml-3">Loading transactions...</p>
                                            </div>
                                        </td>
                                    </tr>
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

    <script>
        let currentPage = 1;
        let isLoading = false;

        // Load transactions on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadTransactions();
            loadStatistics();
        });

        // Load transactions from API
        function loadTransactions() {
            if (isLoading) return;
            isLoading = true;

            const params = new URLSearchParams();
            params.append('page', currentPage);
            
            // Add filters
            const status = document.getElementById('statusFilter').value;
            const type = document.getElementById('typeFilter').value;
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const minAmount = document.getElementById('minAmount').value;
            const maxAmount = document.getElementById('maxAmount').value;

            if (status) params.append('status', status);
            if (type) params.append('type', type);
            if (startDate) params.append('startDate', startDate);
            if (endDate) params.append('endDate', endDate);
            if (minAmount) params.append('minAmount', minAmount);
            if (maxAmount) params.append('maxAmount', maxAmount);

            fetch('/advanced/payment-history?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderTransactions(data.data);
                        updatePagination(data.pagination);
                    } else {
                        console.error('Failed to load transactions:', data.message);
                    }
                })
                .catch(error => console.error('Error loading transactions:', error))
                .finally(() => {
                    isLoading = false;
                });
        }

        // Load statistics
        function loadStatistics() {
            // Simulate statistics (replace with real API call)
            document.getElementById('totalTransactions').textContent = '1,234';
            document.getElementById('totalVolume').textContent = 'TZS 2,456,789';
            document.getElementById('successRate').textContent = '94.5%';
            document.getElementById('avgProcessing').textContent = '1.2s';
        }

        // Render transactions table
        function renderTransactions(transactions) {
            const tbody = document.getElementById('transactionsTable');
            
            if (!transactions || transactions.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3"></i>
                            <p>No transactions found</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = transactions.map(transaction => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" class="rounded border-gray-300" value="${transaction.id}">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${transaction.date}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${transaction.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getTypeClass(transaction.type)}">
                            ${transaction.type}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${transaction.customer}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${transaction.phone}</td>
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

        // Get type class for styling
        function getTypeClass(type) {
            const classes = {
                'USSD_PUSH': 'bg-green-100 text-green-800',
                'CARD': 'bg-blue-100 text-blue-800',
                'PAYOUT': 'bg-purple-100 text-purple-800',
                'TRANSFER': 'bg-orange-100 text-orange-800'
            };
            return classes[type] || 'bg-gray-100 text-gray-800';
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

        // View transaction details
        function viewTransaction(id) {
            // Open transaction details modal or navigate to detail page
            console.log('View transaction:', id);
        }

        // Update pagination
        function updatePagination(pagination) {
            if (!pagination) return;
            
            document.querySelector('.text-gray-700').innerHTML = 
                `Showing <span class="font-medium">${pagination.from}</span> to <span class="font-medium">${pagination.to}</span> of <span class="font-medium">${pagination.total}</span> results`;
            
            document.getElementById('prevPage').disabled = pagination.current_page === 1;
            document.getElementById('nextPage').disabled = pagination.current_page === pagination.last_page;
        }

        // Event listeners for filters
        document.getElementById('statusFilter').addEventListener('change', loadTransactions);
        document.getElementById('typeFilter').addEventListener('change', loadTransactions);
        document.getElementById('startDate').addEventListener('change', loadTransactions);
        document.getElementById('endDate').addEventListener('change', loadTransactions);
        document.getElementById('minAmount').addEventListener('change', loadTransactions);
        document.getElementById('maxAmount').addEventListener('change', loadTransactions);

        // Pagination event listeners
        document.getElementById('prevPage').addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                loadTransactions();
            }
        });

        document.getElementById('nextPage').addEventListener('click', function() {
            currentPage++;
            loadTransactions();
        });

        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>
@endsection
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">All Transactions</h1>
                <p class="text-gray-600 mt-1">Complete overview of all system transactions</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Export Transactions
                </button>
            </div>
        </div>
    </div>

    <!-- Transaction Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <h3 class="text-sm font-medium text-gray-500">Today's Volume</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 2.1M</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <h3 class="text-sm font-medium text-gray-500">This Week</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 8.7M</p>
            <p class="text-xs text-blue-600 mt-1">1,423 transactions</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <h3 class="text-sm font-medium text-gray-500">This Month</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 45.2M</p>
            <p class="text-xs text-yellow-600 mt-1">12,543 transactions</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <h3 class="text-sm font-medium text-gray-500">Success Rate</h3>
            <p class="text-2xl font-bold text-gray-900">98.7%</p>
            <p class="text-xs text-purple-600 mt-1">164 failed</p>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" placeholder="Search transactions..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <option>All Status</option>
                <option>Completed</option>
                <option>Pending</option>
                <option>Failed</option>
                <option>Refunded</option>
            </select>
            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <option>All Methods</option>
                <option>USSD</option>
                <option>Card</option>
                <option>Mobile Money</option>
                <option>Bank Transfer</option>
            </select>
            <input type="date" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Transaction History</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Showing 1-10 of 12,543</span>
                    <button class="text-green-600 hover:text-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#TXN001</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Payment</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">John Doe</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 5,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">USSD</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-16 11:23</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#TXN002</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Payout</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Jane Smith</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 25,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Bank Transfer</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-16 10:45</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#TXN003</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Refund</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Mike Johnson</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS 8,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Card</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-16 09:30</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
