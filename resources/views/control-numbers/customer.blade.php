@extends('layouts.app')

@section('title', 'Customer Control Numbers - FEEDTAN System')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><a href="{{ route('control-numbers.index') }}" class="text-gray-500 hover:text-gray-700">Control Numbers</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li class="text-gray-900 font-medium">Customer Control Numbers</li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Customer Control Numbers</h1>
                <p class="text-gray-600 mt-1">Generate BillPay control numbers for specific customers</p>
                
                <!-- API Connectivity Status -->
                <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" id="apiStatusIndicator">
                    <i class="fas fa-spinner fa-spin mr-2" id="apiStatusIcon"></i>
                    <span id="apiStatusText">Checking API connection...</span>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('control-numbers.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Control Numbers
                </a>
                <button onclick="testAPIConnection()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                    <i class="fas fa-plug mr-2"></i>Test API
                </button>
            </div>
        </div>
    </div>

    <!-- Customer Control Number Form -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Create Customer Control Number</h3>
            <p class="text-sm text-gray-500 mt-1">Fill in the customer details to generate a BillPay control number</p>
        </div>
        <div class="p-6">
            <form id="customerControlForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Customer Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer Name *</label>
                        <input type="text" name="customerName" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter customer name">
                    </div>

                    <!-- Customer Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="customerPhone" placeholder="255712345678"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Tanzania format (255712345678)</p>
                    </div>

                    <!-- Customer Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="customerEmail" placeholder="customer@example.com"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Required if phone number not provided</p>
                    </div>

                    <!-- Bill Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bill Amount (TZS) *</label>
                        <input type="number" name="billAmount" required min="100" step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="10000">
                    </div>

                    <!-- Bill Reference -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bill Reference</label>
                        <input type="text" name="billReference" placeholder="CUSTOM123"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Custom reference (optional, system will auto-generate if not provided)</p>
                    </div>

                    <!-- Payment Mode -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Mode</label>
                        <select name="billPaymentMode" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="ALLOW_PARTIAL_AND_OVER_PAYMENT">Allow Partial & Over Payment</option>
                            <option value="EXACT">Exact Amount Only</option>
                        </select>
                    </div>
                </div>

                <!-- Bill Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bill Description *</label>
                    <textarea name="billDescription" required rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Enter bill description (e.g., Water Bill - July 2024)"></textarea>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="resetForm()" 
                            class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </button>
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Generate Control Number
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Generated Control Number Result -->
    <div id="resultSection" class="hidden">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Generated Control Number</h3>
            </div>
            <div class="p-6">
                <div id="resultContent">
                    <!-- Result will be displayed here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Customer Control Numbers -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Customer Control Numbers</h3>
        </div>
        <div class="p-6">
            <div id="recentCustomerNumbers" class="space-y-3">
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                    <p>Loading recent customer control numbers...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Test API connectivity on page load
    testAPIConnection();
    loadRecentCustomerNumbers();
    
    // Form submission
    document.getElementById('customerControlForm').addEventListener('submit', function(e) {
        e.preventDefault();
        generateCustomerControlNumber();
    });
    
    // Phone number formatting
    document.querySelector('input[name="customerPhone"]').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 0 && !value.startsWith('255')) {
            value = '255' + value;
        }
        e.target.value = value;
    });
});

function testAPIConnection() {
    const statusIndicator = document.getElementById('apiStatusIndicator');
    const statusIcon = document.getElementById('apiStatusIcon');
    const statusText = document.getElementById('apiStatusText');
    
    // Show loading state
    statusIndicator.className = 'mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800';
    statusIcon.className = 'fas fa-spinner fa-spin mr-2';
    statusText.textContent = 'Testing API connection...';
    
    fetch('/api/control-numbers/test-connection', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            statusIndicator.className = 'mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
            statusIcon.className = 'fas fa-check-circle mr-2';
            statusText.textContent = 'API Connected - Direct ClickPesa';
            showNotification('API connection successful', 'success');
        } else {
            statusIndicator.className = 'mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800';
            statusIcon.className = 'fas fa-exclamation-triangle mr-2';
            statusText.textContent = 'API Disconnected - ' + data.message;
            showNotification('API connection failed: ' + data.message, 'error');
        }
    })
    .catch(error => {
        statusIndicator.className = 'mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800';
        statusIcon.className = 'fas fa-exclamation-triangle mr-2';
        statusText.textContent = 'API Error - ' + error.message;
        showNotification('API connection error: ' + error.message, 'error');
    });
}

