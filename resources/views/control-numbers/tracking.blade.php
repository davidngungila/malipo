@extends('layouts.app')

@section('title', 'Control Number Tracking - FEEDTAN System')

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
                <li class="text-gray-900 font-medium">Tracking & Monitoring</li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Control Number Tracking & Monitoring</h1>
                <p class="text-gray-600 mt-1">Real-time monitoring and tracking of all control number generation</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <button onclick="refreshTrackingData()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
                <button onclick="exportTrackingData()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors inline-flex items-center">
                    <i class="fas fa-download mr-2"></i>Export Data
                </button>
            </div>
        </div>
    </div>

    <!-- System Health Status -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-heartbeat text-green-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">API Status</p>
                    <p class="text-lg font-semibold text-gray-900" id="apiStatus">Checking...</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-tachometer-alt text-blue-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Response Time</p>
                    <p class="text-lg font-semibold text-gray-900" id="avgResponseTime">0ms</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Success Rate</p>
                    <p class="text-lg font-semibold text-gray-900" id="successRate">0%</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Error Count</p>
                    <p class="text-lg font-semibold text-gray-900" id="errorCount">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Tracking -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Real-time Tracking</h3>
            <p class="text-sm text-gray-500 mt-1">Live monitoring of control number generation requests</p>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <div class="flex space-x-4">
                    <button onclick="filterTracking('all')" id="filterAll" class="px-4 py-2 bg-blue-600 text-white rounded-lg transition-colors">
                        All Requests
                    </button>
                    <button onclick="filterTracking('success')" id="filterSuccess" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Success Only
                    </button>
                    <button onclick="filterTracking('error')" id="filterError" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Errors Only
                    </button>
                    <button onclick="filterTracking('pending')" id="filterPending" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Pending
                    </button>
                </div>
            </div>
            
            <div id="trackingTable" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Control Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="trackingTableBody" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mb-2"></div>
                                <p>Loading tracking data...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Error Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Errors</h3>
            </div>
            <div class="p-6">
                <div id="recentErrors" class="space-y-3">
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                        <p>Loading error data...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Performance Metrics</h3>
            </div>
            <div class="p-6">
                <div id="performanceMetrics" class="space-y-4">
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                        <p>Loading performance data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Logs -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">System Logs</h3>
                <div class="flex space-x-2">
                    <select id="logLevel" onchange="filterLogs()" class="border border-gray-300 rounded px-3 py-1 text-sm">
                        <option value="all">All Levels</option>
                        <option value="error">Errors</option>
                        <option value="success">Success</option>
                        <option value="info">Info</option>
                    </select>
                    <button onclick="clearLogs()" class="bg-red-100 text-red-700 px-3 py-1 rounded text-sm hover:bg-red-200 transition-colors">
                        Clear Logs
                    </button>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div id="systemLogs" class="space-y-2 max-h-96 overflow-y-auto">
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                    <p>Loading system logs...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Request Details Modal -->
<div id="requestDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Request Details</h3>
            <button onclick="closeRequestDetailsModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="requestDetailsContent">
            <!-- Request details will be displayed here -->
        </div>
    </div>
</div>

<script>
let trackingData = [];
let currentFilter = 'all';
let autoRefreshInterval;

document.addEventListener('DOMContentLoaded', function() {
    loadTrackingData();
    startAutoRefresh();
});

function startAutoRefresh() {
    autoRefreshInterval = setInterval(() => {
        loadTrackingData();
        updateSystemHealth();
    }, 5000); // Refresh every 5 seconds
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
}

function loadTrackingData() {
    fetch('/api/control-numbers/tracking-data')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                trackingData = data.data;
                updateTrackingTable();
                updateSystemHealth();
                loadRecentErrors();
                loadPerformanceMetrics();
                loadSystemLogs();
            } else {
                console.error('Failed to load tracking data:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading tracking data:', error);
        });
}

