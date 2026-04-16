@extends('layouts.app')

@section('title', 'Control Numbers - FEEDTAN System')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li class="text-gray-900 font-medium">Control Numbers</li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Control Numbers Management</h1>
                <p class="text-gray-600 mt-1">Generate and manage BillPay control numbers for customers and orders</p>
                
                <!-- API Connectivity Status -->
                <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" id="mainApiStatus">
                    <i class="fas fa-spinner fa-spin mr-2" id="mainApiIcon"></i>
                    <span id="mainApiText">Checking API connection...</span>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <button onclick="refreshSystemStatus()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh Status
                </button>
                <button onclick="testMainAPIConnection()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors inline-flex items-center">
                    <i class="fas fa-plug mr-2"></i>Test API
                </button>
            </div>
        </div>
    </div>

    <!-- System Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-hashtag text-blue-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Control Numbers</p>
                    <p class="text-lg font-semibold text-gray-900" id="totalControlNumbers">0</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-users text-green-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Customer Numbers</p>
                    <p class="text-lg font-semibold text-gray-900" id="customerNumbers">0</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-shopping-cart text-purple-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Order Numbers</p>
                    <p class="text-lg font-semibold text-gray-900" id="orderNumbers">0</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Active Numbers</p>
                    <p class="text-lg font-semibold text-gray-900" id="activeNumbers">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Customer Control Numbers -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-user-plus text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Customer Control Numbers</h3>
                </div>
                <p class="text-gray-600 mb-4">Generate BillPay control numbers for specific customers with their details</p>
                <div class="space-y-3">
                    <a href="{{ route('control-numbers.customer') }}" class="block w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-center">
                        <i class="fas fa-plus mr-2"></i>Create Customer Number
                    </a>
                    <a href="{{ route('control-numbers.bulk-customer') }}" class="block w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors text-center">
                        <i class="fas fa-users mr-2"></i>Bulk Customer Numbers
                    </a>
                </div>
            </div>
        </div>

        <!-- Order Control Numbers -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-shopping-bag text-green-600 text-xl"></i>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Order Control Numbers</h3>
                </div>
                <p class="text-gray-600 mb-4">Generate BillPay control numbers for orders and general bills</p>
                <div class="space-y-3">
                    <a href="{{ route('control-numbers.order') }}" class="block w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-center">
                        <i class="fas fa-plus mr-2"></i>Create Order Number
                    </a>
                    <a href="{{ route('control-numbers.bulk-order') }}" class="block w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors text-center">
                        <i class="fas fa-list mr-2"></i>Bulk Order Numbers
                    </a>
                </div>
            </div>
        </div>

        <!-- BillPay Management -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-cogs text-purple-600 text-xl"></i>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">BillPay Management</h3>
                </div>
                <p class="text-gray-600 mb-4">Query, update, and manage existing BillPay control numbers</p>
                <div class="space-y-3">
                    <a href="{{ route('control-numbers.query') }}" class="block w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-center">
                        <i class="fas fa-search mr-2"></i>Query Number
                    </a>
                    <a href="{{ route('control-numbers.manage') }}" class="block w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors text-center">
                        <i class="fas fa-tasks mr-2"></i>Manage Numbers
                    </a>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-orange-100 rounded-full">
                        <i class="fas fa-chart-line text-orange-600 text-xl"></i>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">System Status</h3>
                </div>
                <p class="text-gray-600 mb-4">Monitor BillPay system performance and statistics</p>
                <div class="space-y-3">
                    <a href="{{ route('control-numbers.tracking') }}" class="block w-full bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors text-center">
                        <i class="fas fa-satellite-dish mr-2"></i>Live Tracking
                    </a>
                    <a href="{{ route('control-numbers.status') }}" class="block w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors text-center">
                        <i class="fas fa-chart-bar mr-2"></i>View System Status
                    </a>
                    <a href="{{ route('control-numbers.reports') }}" class="block w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors text-center">
                        <i class="fas fa-file-alt mr-2"></i>Generate Reports
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-red-100 rounded-full">
                        <i class="fas fa-bolt text-red-600 text-xl"></i>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <p class="text-gray-600 mb-4">Frequently used operations and shortcuts</p>
                <div class="space-y-3">
                    <button onclick="quickGenerateCustomer()" class="block w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-center">
                        <i class="fas fa-magic mr-2"></i>Quick Generate
                    </button>
                    <button onclick="showRecentActivity()" class="block w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors text-center">
                        <i class="fas fa-history mr-2"></i>Recent Activity
                    </button>
                    <a href="{{ route('control-numbers.api-test') }}" class="block w-full bg-yellow-100 text-yellow-700 px-4 py-2 rounded-lg hover:bg-yellow-200 transition-colors text-center">
                        <i class="fas fa-vial mr-2"></i>API Test
                    </a>
                </div>
            </div>
        </div>

        <!-- Help & Documentation -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-indigo-100 rounded-full">
                        <i class="fas fa-question-circle text-indigo-600 text-xl"></i>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Help & Support</h3>
                </div>
                <p class="text-gray-600 mb-4">Documentation and support resources</p>
                <div class="space-y-3">
                    <a href="{{ route('control-numbers.docs') }}" class="block w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors text-center">
                        <i class="fas fa-book mr-2"></i>Documentation
                    </a>
                    <button onclick="showAPIInfo()" class="block w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors text-center">
                        <i class="fas fa-code mr-2"></i>API Info
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
        </div>
        <div class="p-6">
            <div id="recentActivity" class="space-y-3">
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                    <p>Loading recent activity...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Generate Modal -->
<div id="quickGenerateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Quick Generate Control Number</h3>
            <button onclick="closeQuickGenerateModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="quickGenerateForm">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Customer Name</label>
                <input type="text" id="quickCustomerName" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="tel" id="quickCustomerPhone" placeholder="255712345678" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Amount (TZS)</label>
                <input type="number" id="quickAmount" min="100" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <input type="text" id="quickDescription" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeQuickGenerateModal()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Generate
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Load system status on page load
document.addEventListener('DOMContentLoaded', function() {
    // Test API connectivity on page load
    testMainAPIConnection();
    loadSystemStatus();
    loadRecentActivity();
});

