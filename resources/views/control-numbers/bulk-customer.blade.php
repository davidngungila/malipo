@extends('layouts.app')

@section('title', 'Bulk Customer Control Numbers - FEEDTAN System')

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
                <li class="text-gray-900 font-medium">Bulk Customer Control Numbers</li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bulk Customer Control Numbers</h1>
                <p class="text-gray-600 mt-1">Generate multiple BillPay control numbers for customers (max 50 per request)</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('control-numbers.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors inline-flex items-center mr-3">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Control Numbers
                </a>
                <button onclick="downloadTemplate()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors inline-flex items-center">
                    <i class="fas fa-download mr-2"></i>Download Template
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Generation Form -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Bulk Generate Control Numbers</h3>
            <p class="text-sm text-gray-500 mt-1">Add customer details below or upload a CSV file</p>
        </div>
        <div class="p-6">
            <!-- Input Method Selection -->
            <div class="mb-6">
                <div class="flex space-x-4">
                    <button onclick="showManualInput()" id="manualInputBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-keyboard mr-2"></i>Manual Input
                    </button>
                    <button onclick="showFileUpload()" id="fileUploadBtn" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-file-upload mr-2"></i>Upload CSV
                    </button>
                </div>
            </div>

            <!-- Manual Input Section -->
            <div id="manualInputSection">
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-md font-medium text-gray-900">Customer Control Numbers</h4>
                        <button onclick="addCustomerRow()" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-1"></i>Add Customer
                        </button>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name *</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount (TZS)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="customerTableBody" class="bg-white divide-y divide-gray-200">
                                <!-- Customer rows will be added here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Default Settings -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Default Payment Mode</label>
                        <select id="defaultPaymentMode" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="ALLOW_PARTIAL_AND_OVER_PAYMENT">Allow Partial & Over Payment</option>
                            <option value="EXACT">Exact Amount Only</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Default Amount (TZS)</label>
                        <input type="number" id="defaultAmount" min="100" step="0.01" placeholder="10000"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- File Upload Section -->
            <div id="fileUploadSection" class="hidden">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                    <div class="mb-4">
                        <i class="fas fa-file-csv text-4xl text-gray-400"></i>
                    </div>
                    <div class="mb-4">
                        <label for="csvFile" class="cursor-pointer">
                            <span class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                                <i class="fas fa-upload mr-2"></i>Choose CSV File
                            </span>
                            <input type="file" id="csvFile" accept=".csv" class="hidden" onchange="handleFileUpload(event)">
                        </label>
                    </div>
                    <p class="text-sm text-gray-500">
                        Upload a CSV file with columns: customerName, customerPhone, customerEmail, billAmount, billDescription, billReference
                    </p>
                </div>
                
                <div id="filePreview" class="mt-6 hidden">
                    <h4 class="text-md font-medium text-gray-900 mb-4">File Preview</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                </tr>
                            </thead>
                            <tbody id="filePreviewBody">
                                <!-- File preview rows will be added here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="resetAll()" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-redo mr-2"></i>Reset All
                </button>
                <button type="button" onclick="validateAndGenerate()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Generate Control Numbers
                </button>
            </div>
        </div>
    </div>

    <!-- Generation Results -->
    <div id="resultsSection" class="hidden">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Generation Results</h3>
            </div>
            <div class="p-6">
                <div id="resultsContent">
                    <!-- Results will be displayed here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let customerRows = [];
let uploadedData = [];

document.addEventListener('DOMContentLoaded', function() {
    // Add initial customer row
    addCustomerRow();
});

function showManualInput() {
    document.getElementById('manualInputSection').classList.remove('hidden');
    document.getElementById('fileUploadSection').classList.add('hidden');
    document.getElementById('manualInputBtn').className = 'px-4 py-2 bg-blue-600 text-white rounded-lg transition-colors';
    document.getElementById('fileUploadBtn').className = 'px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors';
}

function showFileUpload() {
    document.getElementById('manualInputSection').classList.add('hidden');
    document.getElementById('fileUploadSection').classList.remove('hidden');
    document.getElementById('manualInputBtn').className = 'px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors';
    document.getElementById('fileUploadBtn').className = 'px-4 py-2 bg-blue-600 text-white rounded-lg transition-colors';
}

function addCustomerRow() {
    const rowId = Date.now();
    const tbody = document.getElementById('customerTableBody');
    
    const row = document.createElement('tr');
    row.id = `row-${rowId}`;
    row.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap">
            <input type="text" name="customerName" required 
                   class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Customer name">
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <input type="tel" name="customerPhone" placeholder="255712345678"
                   class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <input type="email" name="customerEmail" placeholder="customer@example.com"
                   class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <input type="number" name="billAmount" min="100" step="0.01"
                   class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <input type="text" name="billDescription" required
                   class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Bill description">
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <input type="text" name="billReference"
                   class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Optional reference">
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <button onclick="removeCustomerRow('row-${rowId}')" class="text-red-600 hover:text-red-800">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    customerRows.push(rowId);
    
    // Add phone number formatting
    row.querySelector('input[name="customerPhone"]').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 0 && !value.startsWith('255')) {
            value = '255' + value;
        }
        e.target.value = value;
    });
}

