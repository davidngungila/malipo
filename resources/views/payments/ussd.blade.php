@extends('layouts.app')

@section('title', 'USSD Push Payments - FEEDTAN System')

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
                <li><span class="text-gray-700 font-medium">USSD Push Payments</span></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">USSD Push Payments</h1>
                <p class="text-gray-600 mt-1">Send USSD Push requests to customers for payment collection</p>
                
                <!-- API Connectivity Status -->
                <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" id="ussdApiStatus">
                    <i class="fas fa-spinner fa-spin mr-2" id="ussdApiIcon"></i>
                    <span id="ussdApiText">Checking API connection...</span>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-green-600 font-medium">Service Active</span>
                </div>
                <button onclick="testUssdAPIConnection()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-plug mr-2"></i>Test API
                </button>
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    View History
                </button>
            </div>
        </div>
    </div>

    <!-- Payment Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <h3 class="text-sm font-medium text-gray-500">Today's USSD</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 450K</p>
            <p class="text-xs text-green-600 mt-1">47 transactions</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <h3 class="text-sm font-medium text-gray-500">This Week</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 2.8M</p>
            <p class="text-xs text-blue-600 mt-1">323 transactions</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <h3 class="text-sm font-medium text-gray-500">Success Rate</h3>
            <p class="text-2xl font-bold text-gray-900">92.5%</p>
            <p class="text-xs text-yellow-600 mt-1">25 pending</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <h3 class="text-sm font-medium text-gray-500">Avg. Amount</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 8,650</p>
            <p class="text-xs text-purple-600 mt-1">Per transaction</p>
        </div>
    </div>

    <!-- Payment Form -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">New USSD Payment</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Quick send USSD push to customer</span>
                </div>
            </div>
        </div>
        <div class="p-6">
            <form id="ussdForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Customer Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h3>
                        
                        <div>
                            <label for="customerName" class="block text-sm font-medium text-gray-700 mb-2">Customer Name</label>
                            <input type="text" id="customerName" name="customerName" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                   placeholder="Enter customer full name" required>
                        </div>

                        <div>
                            <label for="customerPhone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <div class="relative">
                                <input type="tel" id="customerPhone" name="customerPhone" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                       placeholder="255712345678" pattern="255[67]\d{8}" required>
                                <button type="button" onclick="clearPhone()" 
                                        class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Format: 255712345678 (Tanzania numbers only)</p>
                        </div>

                        <div>
                            <label for="customerEmail" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="customerEmail" name="customerEmail" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                   placeholder="customer@example.com">
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Details</h3>
                        
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount (TZS)</label>
                            <div class="relative">
                                <input type="number" id="amount" name="amount" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                       placeholder="1000" min="100" max="1000000" step="100" required>
                                <button type="button" onclick="clearAmount()" 
                                        class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Min: 100 TZS, Max: 1,000,000 TZS</p>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Payment Description</label>
                            <textarea id="description" name="description" rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                      placeholder="Enter payment description" required></textarea>
                        </div>

                        <div>
                            <label for="orderReference" class="block text-sm font-medium text-gray-700 mb-2">Order Reference (Optional)</label>
                            <input type="text" id="orderReference" name="orderReference" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                   placeholder="FEEDTAN123456789">
                            <p class="text-xs text-gray-500 mt-1">Leave blank to auto-generate</p>
                        </div>
                    </div>
                </div>

                <!-- Additional Options -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Options</h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input type="checkbox" id="fetchSenderDetails" name="fetchSenderDetails" 
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="fetchSenderDetails" class="ml-2 text-sm text-gray-700">
                                Fetch sender details (additional verification)
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="sendSMS" name="sendSMS" checked
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="sendSMS" class="ml-2 text-sm text-gray-700">
                                Send SMS notification to customer
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="sendEmail" name="sendEmail" 
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="sendEmail" class="ml-2 text-sm text-gray-700">
                                Send email notification to customer
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center">
                    <button type="button" onclick="previewPayment()" 
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-eye mr-2"></i>Preview Payment
                    </button>
                    
                    <button type="submit" 
                            class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>Send USSD Push
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Preview Modal -->
    <div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Payment Preview</h3>
                    <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div id="previewContent" class="space-y-3">
                    <!-- Preview content will be loaded here -->
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closePreview()" 
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button onclick="confirmPayment()" 
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-check mr-2"></i>Confirm & Send
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Result Modal -->
    <div id="resultModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Payment Result</h3>
                    <button onclick="closeResult()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div id="resultContent" class="space-y-3">
                    <!-- Result content will be loaded here -->
                </div>
                
                <div class="flex justify-end mt-6">
                    <button onclick="closeResult()" 
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-check mr-2"></i>Done
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let paymentData = null;

        function clearPhone() {
            document.getElementById('customerPhone').value = '';
        }

        function clearAmount() {
            document.getElementById('amount').value = '';
        }

        function closePreview() {
            document.getElementById('previewModal').classList.add('hidden');
        }

        function closeResult() {
            document.getElementById('resultModal').classList.add('hidden');
            document.getElementById('ussdForm').reset();
        }

        function previewPayment() {
            const customerName = document.getElementById('customerName').value;
            const customerPhone = document.getElementById('customerPhone').value;
            const customerEmail = document.getElementById('customerEmail').value;
            const amount = document.getElementById('amount').value;
            const description = document.getElementById('description').value;
            const orderReference = document.getElementById('orderReference').value;
            const fetchSenderDetails = document.getElementById('fetchSenderDetails').checked;
            const sendSMS = document.getElementById('sendSMS').checked;
            const sendEmail = document.getElementById('sendEmail').checked;

            if (!customerName || !customerPhone || !amount || !description) {
                showNotification('Please fill in all required fields', 'error');
                return;
            }

            if (!/^255[67]\d{8}$/.test(customerPhone)) {
                showNotification('Please enter a valid Tanzania phone number', 'error');
                return;
            }

            // Show loading state
            const previewContent = document.getElementById('previewContent');
            previewContent.innerHTML = `
                <div class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                    <p class="mt-2 text-gray-600">Validating payment details...</p>
                </div>
            `;

            document.getElementById('previewModal').classList.remove('hidden');

            // Call ClickPesa API for preview
            const previewData = {
                amount: amount,
                currency: 'TZS',
                orderReference: orderReference || 'FEEDTAN' + Date.now().toString(36).toUpperCase(),
                phoneNumber: customerPhone,
                fetchSenderDetails: fetchSenderDetails
            };

            // Get CSRF token safely
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token meta tag not found');
                showNotification('Security token not found. Please refresh the page.', 'error');
                return;
            }

            console.log('Sending preview request:', previewData);

            fetch('/api/preview-ussd-push', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                },
                body: JSON.stringify(previewData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    paymentData = {
                        customerName,
                        customerPhone,
                        customerEmail,
                        amount,
                        description,
                        orderReference: previewData.orderReference,
                        fetchSenderDetails,
                        sendSMS,
                        sendEmail,
                        apiPreview: data.data
                    };

                    previewContent.innerHTML = `
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Payment Details</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Customer Name:</span>
                                    <span class="font-medium">${paymentData.customerName}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Phone Number:</span>
                                    <span class="font-medium">${paymentData.customerPhone}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <span class="font-medium">${paymentData.customerEmail || 'Not provided'}</span>
                                </div>
                } else {
                    previewContent.innerHTML = `
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-red-100 rounded-full">
                                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-red-800 font-semibold">Preview Failed</h4>
                                    <p class="text-sm text-gray-600">${data.message || 'Failed to validate payment details'}</p>
                                </div>
                            </div>
                        </div>
                    `;
                    showNotification('Payment preview failed: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                previewContent.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="p-2 bg-red-100 rounded-full">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-red-800 font-semibold">API Error</h4>
                                <p class="text-sm text-gray-600">Failed to validate: ${error.message}</p>
                            </div>
                        </div>
                    </div>
                `;
                showNotification('API connection error: ' + error.message, 'error');
            });
        }

        function confirmPayment() {
            if (!paymentData) return;
            
            closePreview();
            
            // Show loading state
            const resultContent = document.getElementById('resultContent');
            resultContent.innerHTML = `
                <div class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                    <p class="mt-2 text-gray-600">Sending USSD Push via ClickPesa API...</p>
                </div>
            `;
            
            document.getElementById('resultModal').classList.remove('hidden');
            
            // Prepare data for ClickPesa API with enhanced validation
            const pushData = {
                amount: parseFloat(paymentData.amount),
                currency: 'TZS',
                orderReference: paymentData.orderReference || generateOrderReference(),
                phoneNumber: paymentData.customerPhone,
                customerName: paymentData.customerName,
                customerEmail: paymentData.customerEmail || null,
                description: paymentData.description || 'USSD Payment Collection'
            };
            
            // Get CSRF token safely
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token meta tag not found');
                showNotification('Security token not found. Please refresh the page.', 'error');
                return;
            }
            
            console.log('Sending USSD push request to ClickPesa API:', pushData);
            
            // Re-check API connectivity before sending
            fetch('/api/control-numbers/test-connection', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(connectionTest => {
                if (!connectionTest.success) {
                    resultContent.innerHTML = `
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-red-100 rounded-full">
                                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-red-800 font-semibold">API Connection Failed</h4>
                                    <p class="text-sm text-gray-600">Cannot connect to ClickPesa API: ${connectionTest.message}</p>
                                    <p class="text-sm text-gray-600">Please check your internet connection and try again.</p>
                                </div>
                            </div>
                        </div>
                    `;
                    showNotification('API connection failed. Please try again.', 'error');
                    return;
                }
                
                // API is connected, proceed with USSD push
                sendUSSDPush(pushData, csrfToken.getAttribute('content'), resultContent);
            })
            .catch(error => {
                resultContent.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="p-2 bg-red-100 rounded-full">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-red-800 font-semibold">Connection Test Failed</h4>
                                <p class="text-sm text-gray-600">Failed to test API connection: ${error.message}</p>
                            </div>
                        </div>
                    </div>
                `;
                showNotification('API connection test failed: ' + error.message, 'error');
            });
        }
        
        function sendUSSDPush(pushData, csrfToken, resultContent) {
            // Send USSD push request to ClickPesa API
            fetch('/api/payments/ussd-push', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(pushData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultContent.innerHTML = `
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-green-100 rounded-full">
                                    <i class="fas fa-check text-green-600"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-green-800 font-semibold">Payment Initiated Successfully</h4>
                                    <p class="text-sm text-gray-600">USSD Push has been sent to ${pushData.phoneNumber} via ClickPesa API</p>
                                    <div class="mt-2 text-sm">
                                        <p><strong>Transaction ID:</strong> ${data.data.id}</p>
                                        <p><strong>Amount:</strong> ${data.data.collectedAmount || pushData.amount} TZS</p>
                                        <p><strong>Status:</strong> ${data.data.status}</p>
                                        <p><strong>Channel:</strong> ${data.data.channel || 'Processing'}</p>
                                        <p><strong>Provider:</strong> ClickPesa</p>
                                        <p><strong>API Response:</strong> Success</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Update statistics
                    updatePaymentStats();
                    showNotification('Payment initiated successfully via ClickPesa API', 'success');
                    
                    // Reset form after successful payment
                    setTimeout(() => {
                        resetForm();
                    }, 3000);
                    
                } else {
                    resultContent.innerHTML = `
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-red-100 rounded-full">
                                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-red-800 font-semibold">Payment Failed</h4>
                                    <p class="text-sm text-gray-600">${data.message || 'Failed to initiate payment'}</p>
                                    <p class="text-sm text-gray-600">API Error Code: ${data.errorCode || 'UNKNOWN'}</p>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    showNotification('Payment failed: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                resultContent.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="p-2 bg-red-100 rounded-full">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-red-800 font-semibold">API Error</h4>
                                <p class="text-sm text-gray-600">Failed to initiate payment: ${error.message}</p>
                                <p class="text-sm text-gray-600">Please check your connection and try again.</p>
                            </div>
                        </div>
                    </div>
                `;
                
                showNotification('API error: ' + error.message, 'error');
            });
        }
        
        function generateOrderReference() {
            const timestamp = Date.now();
            const random = Math.floor(Math.random() * 1000);
            return 'FEEDTAN' + timestamp.toString().slice(-6) + random;
        }
        
        function updatePaymentStats() {
            // Update payment statistics (mock implementation)
            const todayElement = document.querySelector('.border-l-4.border-green-500 p');
            if (todayElement) {
                const currentText = todayElement.querySelector('p.text-2xl');
                if (currentText) {
                    const currentValue = parseInt(currentText.textContent.replace(/[^\d]/g, ''));
                    const newValue = currentValue + 1;
                    currentText.textContent = 'TZS ' + (newValue * 1000).toLocaleString();
                }
            }
        }

        function trackPayment(transactionId) {
            // Navigate to transaction details page
            window.location.href = `/payments/transaction/${transactionId}`;
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

        // Form submission
        document.getElementById('ussdForm').addEventListener('submit', function(e) {
            e.preventDefault();
            previewPayment();
        });

        document.addEventListener('DOMContentLoaded', function() {
    // Test API connectivity first
    testUssdAPIConnection();
    loadUSSDStatistics();
    setupFormValidation();
});

function testUssdAPIConnection() {
    const statusIndicator = document.getElementById('ussdApiStatus');
    const statusIcon = document.getElementById('ussdApiIcon');
    const statusText = document.getElementById('ussdApiText');
    
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
            showNotification('USSD API connection successful', 'success');
        } else {
            statusIndicator.className = 'mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800';
            statusIcon.className = 'fas fa-exclamation-triangle mr-2';
            statusText.textContent = 'API Disconnected - ' + data.message;
            showNotification('USSD API connection failed: ' + data.message, 'error');
        }
    })
    .catch(error => {
        statusIndicator.className = 'mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800';
        statusIcon.className = 'fas fa-exclamation-triangle mr-2';
        statusText.textContent = 'API Error - ' + error.message;
        showNotification('USSD API connection error: ' + error.message, 'error');
    });
}

        // Auto-format phone number
        document.getElementById('customerPhone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0 && !value.startsWith('255')) {
                value = '255' + value;
            }
            e.target.value = value;
        });
    </script>
@endsection