function loadSystemStatus() {
    fetch('/api/control-numbers/status')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalControlNumbers').textContent = data.data.total || 0;
                document.getElementById('customerNumbers').textContent = data.data.customer || 0;
                document.getElementById('orderNumbers').textContent = data.data.order || 0;
                document.getElementById('activeNumbers').textContent = data.data.active || 0;
            }
        })
        .catch(error => {
            console.error('Error loading system status:', error);
        });
}

function loadRecentActivity() {
    fetch('/api/control-numbers/recent-activity')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('recentActivity');
            if (data.success && data.data.length > 0) {
                container.innerHTML = data.data.map(activity => `
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 bg-${activity.type === 'customer' ? 'blue' : 'green'}-100 rounded-full mr-3">
                                <i class="fas fa-${activity.type === 'customer' ? 'user' : 'shopping-cart'} text-${activity.type === 'customer' ? 'blue' : 'green'}-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">${activity.description}</p>
                                <p class="text-xs text-gray-500">${activity.billPayNumber}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">${new Date(activity.createdAt).toLocaleString()}</p>
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${activity.status === 'ACTIVE' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                                ${activity.status}
                            </span>
                        </div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = `
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <p>No recent activity found</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading recent activity:', error);
            document.getElementById('recentActivity').innerHTML = `
                <div class="text-center text-red-500 py-8">
                    <p>Failed to load recent activity</p>
                </div>
            `;
        });
}

function refreshSystemStatus() {
    loadSystemStatus();
    loadRecentActivity();
    showNotification('System status refreshed', 'success');
}

function quickGenerateCustomer() {
    document.getElementById('quickGenerateModal').classList.remove('hidden');
}

function closeQuickGenerateModal() {
    document.getElementById('quickGenerateModal').classList.add('hidden');
    document.getElementById('quickGenerateForm').reset();
}

// Quick generate form submission
document.getElementById('quickGenerateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        customerName: document.getElementById('quickCustomerName').value,
        customerPhone: document.getElementById('quickCustomerPhone').value,
        billAmount: document.getElementById('quickAmount').value,
        billDescription: document.getElementById('quickDescription').value
    };
    
    fetch('/api/control-numbers/quick-generate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeQuickGenerateModal();
            showNotification('Control number generated successfully!', 'success');
            loadSystemStatus();
            loadRecentActivity();
        } else {
            showNotification(data.message || 'Failed to generate control number', 'error');
        }
    })
    .catch(error => {
        console.error('Error generating control number:', error);
        showNotification('Failed to generate control number', 'error');
    });
});

function showRecentActivity() {
    // Scroll to recent activity section
    document.querySelector('#recentActivity').scrollIntoView({ behavior: 'smooth' });
}

function showAPIInfo() {
    showNotification('API documentation coming soon', 'info');
}

function testMainAPIConnection() {
    const statusIndicator = document.getElementById('mainApiStatus');
    const statusIcon = document.getElementById('mainApiIcon');
    const statusText = document.getElementById('mainApiText');
    
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
</script>
@endsection
