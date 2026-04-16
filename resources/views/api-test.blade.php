@extends('layouts.app')

@section('title', 'API Connection Test - MUSARIS System')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><span class="text-gray-700 font-medium">API Connection Test</span></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">API Connection Test</h1>
                <p class="text-gray-600 mt-1">Test ClickPesa API connectivity and troubleshoot issues</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button onclick="runAllTests()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-play mr-2"></i>Run All Tests
                </button>
                <button onclick="clearResults()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium ml-2">
                    <i class="fas fa-trash mr-2"></i>Clear Results
                </button>
            </div>
        </div>
    </div>

    <!-- Test Results -->
    <div id="testResults" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Results will be populated here -->
    </div>

    <!-- API Configuration Status -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">API Configuration Status</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Token Generation Test -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-900">Token Generation</h3>
                    <div id="tokenStatus" class="w-3 h-3 rounded-full bg-gray-300 animate-pulse"></div>
                </div>
                <div id="tokenResult" class="text-sm text-gray-600">Not tested</div>
            </div>

            <!-- Payment Preview Test -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-900">Payment Preview</h3>
                    <div id="previewStatus" class="w-3 h-3 rounded-full bg-gray-300 animate-pulse"></div>
                </div>
                <div id="previewResult" class="text-sm text-gray-600">Not tested</div>
            </div>

            <!-- Payment History Test -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-900">Payment History</h3>
                    <div id="historyStatus" class="w-3 h-3 rounded-full bg-gray-300 animate-pulse"></div>
                </div>
                <div id="historyResult" class="text-sm text-gray-600">Not tested</div>
            </div>

            <!-- Payment Data Query -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-900">Payment Data Query</h3>
                    <div id="queryStatus" class="w-3 h-3 rounded-full bg-gray-300 animate-pulse"></div>
                </div>
                <div id="queryResult" class="text-sm text-gray-600">Not tested</div>
            </div>

            <!-- Connection Speed Test -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-900">Connection Speed</h3>
                    <div id="speedStatus" class="w-3 h-3 rounded-full bg-gray-300 animate-pulse"></div>
                </div>
                <div id="speedResult" class="text-sm text-gray-600">Not tested</div>
            </div>

            <!-- Authentication Test -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-900">Authentication</h3>
                    <div id="authStatus" class="w-3 h-3 rounded-full bg-gray-300 animate-pulse"></div>
                </div>
                <div id="authResult" class="text-sm text-gray-600">Not tested</div>
            </div>
        </div>
    </div>

    <!-- Diagnostics Panel -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Diagnostics & Troubleshooting</h2>
        
        <div class="space-y-4">
            <!-- System Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-3">System Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <strong>Server Time:</strong>
                        <span id="serverTime" class="text-gray-600">Loading...</span>
                    </div>
                    <div>
                        <strong>Client Time:</strong>
                        <span id="clientTime" class="text-gray-600">Loading...</span>
                    </div>
                    <div>
                        <strong>IP Address:</strong>
                        <span id="ipAddress" class="text-gray-600">Loading...</span>
                    </div>
                    <div>
                        <strong>Browser:</strong>
                        <span id="browserInfo" class="text-gray-600">Loading...</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <button onclick="testTokenGeneration()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm">
                        <i class="fas fa-key mr-2"></i>Test Token Generation
                    </button>
                    <button onclick="testPaymentPreview()" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded text-sm">
                        <i class="fas fa-eye mr-2"></i>Test Payment Preview
                    </button>
                    <button onclick="testPaymentHistory()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm">
                        <i class="fas fa-history mr-2"></i>Test Payment History
                    </button>
                    <button onclick="testConnectionSpeed()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded text-sm">
                        <i class="fas fa-tachometer-alt mr-2"></i>Test Connection Speed
                    </button>
                </div>
            </div>

            <!-- Troubleshooting Guide -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Common Issues & Solutions</h3>
                <div class="space-y-3 text-sm">
                    <div class="border-l-4 border-blue-500 pl-4">
                        <strong class="text-blue-700">401 Unauthorized:</strong>
                        <span class="text-gray-600">Check API credentials and IP whitelist</span>
                    </div>
                    <div class="border-l-4 border-yellow-500 pl-4">
                        <strong class="text-yellow-700">Connection Timeout:</strong>
                        <span class="text-gray-600">Check network connectivity and firewall</span>
                    </div>
                    <div class="border-l-4 border-red-500 pl-4">
                        <strong class="text-red-700">Invalid Response:</strong>
                        <span class="text-gray-600">API endpoint may be down or changed</span>
                    </div>
                    <div class="border-l-4 border-green-500 pl-4">
                        <strong class="text-green-700">Working Slowly:</strong>
                        <span class="text-gray-600">Check API rate limits and server performance</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// API Test Functions
