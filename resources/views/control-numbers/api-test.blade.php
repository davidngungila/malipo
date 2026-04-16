@extends('layouts.app')

@section('title', 'API Test - Control Numbers')

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
                <li class="text-gray-900 font-medium">API Test</li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">API Connectivity Test</h1>
                <p class="text-gray-600 mt-1">Test direct ClickPesa API connectivity and control number generation</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('control-numbers.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Control Numbers
                </a>
            </div>
        </div>
    </div>

    <!-- API Status Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-plug text-blue-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">API Connection</p>
                    <p class="text-lg font-semibold text-gray-900" id="apiConnection">Not Tested</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-key text-green-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Authentication</p>
                    <p class="text-lg font-semibold text-gray-900" id="apiAuth">Not Tested</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-hashtag text-purple-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Control Number Generation</p>
                    <p class="text-lg font-semibold text-gray-900" id="controlGen">Not Tested</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Controls -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">API Tests</h3>
            <p class="text-sm text-gray-500 mt-1">Test different aspects of the ClickPesa API connectivity</p>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex space-x-4">
                    <button onclick="testAPIConnection()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plug mr-2"></i>Test API Connection
                    </button>
                    <button onclick="testAuthentication()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-key mr-2"></i>Test Authentication
                    </button>
                    <button onclick="testControlGeneration()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-hashtag mr-2"></i>Test Control Number
                    </button>
                    <button onclick="testBulkGeneration()" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                        <i class="fas fa-list mr-2"></i>Test Bulk Generation
                    </button>
                    <button onclick="runAllTests()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-play mr-2"></i>Run All Tests
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Results -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Individual Test Results -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Test Results</h3>
            </div>
            <div class="p-6">
                <div id="testResults" class="space-y-4">
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-vial text-3xl mb-2"></i>
                        <p>Run tests to see results</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Response Details -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">API Response Details</h3>
            </div>
            <div class="p-6">
                <div id="apiResponse" class="space-y-4">
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-code text-3xl mb-2"></i>
                        <p>API responses will appear here</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Test Form -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Live Control Number Test</h3>
            <p class="text-sm text-gray-500 mt-1">Generate a real control number to test API connectivity</p>
        </div>
        <div class="p-6">
            <form id="liveTestForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer Name *</label>
                        <input type="text" id="testCustomerName" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Test Customer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" id="testCustomerPhone" placeholder="255712345678"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="testCustomerEmail" placeholder="test@example.com"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount (TZS)</label>
                        <input type="number" id="testAmount" min="100" value="10000"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                    <input type="text" id="testDescription" required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Test Control Number Generation">
                </div>
                <div class="flex space-x-4">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-play mr-2"></i>Generate Test Control Number
                    </button>
                    <button type="button" onclick="clearForm()" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-eraser mr-2"></i>Clear Form
                    </button>
                </div>
            </form>
            
            <div id="liveTestResult" class="mt-6 hidden">
                <!-- Live test result will appear here -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-run basic connection test on page load
    setTimeout(() => {
        testAPIConnection();
    }, 1000);
    
    // Setup form submission
    document.getElementById('liveTestForm').addEventListener('submit', function(e) {
        e.preventDefault();
        generateLiveTestControlNumber();
    });
});

function testAPIConnection() {
    updateStatus('apiConnection', 'Testing...');
    
    fetch('/api/control-numbers/test-connection', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        updateStatus('apiConnection', data.success ? 'Connected' : 'Failed');
        addTestResult('API Connection', data.success, data.message || data.error);
        displayAPIResponse('API Connection Test', data);
    })
    .catch(error => {
        updateStatus('apiConnection', 'Error');
        addTestResult('API Connection', false, 'Connection error: ' + error.message);
        displayAPIResponse('API Connection Test', { error: error.message });
    });
}

function testAuthentication() {
    updateStatus('apiAuth', 'Testing...');
    
    fetch('/api/control-numbers/test-auth', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        updateStatus('apiAuth', data.success ? 'Authenticated' : 'Failed');
        addTestResult('Authentication', data.success, data.message || data.error);
        displayAPIResponse('Authentication Test', data);
    })
    .catch(error => {
        updateStatus('apiAuth', 'Error');
        addTestResult('Authentication', false, 'Auth error: ' + error.message);
        displayAPIResponse('Authentication Test', { error: error.message });
    });
}

function testControlGeneration() {
    updateStatus('controlGen', 'Testing...');
    
    const testData = {
        customerName: 'Test Customer',
        customerPhone: '255712345678',
        billDescription: 'Test Control Number Generation',
        billAmount: 10000,
        billPaymentMode: 'ALLOW_PARTIAL_AND_OVER_PAYMENT'
    };
    
    fetch('/api/control-numbers/create-customer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(testData)
    })
    .then(response => response.json())
    .then(data => {
        updateStatus('controlGen', data.success ? 'Working' : 'Failed');
        addTestResult('Control Generation', data.success, data.message || data.error);
        displayAPIResponse('Control Number Generation', data);
    })
    .catch(error => {
        updateStatus('controlGen', 'Error');
        addTestResult('Control Generation', false, 'Generation error: ' + error.message);
        displayAPIResponse('Control Number Generation', { error: error.message });
    });
}

