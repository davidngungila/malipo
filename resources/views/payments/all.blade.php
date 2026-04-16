@extends('layouts.app')

@section('title', 'All Payments - MUSARIS System')

@push('styles')
<style>
.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.status-success, .status-settled {
    background-color: #10b981;
    color: white;
}

.status-processing, .status-pending {
    background-color: #f59e0b;
    color: white;
}

.status-failed {
    background-color: #ef4444;
    color: white;
}

.status-unknown {
    background-color: #6b7280;
    color: white;
}

.payment-details-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.payment-details-content {
    background: white;
    border-radius: 0.5rem;
    padding: 2rem;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
}

.receipt-section {
    background: #f9fafb;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-top: 1rem;
}

.btn-group {
    display: inline-flex;
    vertical-align: middle;
}

.btn-group > .btn {
    position: relative;
    flex: 1 1 auto;
}

.btn-group > .btn:not(:first-child) {
    margin-left: -1px;
}

.btn {
    display: inline-block;
    font-weight: 400;
    color: #212529;
    text-align: center;
    vertical-align: middle;
    cursor: pointer;
    user-select: none;
    background-color: transparent;
    border: 1px solid transparent;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.25rem;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}

.btn-outline-primary {
    color: #0d6efd;
    border-color: #0d6efd;
}

