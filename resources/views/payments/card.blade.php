@extends('layouts.app')

@section('title', 'Card Payments - MUSARIS System')

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
                <li><span class="text-gray-700 font-medium">Card Payments</span></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Card Payments</h1>
                <p class="text-gray-600 mt-1">Process secure card payments with advanced verification</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-green-600 font-medium">Service Active</span>
                </div>
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    View History
                </button>
            </div>
        </div>
    </div>

    <!-- Payment Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <h3 class="text-sm font-medium text-gray-500">Today's Cards</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 890K</p>
            <p class="text-xs text-green-600 mt-1">89 transactions</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <h3 class="text-sm font-medium text-gray-500">This Week</h3>
            <p class="text-2xl font-bold text-gray-900">TZS 5.2M</p>
            <p class="text-xs text-blue-600 mt-1">567 transactions</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <h3 class="text-sm font-medium text-gray-500">Success Rate</h3>
            <p class="text-2xl font-bold text-gray-900">96.8%</p>
            <p class="text-xs text-yellow-600 mt-1">18 declined</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <h3 class="text-sm font-medium text-gray-500">3D Secure</h3>
            <p class="text-2xl font-bold text-gray-900">100%</p>
            <p class="text-xs text-purple-600 mt-1">All verified</p>
        </div>
    </div>

    <!-- Payment Form -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">New Card Payment</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Secure card processing with 3D verification</span>
                </div>
            </div>
        </div>
        <div class="p-6">
            <form id="cardForm" class="space-y-6">
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
                            <label for="customerEmail" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="customerEmail" name="customerEmail" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                   placeholder="customer@example.com" required>
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
                    </div>

                    <!-- Card Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Card Information</h3>
                        
                        <div>
                            <label for="cardNumber" class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                            <div class="relative">
                                <input type="text" id="cardNumber" name="cardNumber" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                       placeholder="1234 5678 9012 3456" maxlength="19" required>
                                <button type="button" onclick="clearCardNumber()" 
                                        class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Enter card number without spaces</p>
                        </div>

                        <div>
                            <label for="cardholderName" class="block text-sm font-medium text-gray-700 mb-2">Cardholder Name</label>
                            <input type="text" id="cardholderName" name="cardholderName" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                   placeholder="Name as on card" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="expiryMonth" class="block text-sm font-medium text-gray-700 mb-2">Expiry Month</label>
                                <select id="expiryMonth" name="expiryMonth" 
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                                    <option value="">MM</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                            </div>
                            <div>
                                <label for="expiryYear" class="block text-sm font-medium text-gray-700 mb-2">Expiry Year</label>
                                <select id="expiryYear" name="expiryYear" 
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                                    <option value="">YYYY</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="cvv" class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                            <div class="relative">
                                <input type="text" id="cvv" name="cvv" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                       placeholder="123" maxlength="4" required>
                                <button type="button" onclick="clearCVV()" 
                                        class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">3-4 digit security code</p>
                        </div>
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

                <!-- Security Options -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Security Options</h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input type="checkbox" id="enable3DS" name="enable3DS" checked
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="enable3DS" class="ml-2 text-sm text-gray-700">
                                Enable 3D Secure verification
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="saveCard" name="saveCard" 
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="saveCard" class="ml-2 text-sm text-gray-700">
                                Save card for future payments
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="sendReceipt" name="sendReceipt" checked
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="sendReceipt" class="ml-2 text-sm text-gray-700">
                                Send email receipt to customer
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center">
                    <button type="button" onclick="validateCard()" 
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-shield-alt mr-2"></i>Validate Card
                    </button>
                    
                    <button type="submit" 
                            class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-credit-card mr-2"></i>Process Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Validation Modal -->
    <div id="validationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Card Validation</h3>
                    <button onclick="closeValidation()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div id="validationContent" class="space-y-3">
                    <!-- Validation content will be loaded here -->
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeValidation()" 
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button onclick="proceedWithCard()" 
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-check mr-2"></i>Proceed
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Processing Modal -->
    <div id="processingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Processing Payment</h3>
                </div>
                
                <div class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                    <p class="mt-2 text-gray-600">Processing card payment...</p>
                    <p class="text-sm text-gray-500">Please do not close this window</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let paymentData = null;

        function clearPhone() {
            document.getElementById('customerPhone').value = '';
        }

        function clearCardNumber() {
            document.getElementById('cardNumber').value = '';
        }

        function clearCVV() {
            document.getElementById('cvv').value = '';
        }

        function clearAmount() {
            document.getElementById('amount').value = '';
        }

        function closeValidation() {
            document.getElementById('validationModal').classList.add('hidden');
        }

        function proceedWithCard() {
            closeValidation();
            // Proceed with payment processing
            processCardPayment();
        }

        function validateCard() {
            const cardNumber = document.getElementById('cardNumber').value;
            const cardholderName = document.getElementById('cardholderName').value;
            const expiryMonth = document.getElementById('expiryMonth').value;
            const expiryYear = document.getElementById('expiryYear').value;
            const cvv = document.getElementById('cvv').value;
            const customerName = document.getElementById('customerName').value;
            const customerEmail = document.getElementById('customerEmail').value;
            const customerPhone = document.getElementById('customerPhone').value;
            const amount = document.getElementById('amount').value;
            const description = document.getElementById('description').value;
            const orderReference = document.getElementById('orderReference').value;

            if (!cardNumber || !cardholderName || !expiryMonth || !expiryYear || !cvv || !customerName || !customerEmail || !customerPhone || !amount || !description) {
                alert('Please fill in all required fields');
                return;
            }

            // Validate card number (basic Luhn algorithm check)
            if (!validateCardNumber(cardNumber)) {
                showValidationError('Invalid card number');
                return;
            }

            // Validate expiry date
            const expiryDate = new Date(expiryYear, expiryMonth, 0);
            const today = new Date();
            if (expiryDate <= today) {
                showValidationError('Card has expired');
                return;
            }

            // Show validation success
            showValidationSuccess();
        }

        function validateCardNumber(cardNumber) {
            // Remove spaces and dashes
            const cleanNumber = cardNumber.replace(/[\s-]/g, '');
            
            // Check if it's numeric and valid length
            if (!/^\d+$/.test(cleanNumber) || cleanNumber.length < 13 || cleanNumber.length > 19) {
                return false;
            }

            // Basic Luhn algorithm for card validation
            let sum = 0;
            let isEven = false;
            
            for (let i = cleanNumber.length - 1; i >= 0; i--) {
                let digit = parseInt(cleanNumber.charAt(i), 10);
                
                if (isEven) {
                    digit *= 2;
                }
                
                sum += Math.floor(digit / 10);
                sum += (digit % 10);
                isEven = !isEven;
            }
            
            return (sum % 10) === 0;
        }

        function showValidationError(message) {
            const validationContent = document.getElementById('validationContent');
            validationContent.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-full">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-red-800 font-semibold">Validation Failed</h4>
                            <p class="text-sm text-gray-600">${message}</p>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('validationModal').classList.remove('hidden');
        }

        function showValidationSuccess() {
            const validationContent = document.getElementById('validationContent');
            validationContent.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-full">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-green-800 font-semibold">Card Validated Successfully</h4>
                            <p class="text-sm text-gray-600">Card is valid and ready for processing</p>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('validationModal').classList.remove('hidden');
        }

        function processCardPayment() {
            const processingModal = document.getElementById('processingModal');
            processingModal.classList.remove('hidden');

            // Collect payment data
            paymentData = {
                customerName: document.getElementById('customerName').value,
                customerEmail: document.getElementById('customerEmail').value,
                customerPhone: document.getElementById('customerPhone').value,
                cardNumber: document.getElementById('cardNumber').value,
                cardholderName: document.getElementById('cardholderName').value,
                expiryMonth: document.getElementById('expiryMonth').value,
                expiryYear: document.getElementById('expiryYear').value,
                cvv: document.getElementById('cvv').value,
                amount: document.getElementById('amount').value,
                description: document.getElementById('description').value,
                orderReference: document.getElementById('orderReference').value || 'FEEDTAN' + Date.now().toString(36).toUpperCase(),
                enable3DS: document.getElementById('enable3DS').checked,
                saveCard: document.getElementById('saveCard').checked,
                sendReceipt: document.getElementById('sendReceipt').checked
            };

            // Send payment request to API
            fetch('/payments/card-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(paymentData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success result
                    showPaymentResult('success', data.data);
                } else {
                    showPaymentResult('error', null, data.message || 'Payment processing failed');
                }
            })
            .catch(error => {
                showPaymentResult('error', null, 'Failed to process payment: ' + error.message);
            })
            .finally(() => {
                // Hide processing modal
                setTimeout(() => {
                    document.getElementById('processingModal').classList.add('hidden');
                }, 2000);
            });
        }

        function showPaymentResult(type, data, message) {
            const processingModal = document.getElementById('processingModal');
            const resultContent = processingModal.querySelector('.text-center');
            
            if (type === 'success') {
                resultContent.innerHTML = `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-full">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-green-800 font-semibold">Payment Successful</h4>
                                <p class="text-sm text-gray-600">Card payment processed successfully</p>
                                <div class="mt-2 text-sm">
                                    <p><strong>Transaction ID:</strong> ${data.id || 'N/A'}</p>
                                    <p><strong>Amount:</strong> ${data.amount || 'N/A'} TZS</p>
                                    <p><strong>Status:</strong> ${data.status || 'SUCCESS'}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                resultContent.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="p-2 bg-red-100 rounded-full">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-red-800 font-semibold">Payment Failed</h4>
                                <p class="text-sm text-gray-600">${message}</p>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Add close button after 3 seconds
            setTimeout(() => {
                const closeButton = document.createElement('button');
                closeButton.textContent = 'Close';
                closeButton.className = 'bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors mt-4';
                closeButton.onclick = function() {
                    document.getElementById('processingModal').classList.add('hidden');
                    document.getElementById('cardForm').reset();
                };
                resultContent.appendChild(closeButton);
            }, 3000);
        }

        // Format card number input
        document.getElementById('cardNumber').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            // Add spaces every 4 digits
            value = value.replace(/(.{4})/g, '$1 ');
            e.target.value = value;
        });

        // Populate expiry years dynamically
        const currentYear = new Date().getFullYear();
        const expiryYearSelect = document.getElementById('expiryYear');
        for (let i = 0; i <= 10; i++) {
            const year = currentYear + i;
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            expiryYearSelect.appendChild(option);
        }

        // Form submission
        document.getElementById('cardForm').addEventListener('submit', function(e) {
            e.preventDefault();
            validateCard();
        });
    </script>
@endsection
               