function testBulkGeneration() {
    const bulkData = {
        controlNumbers: [
            {
                customerName: 'Bulk Test Customer 1',
                customerPhone: '255712345678',
                billDescription: 'Bulk Test 1',
                billAmount: 5000
            },
            {
                customerName: 'Bulk Test Customer 2',
                customerPhone: '255713345678',
                billDescription: 'Bulk Test 2',
                billAmount: 7500
            }
        ]
    };
    
    fetch('/api/control-numbers/bulk-create-customer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(bulkData)
    })
    .then(response => response.json())
    .then(data => {
        addTestResult('Bulk Generation', data.success, data.message || data.error);
        displayAPIResponse('Bulk Generation Test', data);
    })
    .catch(error => {
        addTestResult('Bulk Generation', false, 'Bulk error: ' + error.message);
        displayAPIResponse('Bulk Generation Test', { error: error.message });
    });
}

function runAllTests() {
    testAPIConnection();
    setTimeout(() => testAuthentication(), 2000);
    setTimeout(() => testControlGeneration(), 4000);
    setTimeout(() => testBulkGeneration(), 6000);
}

function generateLiveTestControlNumber() {
    const formData = {
        customerName: document.getElementById('testCustomerName').value,
        customerPhone: document.getElementById('testCustomerPhone').value,
        customerEmail: document.getElementById('testCustomerEmail').value,
        billAmount: document.getElementById('testAmount').value,
        billDescription: document.getElementById('testDescription').value,
        billPaymentMode: 'ALLOW_PARTIAL_AND_OVER_PAYMENT'
    };
    
    const resultDiv = document.getElementById('liveTestResult');
    resultDiv.classList.remove('hidden');
    resultDiv.innerHTML = `
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
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h4 class="text-green-800 font-semibold mb-2">✅ Control Number Generated Successfully!</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Control Number:</p>
                            <p class="font-semibold">${data.data.billPayNumber}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Processing Time:</p>
                            <p class="font-semibold">${data.processingTime}ms</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Request ID:</p>
                            <p class="font-semibold">${data.requestId}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Customer:</p>
                            <p class="font-semibold">${formData.customerName}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex space-x-2">
                        <button onclick="copyToClipboard('${data.data.billPayNumber}')" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">
                            <i class="fas fa-copy mr-1"></i>Copy Number
                        </button>
                        <button onclick="viewTracking('${data.requestId}')" class="bg-green-600 text-white px-3 py-1 rounded text-sm">
                            <i class="fas fa-eye mr-1"></i>View Tracking
                        </button>
                    </div>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h4 class="text-red-800 font-semibold mb-2">❌ Generation Failed</h4>
                    <p class="text-red-700">${data.message}</p>
                    ${data.errors ? `
                        <div class="mt-2">
                            <p class="text-sm font-semibold">Validation Errors:</p>
                            <ul class="list-disc list-inside text-sm">
                                ${data.errors.map(error => `<li>${error}</li>`).join('')}
                            </ul>
                        </div>
                    ` : ''}
                </div>
            `;
        }
        
        displayAPIResponse('Live Test Generation', data);
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="text-red-800 font-semibold mb-2">❌ API Error</h4>
                <p class="text-red-700">${error.message}</p>
            </div>
        `;
        
        displayAPIResponse('Live Test Generation', { error: error.message });
    });
}

function updateStatus(elementId, status) {
    const element = document.getElementById(elementId);
    element.textContent = status;
    
    // Update color based on status
    element.className = 'text-lg font-semibold ';
    if (status === 'Connected' || status === 'Authenticated' || status === 'Working') {
        element.className += 'text-green-600';
    } else if (status === 'Testing...') {
        element.className += 'text-blue-600';
    } else {
        element.className += 'text-red-600';
    }
}

function addTestResult(testName, success, message) {
    const resultsDiv = document.getElementById('testResults');
    const resultItem = document.createElement('div');
    resultItem.className = `p-4 rounded-lg border ${success ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'}`;
    
    resultItem.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas ${success ? 'fa-check-circle text-green-600' : 'fa-exclamation-circle text-red-600'} mr-2"></i>
                <span class="font-medium">${testName}</span>
            </div>
            <span class="text-sm text-gray-500">${new Date().toLocaleTimeString()}</span>
        </div>
        <p class="mt-2 text-sm ${success ? 'text-green-700' : 'text-red-700'}">${message}</p>
    `;
    
    // Add to top of results
    if (resultsDiv.children[0]?.classList?.contains('text-center')) {
        resultsDiv.innerHTML = '';
    }
    resultsDiv.insertBefore(resultItem, resultsDiv.firstChild);
}

function displayAPIResponse(testName, data) {
    const responseDiv = document.getElementById('apiResponse');
    
    // Clear initial message if present
    if (responseDiv.children[0]?.classList?.contains('text-center')) {
        responseDiv.innerHTML = '';
    }
    
    const responseItem = document.createElement('div');
    responseItem.className = 'border border-gray-200 rounded-lg p-4';
    
    responseItem.innerHTML = `
        <div class="flex items-center justify-between mb-2">
            <h4 class="font-medium">${testName}</h4>
            <span class="text-xs text-gray-500">${new Date().toLocaleTimeString()}</span>
        </div>
        <pre class="bg-gray-50 p-3 rounded text-xs overflow-x-auto">${JSON.stringify(data, null, 2)}</pre>
    `;
    
    // Add to top of responses
    responseDiv.insertBefore(responseItem, responseDiv.firstChild);
    
    // Keep only last 5 responses
    while (responseDiv.children.length > 5) {
        responseDiv.removeChild(responseDiv.lastChild);
    }
}

function clearForm() {
    document.getElementById('liveTestForm').reset();
    document.getElementById('liveTestResult').classList.add('hidden');
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Control number copied to clipboard!', 'success');
    }).catch(() => {
        showNotification('Failed to copy', 'error');
    });
}

function viewTracking(requestId) {
    window.open(`/control-numbers/tracking`, '_blank');
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
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
