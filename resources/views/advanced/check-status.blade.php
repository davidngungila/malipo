@extends('layouts.app')

@section('title', 'Check Payment Status')
@section('content')
<div class="flex-1 flex flex-col min-w-0">
    <div class="p-3 lg:p-6 overflow-auto bg-gray-50" style="height: calc(100vh - 160px);">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Check Payment Status</h1>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">Track and verify payment transactions</span>
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-sm text-green-600 font-medium">Service Active</span>
                        </div>
                    </div>

                    <!-- Status Check Form -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Status Inquiry</h2>
                        <form id="statusCheckForm" class="space-y-4">
                            <div>
                                <label for="reference" class="block text-sm font-medium text-gray-700 mb-2">Transaction Reference</label>
                                <div class="mt-1 relative">
                                    <input type="text" id="reference" name="reference" 
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                           placeholder="Enter transaction reference (e.g., FEEDTAN123456789)">
                                    <button type="button" onclick="clearReference()" 
                                            class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <div class="mt-1 relative">
                                    <input type="tel" id="phone" name="phone" 
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                           placeholder="Enter phone number (e.g., 255712345678)">
                                    <button type="button" onclick="clearPhone()" 
                                            class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="flex justify-center">
                                <button type="submit" form="statusCheckForm" 
                                        class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                                    <i class="fas fa-search mr-2"></i>Check Status
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Status Result -->
                    <div id="statusResult" class="hidden">
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Payment Status Details</h3>
                                <button onclick="printStatus()" class="text-green-600 hover:text-green-800">
                                    <i class="fas fa-print mr-2"></i>Print
                                </button>
                            </div>
                            
                            <div id="statusContent" class="space-y-4">
                                <!-- Status will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function clearReference() {
            document.getElementById('reference').value = '';
        }

        function clearPhone() {
            document.getElementById('phone').value = '';
        }

        function printStatus() {
            window.print();
        }

        // Check payment status via API
        document.getElementById('statusCheckForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const reference = document.getElementById('reference').value;
            const phone = document.getElementById('phone').value;
            const resultDiv = document.getElementById('statusResult');
            const statusContent = document.getElementById('statusContent');
            
            if (!reference && !phone) {
                alert('Please enter either transaction reference or phone number');
                return;
            }

            // Show loading state
            resultDiv.classList.remove('hidden');
            statusContent.innerHTML = `
                <div class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                    <p class="mt-2 text-gray-600">Checking payment status...</p>
                </div>
            `;

            // Make API call
            fetch('/advanced/check-payment-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    reference: reference,
                    phone: phone
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusContent.innerHTML = \`
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 bg-green-100 rounded-full">
                                        <i class="fas fa-check text-green-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-green-800 font-semibold">Payment Successful</h4>
                                        <p class="text-sm text-gray-600">Transaction completed successfully</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <h4 class="text-gray-900 font-semibold mb-3">Transaction Details</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Transaction ID:</span>
                                        <span class="text-sm font-medium text-gray-900">\${data.data.id || 'N/A'}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Status:</span>
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">\${data.data.status || 'N/A'}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Amount:</span>
                                        <span class="text-sm font-medium text-gray-900">\${data.data.amount || 'N/A'} TZS</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Currency:</span>
                                        <span class="text-sm font-medium text-gray-900">\${data.data.currency || 'N/A'}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Created:</span>
                                        <span class="text-sm font-medium text-gray-900">\${data.data.createdAt || 'N/A'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    \`;
                } else {
                    statusContent.innerHTML = \`
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-red-100 rounded-full">
                                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-red-800 font-semibold">Payment Not Found</h4>
                                    <p class="text-sm text-gray-600">\${data.message || 'Payment transaction not found'}</p>
                                </div>
                            </div>
                        </div>
                    \`;
                })
            .catch(error => {
                    statusContent.innerHTML = \`
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-red-100 rounded-full">
                                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-red-800 font-semibold">Error</h4>
                                    <p class="text-sm text-gray-600">Failed to check payment status: \${error.message}</p>
                                </div>
                            </div>
                        </div>
                    \`;
                });
        });
    </script>
@endsection