.btn-outline-primary:hover {
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-outline-info {
    color: #0dcaf0;
    border-color: #0dcaf0;
}

.btn-outline-info:hover {
    color: #fff;
    background-color: #0dcaf0;
    border-color: #0dcaf0;
}

.btn-outline-success {
    color: #198754;
    border-color: #198754;
}

.btn-outline-success:hover {
    color: #fff;
    background-color: #198754;
    border-color: #198754;
}

.btn-primary {
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-primary:hover {
    color: #fff;
    background-color: #0b5ed7;
    border-color: #0a58ca;
}
</style>
@endpush

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
                <li><span class="text-gray-700 font-medium">All Payments</span></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">All Payments</h1>
                <p class="text-gray-600 mt-1">View and manage all payment transactions</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <button onclick="exportPDFReport()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </button>
                <button onclick="exportCSVReport()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-file-csv mr-2"></i>Export CSV
                </button>
            </div>
        </div>
    </div>

    <!-- Payment Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        @php
            // Calculate statistics from payment data
            $todayTotal = 0;
            $todayCount = 0;
            $weekTotal = 0;
            $weekCount = 0;
            $monthTotal = 0;
            $monthCount = 0;
            $successCount = 0;
            $failedCount = 0;
            
            if ($payments && is_array($payments)) {
                $now = new DateTime();
                $today = new DateTime($now->format('Y-m-d'));
                $weekStart = new DateTime($now->format('Y-m-d'));
                $weekStart->modify('monday this week');
                $monthStart = new DateTime($now->format('Y-m-01'));
                
                foreach ($payments as $payment) {
                    $amount = $payment['collectedAmount'] ?? 0;
                    $status = $payment['status'] ?? '';
                    $createdAt = isset($payment['createdAt']) ? new DateTime($payment['createdAt']) : null;
                    
                    if ($createdAt) {
                        // Today's stats
                        if ($createdAt >= $today) {
                            $todayTotal += $amount;
                            $todayCount++;
                        }
                        
                        // This week stats
                        if ($createdAt >= $weekStart) {
                            $weekTotal += $amount;
                            $weekCount++;
                        }
                        
                        // This month stats
                        if ($createdAt >= $monthStart) {
                            $monthTotal += $amount;
                            $monthCount++;
                        }
                    }
                    
                    // Success/Failed stats
                    if ($status === 'SUCCESS' || $status === 'SETTLED') {
                        $successCount++;
                    } elseif ($status === 'FAILED') {
                        $failedCount++;
                    }
                }
            }
            
            // Calculate success rate
            $totalCount = $successCount + $failedCount;
            $successRate = $totalCount > 0 ? round(($successCount / $totalCount) * 100, 1) : 0;
            
            // Format amounts
            $todayFormatted = $todayTotal >= 1000000 ? number_format($todayTotal / 1000000, 1) . 'M' : number_format($todayTotal / 1000, 1) . 'K';
            $weekFormatted = $weekTotal >= 1000000 ? number_format($weekTotal / 1000000, 1) . 'M' : number_format($weekTotal / 1000, 1) . 'K';
            $monthFormatted = $monthTotal >= 1000000 ? number_format($monthTotal / 1000000, 1) . 'M' : number_format($monthTotal / 1000, 1) . 'K';
        @endphp
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <h3 class="text-sm font-medium text-gray-500">Today's Payments</h3>
            <p class="text-2xl font-bold text-gray-900">TZS {{ $todayFormatted }}</p>
            <p class="text-xs text-green-600 mt-1">{{ $todayCount }} transactions</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <h3 class="text-sm font-medium text-gray-500">This Week</h3>
            <p class="text-2xl font-bold text-gray-900">TZS {{ $weekFormatted }}</p>
            <p class="text-xs text-blue-600 mt-1">{{ $weekCount }} transactions</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <h3 class="text-sm font-medium text-gray-500">This Month</h3>
            <p class="text-2xl font-bold text-gray-900">TZS {{ $monthFormatted }}</p>
            <p class="text-xs text-yellow-600 mt-1">{{ $monthCount }} transactions</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <h3 class="text-sm font-medium text-gray-500">Success Rate</h3>
            <p class="text-2xl font-bold text-gray-900">{{ $successRate }}%</p>
            <p class="text-xs text-purple-600 mt-1">{{ $failedCount }} failed</p>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" placeholder="Search payments..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
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

    <!-- Success/Error Messages -->
    @if ($success)
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-full">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="text-green-800 font-medium">Success</h4>
                    <p class="text-sm text-gray-600">{{ $success }}</p>
                </div>
            </div>
        </div>
    @endif

    @if ($error)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-full">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="text-red-800 font-medium">Error</h4>
                    <p class="text-sm text-gray-600">{{ $error }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Payments Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Payment Transactions</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">
                        @if($payments && count($payments) > 0)
                            Showing {{ ($currentPage - 1) * $limit + 1 }}-{{ min($currentPage * $limit, $totalCount) }} of {{ $totalCount }}
                        @else
                            No payments found
                        @endif
                    </span>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if($payments && count($payments) > 0)
                        @foreach($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <code>{{ substr($payment['id'] ?? 'N/A', 0, 12) }}...</code>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <strong>{{ $payment['orderReference'] ?? 'N/A' }}</strong>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($payment['collectedAmount'] ?? 0) }} {{ $payment['collectedCurrency'] ?? 'TZS' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @php
                                        if (isset($payment['paymentPhoneNumber'])) {
                                            echo $payment['paymentPhoneNumber'];
                                        } elseif (isset($payment['customer']['customerPhoneNumber'])) {
                                            echo $payment['customer']['customerPhoneNumber'];
                                        } else {
                                            echo '<span class="text-muted">N/A</span>';
                                        }
                                    @endphp
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $status = $payment['status'] ?? 'unknown';
                                        $statusClass = '';
                                        if ($status === 'SUCCESS' || $status === 'SETTLED') {
                                            $statusClass = 'status-success';
                                        } elseif ($status === 'PROCESSING' || $status === 'PENDING') {
                                            $statusClass = 'status-processing';
                                        } elseif ($status === 'FAILED') {
                                            $statusClass = 'status-failed';
                                        } else {
                                            $statusClass = 'status-unknown';
                                        }
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ isset($payment['createdAt']) ? date('Y-m-d H:i', strtotime($payment['createdAt'])) : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('payments.transaction', ['reference' => $payment['orderReference'] ?? '']) }}" 
                   class="btn btn-sm btn-primary" 
                   title="View Transaction">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                        <a href="/payments/status?reference={{ urlencode($payment['orderReference'] ?? '') }}" 
                                               class="btn btn-sm btn-outline-primary" title="Check Status">
                                            <i class="fas fa-search"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-info" 
                                                onclick="showPaymentDetails('{{ htmlspecialchars(json_encode($payment)) }}')" 
                                                title="View Details">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" 
                                                onclick="viewReceipt('{{ $payment['id'] ?? '' }}', '{{ $payment['orderReference'] ?? '' }}')" 
                                                title="View Receipt">
                                            <i class="fas fa-receipt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No payment records found</h3>
                                    <p class="text-gray-500">Try adjusting your search criteria or filters</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($payments && count($payments) > 0 && $totalCount > $limit)
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing {{ ($currentPage - 1) * $limit + 1 }} to {{ min($currentPage * $limit, $totalCount) }} of {{ $totalCount }} results
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- Previous Button -->
                        @if($currentPage > 1)
                            <a href="{{ url('/payments') }}?page={{ $currentPage - 1 }}" 
                               class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Previous
                            </a>
                        @else
                            <button disabled class="px-3 py-2 text-sm bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed opacity-50">
                                Previous
                            </button>
                        @endif
                        
                        <!-- Page Numbers -->
                        @php
                            $totalPages = ceil($totalCount / $limit);
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);
                        @endphp
                        
                        @if($startPage > 1)
                            <a href="{{ url('/payments') }}?page=1" 
                               class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50">1</a>
                            @if($startPage > 2)
                                <span class="px-3 py-2 text-sm text-gray-500">...</span>
                            @endif
                        @endif
                        
                        @for($i = $startPage; $i <= $endPage; $i++)
                            @if($i == $currentPage)
                                <span class="px-3 py-2 text-sm bg-green-600 text-white border border-green-600 rounded-md">{{ $i }}</span>
                            @else
                                <a href="{{ url('/payments') }}?page={{ $i }}" 
                                   class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50">{{ $i }}</a>
                            @endif
                        @endfor
                        
                        @if($endPage < $totalPages)
                            @if($endPage < $totalPages - 1)
                                <span class="px-3 py-2 text-sm text-gray-500">...</span>
                            @endif
                            <a href="{{ url('/payments') }}?page={{ $totalPages }}" 
                                   class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50">{{ $totalPages }}</a>
                        @endif
                        
                        <!-- Next Button -->
                        @if($currentPage < $totalPages)
                            <a href="{{ url('/payments') }}?page={{ $currentPage + 1 }}" 
                               class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Next
                            </a>
                        @else
                            <button disabled class="px-3 py-2 text-sm bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed opacity-50">
                                Next
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

    <script>
        function viewPayment(paymentId) {
            // Open payment details modal or navigate to detail page
            window.open(`/payments/${paymentId}`, '_blank');
        }
        
        function downloadReceipt(paymentId) {
            // Generate and download receipt
            window.open(`/payments/${paymentId}/receipt`, '_blank');
        }
        
        // Add search and filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[placeholder="Search payments..."]');
            const statusSelect = document.querySelector('select option[value="Completed"]').parentElement;
            const methodSelect = document.querySelector('select option[value="USSD"]').parentElement;
            const dateInput = document.querySelector('input[type="date"]');
            
            function applyFilters() {
                const params = new URLSearchParams(window.location.search);
                
                if (searchInput && searchInput.value) {
                    params.set('search', searchInput.value);
                } else {
                    params.delete('search');
                }
                
                if (statusSelect && statusSelect.value !== 'All Status') {
                    params.set('status', statusSelect.value);
                } else {
                    params.delete('status');
                }
                
                if (methodSelect && methodSelect.value !== 'All Methods') {
                    params.set('channel', methodSelect.value);
                } else {
                    params.delete('channel');
                }
                
                if (dateInput && dateInput.value) {
                    params.set('start_date', dateInput.value);
                } else {
                    params.delete('start_date');
                }
                
                // Reset to page 1 when applying new filters
                params.delete('page');
                
                window.location.href = '/payments?' + params.toString();
            }
            
            // Add event listeners
            if (searchInput) searchInput.addEventListener('input', applyFilters);
            if (statusSelect) statusSelect.addEventListener('change', applyFilters);
            if (methodSelect) methodSelect.addEventListener('change', applyFilters);
            if (dateInput) dateInput.addEventListener('change', applyFilters);
        });
    </script>