function updateTrackingTable() {
    const tbody = document.getElementById('trackingTableBody');
    const filteredData = filterTrackingData(trackingData, currentFilter);
    
    if (filteredData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-3xl mb-2"></i>
                    <p>No tracking data available</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = filteredData.map(item => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                ${item.requestId}
                <button onclick="copyToClipboard('${item.requestId}')" class="ml-1 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-copy text-xs"></i>
                </button>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${item.customerName || 'N/A'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${item.billPayNumber || '-'}
                ${item.billPayNumber ? `
                    <button onclick="copyToClipboard('${item.billPayNumber}')" class="ml-1 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-copy text-xs"></i>
                    </button>
                ` : ''}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${item.billAmount ? formatCurrency(item.billAmount) : '-'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusClass(item.status)}">
                    ${item.status}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${item.processingTime ? item.processingTime + 'ms' : '-'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${formatDateTime(item.createdAt)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <button onclick="showRequestDetails('${item.requestId}')" class="text-blue-600 hover:text-blue-800 mr-2">
                    <i class="fas fa-eye"></i>
                </button>
                ${item.status === 'ERROR' ? `
                    <button onclick="retryRequest('${item.requestId}')" class="text-orange-600 hover:text-orange-800">
                        <i class="fas fa-redo"></i>
                    </button>
                ` : ''}
            </td>
        </tr>
    `).join('');
}

function filterTrackingData(data, filter) {
    switch (filter) {
        case 'success':
            return data.filter(item => item.status === 'SUCCESS');
        case 'error':
            return data.filter(item => item.status === 'ERROR');
        case 'pending':
            return data.filter(item => item.status === 'PENDING');
        default:
            return data;
    }
}

function filterTracking(filter) {
    currentFilter = filter;
    
    // Update button styles
    document.querySelectorAll('[id^="filter"]').forEach(btn => {
        btn.className = 'px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors';
    });
    
    const activeBtn = document.getElementById('filter' + filter.charAt(0).toUpperCase() + filter.slice(1));
    if (activeBtn) {
        activeBtn.className = 'px-4 py-2 bg-blue-600 text-white rounded-lg transition-colors';
    }
    
    updateTrackingTable();
}

function updateSystemHealth() {
    // Calculate metrics from tracking data
    const totalRequests = trackingData.length;
    const successfulRequests = trackingData.filter(item => item.status === 'SUCCESS').length;
    const errorRequests = trackingData.filter(item => item.status === 'ERROR').length;
    const avgResponseTime = trackingData.length > 0 
        ? Math.round(trackingData.reduce((sum, item) => sum + (item.processingTime || 0), 0) / trackingData.length)
        : 0;
    const successRate = totalRequests > 0 ? Math.round((successfulRequests / totalRequests) * 100) : 0;
    
    // Update UI
    document.getElementById('apiStatus').textContent = errorRequests === 0 ? 'Healthy' : 'Issues Detected';
    document.getElementById('apiStatus').className = errorRequests === 0 
        ? 'text-lg font-semibold text-green-600' 
        : 'text-lg font-semibold text-red-600';
    
    document.getElementById('avgResponseTime').textContent = avgResponseTime + 'ms';
    document.getElementById('successRate').textContent = successRate + '%';
    document.getElementById('errorCount').textContent = errorRequests;
}

function loadRecentErrors() {
    const errors = trackingData.filter(item => item.status === 'ERROR').slice(0, 5);
    const container = document.getElementById('recentErrors');
    
    if (errors.length === 0) {
        container.innerHTML = `
            <div class="text-center text-gray-500 py-4">
                <i class="fas fa-check-circle text-2xl mb-2 text-green-500"></i>
                <p>No recent errors</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = errors.map(error => `
        <div class="border-l-4 border-red-500 pl-4 py-2">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-900">${error.requestId}</p>
                    <p class="text-sm text-gray-600">${error.errorMessage || 'Unknown error'}</p>
                    <p class="text-xs text-gray-500">${formatDateTime(error.createdAt)}</p>
                </div>
                <button onclick="showRequestDetails('${error.requestId}')" class="text-blue-600 hover:text-blue-800 text-sm">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>
    `).join('');
}

function loadPerformanceMetrics() {
    const container = document.getElementById('performanceMetrics');
    
    // Calculate performance metrics
    const responseTimes = trackingData.map(item => item.processingTime || 0).filter(time => time > 0);
    const avgTime = responseTimes.length > 0 ? responseTimes.reduce((a, b) => a + b) / responseTimes.length : 0;
    const maxTime = responseTimes.length > 0 ? Math.max(...responseTimes) : 0;
    const minTime = responseTimes.length > 0 ? Math.min(...responseTimes) : 0;
    
    container.innerHTML = `
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Average Response Time</span>
                <span class="text-sm font-medium text-gray-900">${Math.round(avgTime)}ms</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Min Response Time</span>
                <span class="text-sm font-medium text-gray-900">${minTime}ms</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Max Response Time</span>
                <span class="text-sm font-medium text-gray-900">${maxTime}ms</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Total Requests</span>
                <span class="text-sm font-medium text-gray-900">${trackingData.length}</span>
            </div>
        </div>
    `;
}

function loadSystemLogs() {
    const container = document.getElementById('systemLogs');
    const logLevel = document.getElementById('logLevel').value;
    
    // Mock system logs - in production, this would come from actual log files
    const logs = [
        { level: 'success', message: 'Control number generated successfully', timestamp: new Date() },
        { level: 'info', message: 'API connection established', timestamp: new Date(Date.now() - 60000) },
        { level: 'error', message: 'Validation failed for request REQ_123', timestamp: new Date(Date.now() - 120000) },
        { level: 'success', message: 'Bulk generation completed: 45/50 successful', timestamp: new Date(Date.now() - 180000) }
    ];
    
    const filteredLogs = logLevel === 'all' ? logs : logs.filter(log => log.level === logLevel);
    
    container.innerHTML = filteredLogs.map(log => `
        <div class="flex items-start space-x-2 p-2 rounded ${getLogClass(log.level)}">
            <i class="fas ${getLogIcon(log.level)} mt-1"></i>
            <div class="flex-1">
                <p class="text-sm">${log.message}</p>
                <p class="text-xs text-gray-500">${formatDateTime(log.timestamp)}</p>
            </div>
        </div>
    `).join('');
}

function getLogClass(level) {
    switch (level) {
        case 'error': return 'bg-red-50 text-red-800';
        case 'success': return 'bg-green-50 text-green-800';
        case 'info': return 'bg-blue-50 text-blue-800';
        default: return 'bg-gray-50 text-gray-800';
    }
}

function getLogIcon(level) {
    switch (level) {
        case 'error': return 'fas fa-exclamation-triangle';
        case 'success': return 'fas fa-check-circle';
        case 'info': return 'fas fa-info-circle';
        default: return 'fas fa-circle';
    }
}

function getStatusClass(status) {
    switch (status) {
        case 'SUCCESS': return 'bg-green-100 text-green-800';
        case 'ERROR': return 'bg-red-100 text-red-800';
        case 'PENDING': return 'bg-yellow-100 text-yellow-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function showRequestDetails(requestId) {
    const request = trackingData.find(item => item.requestId === requestId);
    if (!request) return;
    
    const modal = document.getElementById('requestDetailsModal');
    const content = document.getElementById('requestDetailsContent');
    
    content.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-900 mb-4">Request Information</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Request ID:</span>
                        <span class="text-sm font-medium">${request.requestId}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Status:</span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusClass(request.status)}">
                            ${request.status}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Processing Time:</span>
                        <span class="text-sm font-medium">${request.processingTime || '-'}ms</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Created At:</span>
                        <span class="text-sm font-medium">${formatDateTime(request.createdAt)}</span>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="font-medium text-gray-900 mb-4">Customer Information</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Customer Name:</span>
                        <span class="text-sm font-medium">${request.customerName || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Phone:</span>
                        <span class="text-sm font-medium">${request.customerPhone || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Email:</span>
                        <span class="text-sm font-medium">${request.customerEmail || 'N/A'}</span>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="font-medium text-gray-900 mb-4">Bill Information</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Control Number:</span>
                        <span class="text-sm font-medium">${request.billPayNumber || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Amount:</span>
                        <span class="text-sm font-medium">${request.billAmount ? formatCurrency(request.billAmount) : 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Description:</span>
                        <span class="text-sm font-medium">${request.billDescription || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Reference:</span>
                        <span class="text-sm font-medium">${request.billReference || 'N/A'}</span>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="font-medium text-gray-900 mb-4">Error Information</h4>
                ${request.errorMessage ? `
                    <div class="bg-red-50 border border-red-200 rounded p-3">
                        <p class="text-sm text-red-800">${request.errorMessage}</p>
                        ${request.errorDetails ? `
                            <div class="mt-2 text-xs text-red-600">
                                <p><strong>File:</strong> ${request.errorDetails.file}</p>
                                <p><strong>Line:</strong> ${request.errorDetails.line}</p>
                            </div>
                        ` : ''}
                    </div>
                ` : '<p class="text-sm text-gray-500">No errors</p>'}
            </div>
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            ${request.status === 'ERROR' ? `
                <button onclick="retryRequest('${request.requestId}')" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                    <i class="fas fa-redo mr-2"></i>Retry Request
                </button>
            ` : ''}
            <button onclick="closeRequestDetailsModal()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                Close
            </button>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeRequestDetailsModal() {
    document.getElementById('requestDetailsModal').classList.add('hidden');
}

function retryRequest(requestId) {
    showNotification('Retry functionality coming soon', 'info');
    closeRequestDetailsModal();
}

function refreshTrackingData() {
    loadTrackingData();
    showNotification('Tracking data refreshed', 'success');
}

function exportTrackingData() {
    showNotification('Export functionality coming soon', 'info');
}

function filterLogs() {
    loadSystemLogs();
}

function clearLogs() {
    showNotification('Clear logs functionality coming soon', 'info');
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Copied to clipboard', 'success');
    }).catch(() => {
        showNotification('Failed to copy', 'error');
    });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'TZS'
    }).format(amount);
}

function formatDateTime(date) {
    return new Date(date).toLocaleString();
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

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    stopAutoRefresh();
});
</script>
@endsection