function removeCustomerRow(rowId) {
    const row = document.getElementById(rowId);
    if (row) {
        row.remove();
        customerRows = customerRows.filter(id => id !== parseInt(rowId.split('-')[1]));
    }
}

function handleFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const csv = e.target.result;
            const lines = csv.split('\n');
            const headers = lines[0].split(',').map(h => h.trim());
            
            uploadedData = [];
            
            for (let i = 1; i < lines.length; i++) {
                if (lines[i].trim() === '') continue;
                
                const values = lines[i].split(',').map(v => v.trim());
                const row = {};
                
                headers.forEach((header, index) => {
                    row[header] = values[index] || '';
                });
                
                uploadedData.push(row);
            }
            
            displayFilePreview();
            showNotification('CSV file loaded successfully', 'success');
        } catch (error) {
            showNotification('Error parsing CSV file', 'error');
        }
    };
    reader.readAsText(file);
}

function displayFilePreview() {
    const previewSection = document.getElementById('filePreview');
    const previewBody = document.getElementById('filePreviewBody');
    
    previewSection.classList.remove('hidden');
    previewBody.innerHTML = uploadedData.slice(0, 10).map(row => `
        <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${row.customerName || 'N/A'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${row.customerPhone || 'N/A'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${row.customerEmail || 'N/A'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${row.billAmount || 'N/A'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${row.billDescription || 'N/A'}</td>
        </tr>
    `).join('');
    
    if (uploadedData.length > 10) {
        previewBody.innerHTML += `
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                    ... and ${uploadedData.length - 10} more rows
                </td>
            </tr>
        `;
    }
}

function validateAndGenerate() {
    const isManualInput = !document.getElementById('manualInputSection').classList.contains('hidden');
    
    if (isManualInput) {
        // Validate manual input
        const rows = document.querySelectorAll('#customerTableBody tr');
        const controlNumbers = [];
        
        rows.forEach((row, index) => {
            const customerName = row.querySelector('input[name="customerName"]').value;
            const customerPhone = row.querySelector('input[name="customerPhone"]').value;
            const customerEmail = row.querySelector('input[name="customerEmail"]').value;
            const billAmount = row.querySelector('input[name="billAmount"]').value;
            const billDescription = row.querySelector('input[name="billDescription"]').value;
            const billReference = row.querySelector('input[name="billReference"]').value;
            
            if (customerName && billDescription) {
                const controlNumber = {
                    customerName: customerName,
                    billDescription: billDescription
                };
                
                if (customerPhone) controlNumber.customerPhone = customerPhone;
                if (customerEmail) controlNumber.customerEmail = customerEmail;
                if (billAmount) controlNumber.billAmount = parseFloat(billAmount);
                if (billReference) controlNumber.billReference = billReference;
                
                controlNumbers.push(controlNumber);
            }
        });
        
        if (controlNumbers.length === 0) {
            showNotification('Please add at least one customer with required fields', 'error');
            return;
        }
        
        if (controlNumbers.length > 50) {
            showNotification('Maximum 50 control numbers can be generated at once', 'error');
            return;
        }
        
        generateBulkControlNumbers(controlNumbers);
    } else {
        // Use uploaded data
        if (uploadedData.length === 0) {
            showNotification('Please upload a CSV file first', 'error');
            return;
        }
        
        if (uploadedData.length > 50) {
            showNotification('Maximum 50 control numbers can be generated at once', 'error');
            return;
        }
        
        generateBulkControlNumbers(uploadedData);
    }
}