@endsection

@push('scripts')
<script>
let currentPaymentData = null;

function showPaymentDetails(paymentData) {
    try {
        currentPaymentData = JSON.parse(paymentData);
    } catch (e) {
        currentPaymentData = paymentData;
    }
    
    // Create modal HTML
    const modalHtml = `
        <div id="paymentDetailsModal" class="payment-details-modal">
            <div class="payment-details-content">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Payment Details</h2>
                    <button onclick="closePaymentDetails()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Transaction ID</label>
                            <p class="text-sm text-gray-900">${currentPaymentData.id || 'N/A'}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Order Reference</label>
                            <p class="text-sm text-gray-900">${currentPaymentData.orderReference || 'N/A'}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Amount</label>
                            <p class="text-sm text-gray-900">${currentPaymentData.collectedAmount ? number_format(currentPaymentData.collectedAmount) : '0'} ${currentPaymentData.collectedCurrency || 'TZS'}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <p class="text-sm">
                                <span class="status-badge status-${(currentPaymentData.status || '').toLowerCase()}">${currentPaymentData.status || 'Unknown'}</span>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Phone Number</label>
                            <p class="text-sm text-gray-900">${currentPaymentData.paymentPhoneNumber || (currentPaymentData.customer?.customerPhoneNumber) || 'N/A'}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Customer Email</label>
                            <p class="text-sm text-gray-900">${currentPaymentData.customer?.customerEmail || 'N/A'}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Created Date</label>
                            <p class="text-sm text-gray-900">${currentPaymentData.createdAt ? new Date(currentPaymentData.createdAt).toLocaleString() : 'N/A'}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Updated Date</label>
                            <p class="text-sm text-gray-900">${currentPaymentData.updatedAt ? new Date(currentPaymentData.updatedAt).toLocaleString() : 'N/A'}</p>
                        </div>
                    </div>
                    
                    ${currentPaymentData.message ? `
                    <div>
                        <label class="text-sm font-medium text-gray-500">Message</label>
                        <p class="text-sm text-gray-900">${currentPaymentData.message}</p>
                    </div>
                    ` : ''}
                    
                    ${currentPaymentData.exchanged && currentPaymentData.exchange ? `
                    <div class="receipt-section">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Exchange Details</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Source Currency</label>
                                <p class="text-sm text-gray-900">${currentPaymentData.exchange.sourceCurrency}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Target Currency</label>
                                <p class="text-sm text-gray-900">${currentPaymentData.exchange.targetCurrency}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Source Amount</label>
                                <p class="text-sm text-gray-900">${currentPaymentData.exchange.sourceAmount}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Exchange Rate</label>
                                <p class="text-sm text-gray-900">${currentPaymentData.exchange.rate}</p>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                    
                    <div class="flex justify-end space-x-3">
                        <button onclick="printReceipt()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-print mr-2"></i>Print Receipt
                        </button>
                        <button onclick="downloadReceipt()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-download mr-2"></i>Download Receipt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function closePaymentDetails() {
    const modal = document.getElementById('paymentDetailsModal');
    if (modal) {
        modal.remove();
    }
    currentPaymentData = null;
}

function viewPayment(paymentId, orderReference) {
    // Find the payment data from the current page
    const paymentRows = document.querySelectorAll('tbody tr');
    let paymentData = null;
    
    paymentRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length > 0) {
            const refElement = cells[1].querySelector('strong');
            if (refElement && refElement.textContent === orderReference) {
                // Extract payment data from the row
                paymentData = {
                    id: paymentId,
                    orderReference: orderReference,
                    amount: cells[2].textContent,
                    phone: cells[3].textContent,
                    status: cells[4].textContent,
                    date: cells[5].textContent
                };
            }
        }
    });
    
    if (paymentData) {
        // Show a simple view modal with payment summary
        showPaymentViewModal(paymentData);
    } else {
        alert('Payment data not found');
    }
}