let testResults = [];

function updateTestStatus(testId, status, message, details = null) {
    const statusElement = document.getElementById(testId + 'Status');
    const resultElement = document.getElementById(testId + 'Result');
    
    // Update status indicator
    statusElement.className = status === 'success' ? 
        'w-3 h-3 rounded-full bg-green-500' : 
        status === 'error' ? 
        'w-3 h-3 rounded-full bg-red-500' : 
        'w-3 h-3 rounded-full bg-yellow-500';
    
    statusElement.className = status === 'success' ? 
        statusElement.className + ' animate-pulse' : 
        statusElement.className;
    
    // Update result text
    resultElement.innerHTML = message;
    
    // Add to results panel
    addToResults(testId, status, message, details);
}

function addToResults(testId, status, message, details) {
    const resultsContainer = document.getElementById('testResults');
    
    const resultCard = document.createElement('div');
    resultCard.className = `bg-white rounded-lg shadow p-6 ${status === 'success' ? 'border-green-200' : status === 'error' ? 'border-red-200' : 'border-yellow-200'}`;
    resultCard.innerHTML = `
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-semibold ${status === 'success' ? 'text-green-800' : status === 'error' ? 'text-red-800' : 'text-yellow-800'}">${getTestName(testId)}</h3>
            <div class="px-2 py-1 rounded-full ${status === 'success' ? 'bg-green-100 text-green-800' : status === 'error' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'} text-xs font-medium">
                ${status === 'success' ? '✓ Passed' : status === 'error' ? '✗ Failed' : '⚠ Warning'}
            </div>
        </div>
        <div class="text-sm text-gray-600">
            <p class="mb-2">${message}</p>
            ${details ? `<div class="bg-gray-50 rounded p-3 text-xs"><pre>${JSON.stringify(details, null, 2)}</pre></div>` : ''}
        </div>
    `;
    
    resultsContainer.appendChild(resultCard);
}

function getTestName(testId) {
    const names = {
        'token': 'Token Generation',
        'preview': 'Payment Preview',
        'history': 'Payment History',
        'query': 'Payment Data Query',
        'speed': 'Connection Speed',
        'auth': 'Authentication'
    };
    return names[testId] || 'Unknown Test';
}

function runAllTests() {
    clearResults();
    testTokenGeneration();
    setTimeout(() => testPaymentPreview(), 500);
    setTimeout(() => testPaymentHistory(), 1000);
    setTimeout(() => testPaymentDataQuery(), 1500);
    setTimeout(() => testConnectionSpeed(), 2000);
    setTimeout(() => testAuthentication(), 2500);
}

function clearResults() {
    document.getElementById('testResults').innerHTML = '<!-- Results will be populated here -->';
    
    // Reset all status indicators
    ['token', 'preview', 'history', 'query', 'speed', 'auth'].forEach(testId => {
        const statusElement = document.getElementById(testId + 'Status');
        const resultElement = document.getElementById(testId + 'Result');
        if (statusElement) {
            statusElement.className = 'w-3 h-3 rounded-full bg-gray-300 animate-pulse';
        }
        if (resultElement) {
            resultElement.textContent = 'Not tested';
        }
    });
}

function testTokenGeneration() {
    updateTestStatus('token', 'loading', 'Testing token generation...');
    
    fetch('/api/status-check')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateTestStatus('token', 'success', 'Token generation working', data);
            } else {
                updateTestStatus('token', 'error', 'Token generation failed', data);
            }
        })
        .catch(error => {
            updateTestStatus('token', 'error', 'Connection failed', { error: error.message });
        });
}