function generateBulkControlNumbers(controlNumbers) {
    const resultsSection = document.getElementById('resultsSection');
    const resultsContent = document.getElementById('resultsContent');
    
    resultsSection.classList.remove('hidden');
    resultsContent.innerHTML = `
        <div class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            <p class="mt-2 text-gray-600">Generating ${controlNumbers.length} control numbers...</p>
        </div>
    `;
    
    fetch('/api/control-numbers/bulk-create-customer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ controlNumbers: controlNumbers })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayBulkResults(data.data);
            showNotification(`Successfully generated ${data.data.created} control numbers`, 'success');
        } else {
            resultsContent.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-full">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-red-800 font-semibold">Generation Failed</h4>
                            <p class="text-sm text-gray-600">${data.message || 'Failed to generate control numbers'}</p>
                        </div>
                    </div>
                </div>
            `;
            showNotification(data.message || 'Failed to generate control numbers', 'error');
        }
    })
    .catch(error => {
        console.error('Error generating bulk control numbers:', error);
        resultsContent.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-full">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-red-800 font-semibold">Error</h4>
                        <p class="text-sm text-gray-600">Failed to generate control numbers: ${error.message}</p>
                    </div>
                </div>
            </div>
        `;
        showNotification('Failed to generate control numbers', 'error');
    });
}

function displayBulkResults(data) {
    const resultsContent = document.getElementById('resultsContent');
    
    let html = `
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
            <div class="flex items-center mb-4">
                <div class="p-2 bg-green-100 rounded-full">
                    <i class="fas fa-check text-green-600"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-green-800 font-semibold">Bulk Generation Completed</h4>
                    <p class="text-sm text-gray-600">Successfully processed ${data.created + data.failed} requests</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">${data.created}</div>
                    <div class="text-sm text-gray-600">Successfully Created</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">${data.failed}</div>
                    <div class="text-sm text-gray-600">Failed</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">${data.created + data.failed}</div>
                    <div class="text-sm text-gray-600">Total Processed</div>
                </div>
            </div>
        </div>
    `;
    
    if (data.billPayNumbers && data.billPayNumbers.length > 0) {
        html += `
            <div class="mb-6">
                <h4 class="text-md font-medium text-gray-900 mb-4">Generated Control Numbers</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">BillPay Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${data.billPayNumbers.map((number, index) => `
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        ${number}
                                        <button onclick="copyToClipboard('${number}')" class="ml-2 text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Customer ${index + 1}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">-</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">-</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <button onclick="viewDetails('${number}')" class="text-blue-600 hover:text-blue-800 mr-2">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="downloadPDF('${number}')" class="text-green-600 hover:text-green-800">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }
    
    if (data.errors && data.errors.length > 0) {
        html += `
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h4 class="text-yellow-800 font-semibold mb-2">Errors (${data.errors.length})</h4>
                <div class="space-y-2">
                    ${data.errors.map(error => `
                        <div class="text-sm text-yellow-700">
                            Row ${error.index + 1}: ${error.reason}
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }
    
    html += `
        <div class="flex justify-end space-x-4 mt-6">
            <button onclick="downloadResults()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-download mr-2"></i>Download Results
            </button>
            <button onclick="resetAll()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fas fa-redo mr-2"></i>Start New Batch
            </button>
        </div>
    `;
    
    resultsContent.innerHTML = html;
}

function downloadTemplate() {
    const csvContent = `customerName,customerPhone,customerEmail,billAmount,billDescription,billReference
John Doe,255712345678,john@example.com,10000,Water Bill - July 2024,WATER001
Jane Smith,255713345678,jane@example.com,15000,Electricity Bill - July 2024,ELEC001`;
    
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'customer_control_numbers_template.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
    
    showNotification('Template downloaded successfully', 'success');
}

function resetAll() {
    // Reset manual input
    document.getElementById('customerTableBody').innerHTML = '';
    customerRows = [];
    addCustomerRow();
    
    // Reset file upload
    document.getElementById('csvFile').value = '';
    document.getElementById('filePreview').classList.add('hidden');
    uploadedData = [];
    
    // Hide results
    document.getElementById('resultsSection').classList.add('hidden');
    
    // Reset to manual input
    showManualInput();
    
    showNotification('Form reset successfully', 'success');
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Control number copied to clipboard!', 'success');
    }).catch(() => {
        showNotification('Failed to copy to clipboard', 'error');
    });
}

function viewDetails(billPayNumber) {
    window.location.href = `/control-numbers/view/${billPayNumber}`;
}

function downloadPDF(billPayNumber) {
    // Implement PDF download functionality
    showNotification('PDF download feature coming soon', 'info');
}

function downloadResults() {
    showNotification('Results download feature coming soon', 'info');
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