function viewReceipt(paymentId, orderReference) {
    // Find the payment data from the current page
    const paymentRows = document.querySelectorAll('tbody tr');
    let paymentData = null;
    
    paymentRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length > 0) {
            const refElement = cells[1].querySelector('strong');
            if (refElement && refElement.textContent === orderReference) {
                // Extract payment data from the row
                paymentData = {
                    id: paymentId,
                    orderReference: orderReference,
                    amount: cells[2].textContent,
                    status: cells[3].textContent,
                    phone: cells[4].textContent,
                    date: cells[5].textContent
                };
            }
        }
    });
    
    if (paymentData) {
        showReceiptModal(paymentData);
    } else {
        alert('Payment data not found');
    }
}

function showReceiptModal(paymentData) {
    const modalHtml = `
        <div id="receiptModal" class="payment-details-modal">
            <div class="payment-details-content">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Payment Receipt</h2>
                    <button onclick="closeReceiptModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="receipt-section">
                    <div class="text-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">MUSARIS PAYMENT SYSTEM</h3>
                        <p class="text-sm text-gray-600">ClickPesa Payment Receipt</p>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Receipt Number:</span>
                            <span class="text-sm font-medium">${paymentData.id}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Order Reference:</span>
                            <span class="text-sm font-medium">${paymentData.orderReference}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Amount:</span>
                            <span class="text-sm font-medium">${paymentData.amount}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Status:</span>
                            <span class="text-sm font-medium">${paymentData.status}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Phone Number:</span>
                            <span class="text-sm font-medium">${paymentData.phone}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Date:</span>
                            <span class="text-sm font-medium">${paymentData.date}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Payment Method:</span>
                            <span class="text-sm font-medium">Mobile Money (USSD)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Processed By:</span>
                            <span class="text-sm font-medium">ClickPesa API</span>
                        </div>
                    </div>
                    
                    <div class="text-center mt-6 text-sm text-gray-500">
                        <p>Thank you for using MUSARIS Payment System</p>
                        <p>This is a computer-generated receipt</p>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-4">
                    <button onclick="printReceipt()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                    <button onclick="downloadReceipt()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        <i class="fas fa-download mr-2"></i>Download
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function showPaymentViewModal(paymentData) {
    const modalHtml = `
        <div id="paymentViewModal" class="payment-details-modal">
            <div class="payment-details-content">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Payment View</h2>
                    <button onclick="closePaymentViewModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Transaction ID</label>
                            <p class="text-sm text-gray-900">${paymentData.id}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Order Reference</label>
                            <p class="text-sm text-gray-900">${paymentData.orderReference}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Amount</label>
                            <p class="text-sm text-gray-900">${paymentData.amount}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Phone Number</label>
                            <p class="text-sm text-gray-900">${paymentData.phone}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <p class="text-sm">
                                <span class="status-badge status-${paymentData.status.toLowerCase()}">${paymentData.status}</span>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Date</label>
                            <p class="text-sm text-gray-900">${paymentData.date}</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button onclick="showPaymentDetails('${JSON.stringify(currentPaymentData)}')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-info-circle mr-2"></i>View Details
                        </button>
                        <button onclick="viewReceipt('${paymentData.id}', '${paymentData.orderReference}')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-receipt mr-2"></i>View Receipt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function closePaymentViewModal() {
    const modal = document.getElementById('paymentViewModal');
    if (modal) {
        modal.remove();
    }
}

function closeReceiptModal() {
    const modal = document.getElementById('receiptModal');
    if (modal) {
        modal.remove();
    }
}

function printReceipt() {
    window.print();
}

function downloadReceipt() {
    // Create a simple text receipt
    const receiptContent = `
MUSARIS PAYMENT SYSTEM - PAYMENT RECEIPT
========================================
Receipt Number: ${currentPaymentData?.id || 'N/A'}
Order Reference: ${currentPaymentData?.orderReference || 'N/A'}
Amount: ${currentPaymentData?.collectedAmount ? number_format(currentPaymentData.collectedAmount) : '0'} ${currentPaymentData?.collectedCurrency || 'TZS'}
Status: ${currentPaymentData?.status || 'N/A'}
Phone Number: ${currentPaymentData?.paymentPhoneNumber || (currentPaymentData?.customer?.customerPhoneNumber) || 'N/A'}
Date: ${currentPaymentData?.createdAt ? new Date(currentPaymentData.createdAt).toLocaleString() : 'N/A'}

Payment Method: Mobile Money (USSD)
Processed By: ClickPesa API

Thank you for using MUSARIS Payment System
This is a computer-generated receipt
    `;
    
    // Create blob and download
    const blob = new Blob([receiptContent], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `receipt_${currentPaymentData?.orderReference || 'payment'}_${Date.now()}.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

function exportPDFReport() {
    const reportContent = generateReportContent();
    
    // Create a text-based report for now (can be enhanced with jsPDF library)
    const textContent = `
FEEDTAN PAYMENT SYSTEM - PAYMENT REPORT
============================================
Generated on: ${new Date().toLocaleString()}
Total Payments: {{ $payments ? count($payments) : 0 }}

PAYMENT STATISTICS:
==================
Today's Payments: TZS {{ $todayFormatted }} ({{ $todayCount }} transactions)
This Week: TZS {{ $weekFormatted }} ({{ $weekCount }} transactions)
This Month: TZS {{ $monthFormatted }} ({{ $monthCount }} transactions)
Success Rate: {{ $successRate }}% ({{ $failedCount }} failed)

TRANSACTION DETAILS:
===================
${generatePaymentListText()}
============================================
This is an auto-generated report from FEEDTAN Payment System
    `;
    
    // Create blob and download
    const blob = new Blob([textContent], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `payment_report_${new Date().toISOString().split('T')[0]}.txt`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
    
    showNotification('Payment report exported successfully!', 'success');
}

function exportCSVReport() {
    let csvContent = "Transaction ID,Order Reference,Amount,Currency,Status,Customer Name,Phone Number,Created Date\n";
    
    @if ($payments && is_array($payments))
        @foreach ($payments as $payment)
            csvContent += "{{ $payment['id'] ?? 'N/A' }},";
            csvContent += "{{ $payment['orderReference'] ?? 'N/A' }},";
            csvContent += "{{ $payment['collectedAmount'] ?? 0 }},";
            csvContent += "{{ $payment['collectedCurrency'] ?? 'TZS' }},";
            csvContent += "{{ $payment['status'] ?? 'N/A' }},";
            csvContent += "{{ $payment['customer']['customerName'] ?? 'N/A' }},";
            csvContent += "{{ $payment['paymentPhoneNumber'] ?? ($payment['customer']['customerPhoneNumber'] ?? 'N/A') }},";
            csvContent += "{{ isset($payment['createdAt']) ? date('Y-m-d H:i:s', strtotime($payment['createdAt'])) : 'N/A' }}";
            csvContent += "\n";
        @endforeach
    @endif
    
    // Create blob and download
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `payment_data_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
    
    showNotification('Payment data exported to CSV successfully!', 'success');
}

function generatePaymentListText() {
    let text = '';
    @if ($payments && is_array($payments))
        @foreach (array_slice($payments, 0, 50) as $payment)
            text += "ID: {{ substr($payment['id'] ?? 'N/A', 0, 12) }}... | Ref: {{ $payment['orderReference'] ?? 'N/A' }} | Amount: {{ number_format($payment['collectedAmount'] ?? 0) }} {{ $payment['collectedCurrency'] ?? 'TZS' }} | Status: {{ $payment['status'] ?? 'N/A' }} | Date: {{ isset($payment['createdAt']) ? date('Y-m-d H:i', strtotime($payment['createdAt'])) : 'N/A' }}\n";
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

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('payment-details-modal')) {
        event.target.remove();
        currentPaymentData = null;
    }
});
</script>
@endpush