function testPaymentPreview() {
    updateTestStatus('preview', 'loading', 'Testing payment preview API...');
    
    fetch('/api/status-check')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.endpoints && data.endpoints.payment_preview === 'Working') {
                updateTestStatus('preview', 'success', 'Payment preview API working');
            } else {
                updateTestStatus('preview', 'error', 'Payment preview API not working', data);
            }
        })
        .catch(error => {
            updateTestStatus('preview', 'error', 'Connection failed', { error: error.message });
        });
}

function testPaymentHistory() {
    updateTestStatus('history', 'loading', 'Testing payment history API...');
    
    fetch('/api/status-check')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.endpoints && data.endpoints.payment_history === 'Working') {
                updateTestStatus('history', 'success', 'Payment history API working');
            } else {
                updateTestStatus('history', 'error', 'Payment history API not working', data);
            }
        })
        .catch(error => {
            updateTestStatus('history', 'error', 'Connection failed', { error: error.message });
        });
}

function testPaymentDataQuery() {
    updateTestStatus('query', 'loading', 'Testing payment data query...');
    
    // Test the payment data endpoint
    fetch('/payments')
        .then(response => response.text())
        .then(html => {
            if (html.includes('Retrieved') && html.includes('payment records')) {
                updateTestStatus('query', 'success', 'Payment data query working');
            } else {
                updateTestStatus('query', 'error', 'Payment data query failed', { html: html.substring(0, 200) });
            }
        })
        .catch(error => {
            updateTestStatus('query', 'error', 'Payment query connection failed', { error: error.message });
        });
}

function testConnectionSpeed() {
    updateTestStatus('speed', 'loading', 'Testing connection speed...');
    
    const startTime = Date.now();
    
    fetch('/api/status-check')
        .then(response => response.json())
        .then(data => {
            const endTime = Date.now();
            const responseTime = endTime - startTime;
            
            if (responseTime < 1000) {
                updateTestStatus('speed', 'success', `Fast connection (${responseTime}ms)`);
            } else if (responseTime < 3000) {
                updateTestStatus('speed', 'success', `Good connection (${responseTime}ms)`);
            } else {
                updateTestStatus('speed', 'error', `Slow connection (${responseTime}ms)`);
            }
        })
        .catch(error => {
            updateTestStatus('speed', 'error', 'Speed test failed', { error: error.message });
        });
}

function testAuthentication() {
    updateTestStatus('auth', 'loading', 'Testing authentication...');
    
    fetch('/api/status-check')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.token) {
                updateTestStatus('auth', 'success', 'Authentication working');
            } else {
                updateTestStatus('auth', 'error', 'Authentication failed', data);
            }
        })
        .catch(error => {
            updateTestStatus('auth', 'error', 'Auth test failed', { error: error.message });
        });
}

// Load system information
document.addEventListener('DOMContentLoaded', function() {
    // Server time
    fetch('/api/status-check')
        .then(response => response.json())
        .then(data => {
            if (data.timestamp) {
                document.getElementById('serverTime').textContent = new Date(data.timestamp).toLocaleString();
            }
        });
    
    // Client time
    document.getElementById('clientTime').textContent = new Date().toLocaleString();
    
    // IP Address
    fetch('https://api.ipify.org?format=json')
        .then(response => response.json())
        .then(data => {
            document.getElementById('ipAddress').textContent = data.ip;
        })
        .catch(error => {
            document.getElementById('ipAddress').textContent = 'Unable to detect';
        });
    
    // Browser info
    const userAgent = navigator.userAgent;
    let browserInfo = 'Unknown';
    if (userAgent.indexOf('Chrome') > -1) browserInfo = 'Chrome';
    else if (userAgent.indexOf('Firefox') > -1) browserInfo = 'Firefox';
    else if (userAgent.indexOf('Safari') > -1) browserInfo = 'Safari';
    else if (userAgent.indexOf('Edge') > -1) browserInfo = 'Edge';
    
    document.getElementById('browserInfo').textContent = browserInfo;
});
</script>
@endsection