function generateCustomerControlNumber() {
    const formData = new FormData(document.getElementById('customerControlForm'));
    const data = Object.fromEntries(formData.entries());
    
    // Validate phone or email is provided
    if (!data.customerPhone && !data.customerEmail) {
        showNotification('Please provide either phone number or email address', 'error');
        return;
    }
    
    // Show loading state
    const resultSection = document.getElementById('resultSection');
    const resultContent = document.getElementById('resultContent');
    
    resultSection.classList.remove('hidden');
    resultContent.innerHTML = `
        <div class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            <p class="mt-2 text-gray-600">Generating control number...</p>
        </div>
    `;
    
    fetch('/api/control-numbers/create-customer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayResult(data.data);
            showNotification('Customer control number generated successfully!', 'success');
            loadRecentCustomerNumbers();
            resetForm();
        } else {
            resultContent.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-full">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-red-800 font-semibold">Generation Failed</h4>
                            <p class="text-sm text-gray-600">${data.message || 'Failed to generate control number'}</p>
                        </div>
                    </div>
                </div>
            `;
            showNotification(data.message || 'Failed to generate control number', 'error');
        }
    })
    .catch(error => {
        console.error('Error generating control number:', error);
        resultContent.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-full">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-red-800 font-semibold">Error</h4>
                        <p class="text-sm text-gray-600">Failed to generate control number: ${error.message}</p>
                    </div>
                </div>
            </div>
        `;
        showNotification('Failed to generate control number', 'error');
    });
}

function displayResult(data) {
    const resultContent = document.getElementById('resultContent');
    
    resultContent.innerHTML = `
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <div class="flex items-center mb-4">
                <div class="p-2 bg-green-100 rounded-full">
                    <i class="fas fa-check text-green-600"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-green-800 font-semibold">Control Number Generated Successfully</h4>
                    <p class="text-sm text-gray-600">Customer BillPay control number has been created</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">BillPay Number</label>
                    <div class="mt-1 flex items-center">
                        <span class="text-lg font-semibold text-gray-900">${data.billPayNumber}</span>
                        <button onclick="copyToClipboard('${data.billPayNumber}')" class="ml-2 text-blue-600 hover:text-blue-800">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Customer Name</label>
                    <p class="mt-1 text-lg font-semibold text-gray-900">${data.billCustomerName || 'N/A'}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Bill Amount</label>
                    <p class="mt-1 text-lg font-semibold text-gray-900">${number_format(data.billAmount)} TZS</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Payment Mode</label>
                    <p class="mt-1 text-lg font-semibold text-gray-900">${data.billPaymentMode}</p>
                </div>
            </div>
            
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-500">Bill Description</label>
                <p class="mt-1 text-gray-900">${data.billDescription}</p>
            </div>
            
            <div class="mt-6 flex space-x-4">
                <button onclick="printControlNumber('${data.billPayNumber}')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
                <button onclick="downloadControlNumber('${data.billPayNumber}')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </button>
                <button onclick="sendToCustomer('${data.billPayNumber}')" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>Send to Customer
                </button>
            </div>
        </div>
    `;
}

function loadRecentCustomerNumbers() {
    fetch('/api/control-numbers/recent-customer')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('recentCustomerNumbers');
            if (data.success && data.data.length > 0) {
                container.innerHTML = data.data.map(item => `
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-900">${item.billPayNumber}</span>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        ${item.billPaymentMode}
                                    </span>
                                </div>
                                <div class="mt-1 text-sm text-gray-600">
                                    <span class="font-medium">${number_format(item.billAmount)} TZS</span>
                                    <span class="mx-2">·</span>
                                    <span>${item.billCustomerName || 'N/A'}</span>
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    ${item.billDescription}
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="viewDetails('${item.billPayNumber}')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i>View
                                </button>
                                <button onclick="copyToClipboard('${item.billPayNumber}')" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                                    <i class="fas fa-copy mr-1"></i>Copy
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = `
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <p>No recent customer control numbers found</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading recent customer numbers:', error);
            document.getElementById('recentCustomerNumbers').innerHTML = `
                <div class="text-center text-red-500 py-8">
                    <p>Failed to load recent customer control numbers</p>
                </div>
            `;
        });
}

function resetForm() {
    document.getElementById('customerControlForm').reset();
    document.getElementById('resultSection').classList.add('hidden');
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Control number copied to clipboard!', 'success');
    }).catch(() => {
        showNotification('Failed to copy to clipboard', 'error');
    });
}

function printControlNumber(billPayNumber) {
    window.print();
}

function downloadControlNumber(billPayNumber) {
    // Implement PDF download functionality
    showNotification('PDF download feature coming soon', 'info');
}

function sendToCustomer(billPayNumber) {
    // Implement send to customer functionality
    showNotification('Send to customer feature coming soon', 'info');
}

function viewDetails(billPayNumber) {
    window.location.href = `/control-numbers/view/${billPayNumber}`;
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

// Helper function for number formatting
function number_format(num) {
    return new Intl.NumberFormat('en-US').format(num);
}
</script>
@endsection
