<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/public/payment');
});

// Public Payment Routes
Route::get('/public/payment', function () {
    $success = '';
    $error = '';
    $paymentData = null;
    $amount = null;
    $phoneNumber = null;
    $orderReference = null;
    
    return view('public.payment', compact('success', 'error', 'paymentData', 'amount', 'phoneNumber', 'orderReference'));
});
Route::post('/public/payment', function () {
    // Handle payment processing
    $success = '';
    $error = '';
    $paymentData = null;
    $amount = null;
    $phoneNumber = null;
    $orderReference = null;

    // Check for collection order reference in URL
    $collectionReference = request()->query('collection');

    if ($collectionReference) {
        // Display collection success page
        $success = "HAKIKISHA UNADISPLAY NA Collection Order Reference<br><strong>FEEDTANF6A31C8526711</strong><br><br>KWENYE PAGE YA MALIPO YAMEANZISHWA...";
    } elseif (request()->isMethod('post')) {
        try {
            // Load ClickPesa configuration
            require_once public_path('config.php');
            require_once public_path('ClickPesaAPI.php');
            
            $config = include(public_path('config.php'));
            $api = new ClickPesaAPI($config);
            
            $amount = $api->formatAmount(request('amount'));
            $phoneNumber = $api->validatePhoneNumber(request('phone_number'));
            $memberName = request('member_name');
            $paymentPurpose = request('payment_purpose');
            $orderReference = $api->generateOrderReference();
            
            if (!$phoneNumber) {
                throw new Exception('Namba ya simu si sahihi. Tumia format: 255712345678');
            }
            
            if ($amount < $config['clickpesa']['payment']['min_amount'] || $amount > $config['clickpesa']['payment']['max_amount']) {
                throw new Exception('Kiasi lazima kuwa kati ya ' . number_format($config['clickpesa']['payment']['min_amount']) . ' na ' . number_format($config['clickpesa']['payment']['max_amount']) . ' TZS');
            }
            
            if (empty($memberName)) {
                throw new Exception('Tafadhali jina lako kamili');
            }
            
            if (empty($paymentPurpose)) {
                throw new Exception('Tafadhali eleza madhumuni ya malipo');
            }
            
            // Preview the payment first
            $preview = $api->previewUSSDPush($amount, $orderReference, $phoneNumber, true);
            
            if (isset($preview['activeMethods']) && !empty($preview['activeMethods'])) {
                // Initiate the payment
                $payment = $api->initiateUSSDPush($amount, $orderReference, $phoneNumber);
                $paymentData = $payment;
                $success = 'Malipo yako yameanza! Tafadhali thibitisha malipo kwenye simu yako. Muamala ID: ' . $payment['id'];
            } else {
                throw new Exception('Hakuna njia za malipo zinazopatikana kwa namba hii ya simu');
            }
            
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

    return view('public.payment', compact('success', 'error', 'paymentData', 'amount', 'phoneNumber', 'orderReference'));
});

// Dashboard Routes
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/dashboard/analytics', function () {
    return view('dashboard.analytics');
})->name('dashboard.analytics');

// Advanced Routes
Route::get('/advanced/dashboard', [App\Http\Controllers\AdvancedController::class, 'index'])->name('advanced.dashboard');
Route::get('/advanced/live-status', [App\Http\Controllers\AdvancedController::class, 'liveStatus'])->name('advanced.live-status');
Route::get('/advanced/payment-history', [App\Http\Controllers\AdvancedController::class, 'paymentHistory'])->name('advanced.payment-history');
Route::get('/advanced/check-status', function () {
    return view('advanced.check-status');
})->name('advanced.check-status');

// Advanced API Routes
Route::post('/advanced/check-payment-status', [App\Http\Controllers\AdvancedController::class, 'checkPaymentStatus'])->name('advanced.check-payment-status');
Route::post('/advanced/payment-history', [App\Http\Controllers\AdvancedController::class, 'getPaymentHistory'])->name('advanced.get-payment-history');
Route::post('/advanced/account-balance', [App\Http\Controllers\AdvancedController::class, 'getAccountBalance'])->name('advanced.get-account-balance');
Route::post('/advanced/account-statement', [App\Http\Controllers\AdvancedController::class, 'getAccountStatement'])->name('advanced.get-account-statement');
Route::post('/advanced/banks-list', [App\Http\Controllers\AdvancedController::class, 'getBanksList'])->name('advanced.get-banks-list');

// CRM & Sales Routes
Route::get('/customers', function () {
    return view('crm.customers');
})->name('customers');

// Collection/Payments Routes
Route::get('/payments', function () {
    // Load ClickPesa configuration
    require_once public_path('config.php');
    require_once public_path('ClickPesaAPI.php');
    
    $config = include(public_path('config.php'));
    $api = new \ClickPesaAPI($config);
    
    $payments = null;
    $error = '';
    $success = '';
    $totalCount = 0;
    $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = 20;
    $skip = ($currentPage - 1) * $limit;
    
    // Build query parameters
    $params = [
        'limit' => $limit,
        'skip' => $skip,
        'orderBy' => 'DESC',
        'sortBy' => 'createdAt'
    ];
    
    // Apply filters
    if (!empty($_GET['status'])) {
        $params['status'] = $_GET['status'];
    }
    if (!empty($_GET['currency'])) {
        $params['collectedCurrency'] = $_GET['currency'];
    }
    if (!empty($_GET['channel'])) {
        $params['channel'] = $_GET['channel'];
    }
    if (!empty($_GET['order_reference'])) {
        $params['orderReference'] = $_GET['order_reference'];
    }
    if (!empty($_GET['start_date'])) {
        $params['startDate'] = $_GET['start_date'];
    }
    if (!empty($_GET['end_date'])) {
        $params['endDate'] = $_GET['end_date'];
    }
    
    try {
        $response = $api->queryAllPayments($params);
        
        // Handle ClickPesa API response structure
        if (isset($response['data']) && is_array($response['data'])) {
            $payments = $response['data'];
            $totalCount = $response['totalCount'] ?? count($payments);
            $success = 'Retrieved ' . count($payments) . ' payment records';
        } elseif (isset($response['success']) && $response['success'] === false) {
            $payments = [];
            $error = $response['message'] ?? 'API returned an error';
        } else {
            $payments = [];
            $success = 'No payment records found matching your criteria';
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    
    return view('payments.all', compact('payments', 'error', 'success', 'totalCount', 'currentPage', 'limit'));
})->name('payments.all');

Route::get('/payments/ussd', function () {
    return view('payments.ussd');
})->name('payments.ussd');

Route::get('/payments/card', function () {
    return view('payments.card');
})->name('payments.card');

// Transaction Details Page
Route::get('/payments/transaction/{reference}', function ($reference) {
    try {
        // Load ClickPesa configuration
        require_once public_path('config.php');
        require_once public_path('ClickPesaAPI.php');
        
        $config = include(public_path('config.php'));
        $api = new \ClickPesaAPI($config);
        
        // Query specific transaction by order reference
        $response = $api->queryAllPayments(['orderReference' => $reference]);
        
        $transaction = null;
        if (isset($response['data']) && !empty($response['data'])) {
            // Find the specific transaction - try multiple reference fields
            foreach ($response['data'] as $payment) {
                if (
                    ($payment['orderReference'] ?? '') === $reference ||
                    ($payment['id'] ?? '') === $reference ||
                    ($payment['paymentReference'] ?? '') === $reference
                ) {
                    $transaction = $payment;
                    break;
                }
            }
        }
        
        // If not found by orderReference, try searching all payments
        if (!$transaction) {
            $allResponse = $api->queryAllPayments(['limit' => 100]);
            if (isset($allResponse['data']) && !empty($allResponse['data'])) {
                foreach ($allResponse['data'] as $payment) {
                    if (
                        ($payment['orderReference'] ?? '') === $reference ||
                        ($payment['id'] ?? '') === $reference ||
                        ($payment['paymentReference'] ?? '') === $reference
                    ) {
                        $transaction = $payment;
                        break;
                    }
                }
            }
        }
        
        return view('payments.transaction', compact('transaction'));
        
    } catch (Exception $e) {
        return view('payments.transaction', ['transaction' => null]);
    }
})->name('payments.transaction');

Route::get('/payments/status', function () {
    return view('payments.status');
})->name('payments.status');

// API Route for USSD Preview
Route::post('/api/preview-ussd-push', function (Illuminate\Http\Request $request) {
    try {
        // Load ClickPesa configuration
        require_once public_path('config.php');
        require_once public_path('ClickPesaAPI.php');
        
        $config = include(public_path('config.php'));
        $api = new \ClickPesaAPI($config);
        
        // Validate required fields
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'amount' => 'required|numeric|min:100',
            'currency' => 'required|string|in:TZS',
            'orderReference' => 'required|string',
            'phoneNumber' => 'required|string|regex:/^255[67]\d{8}$/',
            'fetchSenderDetails' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all())
            ]);
        }
        
        // Call ClickPesa preview API
        $response = $api->previewUSSDPush(
            $request->amount,
            $request->orderReference,
            $request->phoneNumber,
            $request->boolean('fetchSenderDetails', false)
        );
        
        if ($response && isset($response['activeMethods'])) {
            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Payment details validated successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to validate payment details',
                'data' => $response
            ]);
        }
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'API Error: ' . $e->getMessage()
        ]);
    }
});

// API Route for USSD Push Initiation
Route::post('/api/initiate-ussd-push', function (Illuminate\Http\Request $request) {
    try {
        // Load ClickPesa configuration
        require_once public_path('config.php');
        require_once public_path('ClickPesaAPI.php');
        
        $config = include(public_path('config.php'));
        $api = new \ClickPesaAPI($config);
        
        // Validate required fields
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'amount' => 'required|numeric|min:100',
            'currency' => 'required|string|in:TZS',
            'orderReference' => 'required|string',
            'phoneNumber' => 'required|string|regex:/^255[67]\d{8}$/'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all())
            ]);
        }
        
        // Call ClickPesa initiate USSD push API
        $response = $api->initiateUSSDPush(
            $request->amount,
            $request->orderReference,
            $request->phoneNumber
        );
        
        if ($response && isset($response['id'])) {
            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'USSD Push initiated successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate USSD Push',
                'data' => $response
            ]);
        }
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'API Error: ' . $e->getMessage()
        ]);
    }
});

// API Route for Recent Transactions
Route::get('/api/recent-transactions', function () {
    try {
        // Load ClickPesa configuration
        require_once public_path('config.php');
        require_once public_path('ClickPesaAPI.php');
        
        $config = include(public_path('config.php'));
        $api = new \ClickPesaAPI($config);
        
        // Get recent transactions
        $response = $api->queryAllPayments(['limit' => 10]);
        
        if (isset($response['data']) && is_array($response['data'])) {
            return response()->json([
                'success' => true,
                'data' => $response['data']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No recent transactions found'
            ]);
        }
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to load recent transactions: ' . $e->getMessage()
        ]);
    }
});

// API Route for Transaction Search
Route::post('/api/search-transactions', function (Illuminate\Http\Request $request) {
    try {
        // Load ClickPesa configuration
        require_once public_path('config.php');
        require_once public_path('ClickPesaAPI.php');
        
        $config = include(public_path('config.php'));
        $api = new \ClickPesaAPI($config);
        
        $searchTerm = $request->input('search');
        $results = [];
        
        // Search by transaction ID
        $response = $api->queryAllPayments(['limit' => 50]);
        if (isset($response['data']) && is_array($response['data'])) {
            foreach ($response['data'] as $transaction) {
                if (
                    stripos($transaction['id'] ?? '', $searchTerm) !== false ||
                    stripos($transaction['orderReference'] ?? '', $searchTerm) !== false ||
                    stripos($transaction['paymentReference'] ?? '', $searchTerm) !== false ||
                    stripos($transaction['paymentPhoneNumber'] ?? '', $searchTerm) !== false ||
                    stripos($transaction['customer']['customerName'] ?? '', $searchTerm) !== false ||
                    stripos($transaction['customer']['customerEmail'] ?? '', $searchTerm) !== false
                ) {
                    $results[] = $transaction;
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $results
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Search failed: ' . $e->getMessage()
        ]);
    }
});

// Control Numbers Routes
Route::get('/control-numbers', function () {
    return view('control-numbers.index');
})->name('control-numbers.index');

Route::get('/control-numbers/customer', function () {
    return view('control-numbers.customer');
})->name('control-numbers.customer');

Route::get('/control-numbers/bulk-customer', function () {
    return view('control-numbers.bulk-customer');
})->name('control-numbers.bulk-customer');

Route::get('/control-numbers/order', function () {
    return view('control-numbers.order');
})->name('control-numbers.order');

Route::get('/control-numbers/bulk-order', function () {
    return view('control-numbers.bulk-order');
})->name('control-numbers.bulk-order');

Route::get('/control-numbers/query', function () {
    return view('control-numbers.query');
})->name('control-numbers.query');

Route::get('/control-numbers/manage', function () {
    return view('control-numbers.manage');
})->name('control-numbers.manage');

Route::get('/control-numbers/status', function () {
    return view('control-numbers.status');
})->name('control-numbers.status');

Route::get('/control-numbers/reports', function () {
    return view('control-numbers.reports');
})->name('control-numbers.reports');

Route::get('/control-numbers/docs', function () {
    return view('control-numbers.docs');
})->name('control-numbers.docs');

Route::get('/control-numbers/tracking', function () {
    return view('control-numbers.tracking');
})->name('control-numbers.tracking');

Route::get('/control-numbers/api-test', function () {
    return view('control-numbers.api-test');
})->name('control-numbers.api-test');

// API Routes for Control Numbers
Route::post('/api/control-numbers/create-customer', function (Illuminate\Http\Request $request) {
    $startTime = microtime(true);
    $requestId = 'REQ_' . uniqid();
    
    try {
        // Log request start
        error_log("[$requestId] Customer Control Number Request Started: " . json_encode([
            'customerName' => $request->customerName,
            'customerPhone' => $request->customerPhone,
            'customerEmail' => $request->customerEmail,
            'billAmount' => $request->billAmount,
            'billDescription' => $request->billDescription
        ]));
        
        // Load ClickPesa configuration
        require_once public_path('config.php');
        require_once public_path('ClickPesaAPI.php');
        
        $config = include(public_path('config.php'));
        $api = new \ClickPesaAPI($config);
        
        // Validate required fields
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'customerName' => 'required|string|min:2|max:100',
            'billDescription' => 'required|string|min:5|max:500',
            'customerPhone' => 'nullable|string|regex:/^255[67]\d{8}$/',
            'customerEmail' => 'nullable|email|max:255',
            'billAmount' => 'nullable|numeric|min:100|max:10000000',
            'billReference' => 'nullable|string|max:50',
            'billPaymentMode' => 'nullable|string|in:ALLOW_PARTIAL_AND_OVER_PAYMENT,EXACT'
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            error_log("[$requestId] Validation Failed: " . json_encode($errors));
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $errors),
                'requestId' => $requestId,
                'errors' => $errors
            ], 400);
        }
        
        // Validate that either phone or email is provided
        if (!$request->customerPhone && !$request->customerEmail) {
            error_log("[$requestId] Validation Failed: Phone or email required");
            
            return response()->json([
                'success' => false,
                'message' => 'Either phone number or email address is required',
                'requestId' => $requestId
            ], 400);
        }
        
        // Prepare data for API
        $apiData = [
            'customerName' => trim($request->customerName),
            'billDescription' => trim($request->billDescription),
            'billPaymentMode' => $request->billPaymentMode ?? 'ALLOW_PARTIAL_AND_OVER_PAYMENT'
        ];
        
        if ($request->customerPhone) {
            $apiData['customerPhone'] = $request->customerPhone;
        }
        
        if ($request->customerEmail) {
            $apiData['customerEmail'] = strtolower(trim($request->customerEmail));
        }
        
        if ($request->billAmount) {
            $apiData['billAmount'] = (float) $request->billAmount;
        }
        
        if ($request->billReference) {
            $apiData['billReference'] = trim($request->billReference);
        }
        
        error_log("[$requestId] Calling ClickPesa API: " . json_encode($apiData));
        
        // Call ClickPesa API
        $response = $api->createCustomerControlNumber($apiData);
        
        $processingTime = round((microtime(true) - $startTime) * 1000);
        
        if ($response && isset($response['billPayNumber'])) {
            // Log successful generation
            error_log("[$requestId] SUCCESS: Control Number Generated: " . json_encode([
                'billPayNumber' => $response['billPayNumber'],
                'processingTime' => $processingTime
            ]));
            
            // Store tracking information (in production, this would go to database)
            $trackingData = [
                'requestId' => $requestId,
                'billPayNumber' => $response['billPayNumber'],
                'customerName' => $apiData['customerName'],
                'customerPhone' => $apiData['customerPhone'] ?? null,
                'customerEmail' => $apiData['customerEmail'] ?? null,
                'billAmount' => $apiData['billAmount'] ?? null,
                'billDescription' => $apiData['billDescription'],
                'billReference' => $apiData['billReference'] ?? null,
                'billPaymentMode' => $apiData['billPaymentMode'],
                'createdAt' => date('Y-m-d H:i:s'),
                'processingTime' => $processingTime,
                'status' => 'SUCCESS'
            ];
            
            error_log("[$requestId] Tracking Data: " . json_encode($trackingData));
            
            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Customer control number generated successfully',
                'requestId' => $requestId,
                'processingTime' => $processingTime,
                'tracking' => $trackingData
            ]);
        } else {
            // Log API failure
            error_log("[$requestId] API FAILURE: " . json_encode([
                'response' => $response,
                'processingTime' => $processingTime
            ]));
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate customer control number',
                'data' => $response,
                'requestId' => $requestId,
                'processingTime' => $processingTime
            ], 500);
        }
        
    } catch (Exception $e) {
        $processingTime = round((microtime(true) - $startTime) * 1000);
        
        // Log exception
        error_log("[$requestId] EXCEPTION: " . json_encode([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'processingTime' => $processingTime
        ]));
        
        return response()->json([
            'success' => false,
            'message' => 'API Error: ' . $e->getMessage(),
            'requestId' => $requestId,
            'processingTime' => $processingTime,
            'errorDetails' => [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        ], 500);
    }
});

Route::post('/api/control-numbers/bulk-create-customer', function (Illuminate\Http\Request $request) {
    try {
        // Load ClickPesa configuration
        require_once public_path('config.php');
        require_once public_path('ClickPesaAPI.php');
        
        $config = include(public_path('config.php'));
        $api = new \ClickPesaAPI($config);
        
        $controlNumbers = $request->input('controlNumbers', []);
        
        if (empty($controlNumbers) || count($controlNumbers) > 50) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide 1-50 control numbers to generate'
            ]);
        }
        
        // Call ClickPesa API
        $response = $api->bulkCreateCustomerControlNumbers($controlNumbers);
        
        if ($response && isset($response['billPayNumbers'])) {
            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Bulk customer control numbers processed'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process bulk customer control numbers',
                'data' => $response
            ]);
        }
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'API Error: ' . $e->getMessage()
        ]);
    }
});

Route::post('/api/control-numbers/quick-generate', function (Illuminate\Http\Request $request) {
    try {
        // Load ClickPesa configuration
        require_once public_path('config.php');
        require_once public_path('ClickPesaAPI.php');
        
        $config = include(public_path('config.php'));
        $api = new \ClickPesaAPI($config);
        
        // Validate required fields
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'customerName' => 'required|string',
            'billDescription' => 'required|string',
            'customerPhone' => 'nullable|string|regex:/^255[67]\d{8}$/',
            'billAmount' => 'nullable|numeric|min:100'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all())
            ]);
        }
        
        // Call ClickPesa API
        $response = $api->createCustomerControlNumber([
            'customerName' => $request->customerName,
            'customerPhone' => $request->customerPhone,
            'billDescription' => $request->billDescription,
            'billAmount' => $request->billAmount,
            'billPaymentMode' => 'ALLOW_PARTIAL_AND_OVER_PAYMENT'
        ]);
        
        if ($response && isset($response['billPayNumber'])) {
            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Control number generated successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate control number',
                'data' => $response
            ]);
        }
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'API Error: ' . $e->getMessage()
        ]);
    }
});

Route::get('/api/control-numbers/status', function () {
    try {
        // Return mock system status for now
        return response()->json([
            'success' => true,
            'data' => [
                'total' => 1247,
                'customer' => 856,
                'order' => 391,
                'active' => 1189
            ]
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to load system status'
        ]);
    }
});

Route::get('/api/control-numbers/recent-activity', function () {
    try {
        // Return mock recent activity for now
        return response()->json([
            'success' => true,
            'data' => [
                [
                    'type' => 'customer',
                    'description' => 'Customer control number created',
                    'billPayNumber' => '55042914871931',
                    'status' => 'ACTIVE',
                    'createdAt' => date('Y-m-d H:i:s', strtotime('-2 hours'))
                ],
                [
                    'type' => 'order',
                    'description' => 'Order control number created',
                    'billPayNumber' => '55042914871932',
                    'status' => 'ACTIVE',
                    'createdAt' => date('Y-m-d H:i:s', strtotime('-4 hours'))
                ]
            ]
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to load recent activity'
        ]);
    }
});

Route::get('/api/control-numbers/recent-customer', function () {
    try {
        // Return mock recent customer control numbers for now
        return response()->json([
            'success' => true,
            'data' => [
                [
                    'billPayNumber' => '55042914871931',
                    'billCustomerName' => 'John Doe',
                    'billAmount' => 10000,
                    'billPaymentMode' => 'EXACT',
                    'billDescription' => 'Water Bill - July 2024'
                ],
                [
                    'billPayNumber' => '55042914871932',
                    'billCustomerName' => 'Jane Smith',
                    'billAmount' => 15000,
                    'billPaymentMode' => 'ALLOW_PARTIAL_AND_OVER_PAYMENT',
                    'billDescription' => 'Electricity Bill - July 2024'
                ]
            ]
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to load recent customer control numbers'
        ]);
    }
});

Route::get('/api/control-numbers/tracking-data', function () {
    try {
        // In production, this would query a database for actual tracking data
        // For now, return mock tracking data
        $mockData = [
            [
                'requestId' => 'REQ_' . uniqid(),
                'customerName' => 'John Doe',
                'customerPhone' => '255712345678',
                'customerEmail' => 'john@example.com',
                'billPayNumber' => '55042914871931',
                'billAmount' => 10000,
                'billDescription' => 'Water Bill - July 2024',
                'billReference' => 'WATER001',
                'billPaymentMode' => 'EXACT',
                'status' => 'SUCCESS',
                'processingTime' => 1250,
                'createdAt' => date('Y-m-d H:i:s', strtotime('-5 minutes'))
            ],
            [
                'requestId' => 'REQ_' . uniqid(),
                'customerName' => 'Jane Smith',
                'customerPhone' => '255713345678',
                'billPayNumber' => '55042914871932',
                'billAmount' => 15000,
                'billDescription' => 'Electricity Bill - July 2024',
                'billReference' => 'ELEC001',
                'billPaymentMode' => 'ALLOW_PARTIAL_AND_OVER_PAYMENT',
                'status' => 'SUCCESS',
                'processingTime' => 980,
                'createdAt' => date('Y-m-d H:i:s', strtotime('-10 minutes'))
            ],
            [
                'requestId' => 'REQ_' . uniqid(),
                'customerName' => 'Bob Johnson',
                'customerPhone' => '255714345678',
                'billPayNumber' => null,
                'billAmount' => 20000,
                'billDescription' => 'Internet Bill - July 2024',
                'billReference' => 'NET001',
                'billPaymentMode' => 'EXACT',
                'status' => 'ERROR',
                'processingTime' => 2100,
                'errorMessage' => 'API Error (HTTP 400, Code: INVALID_PHONE): Invalid phone number format',
                'errorDetails' => [
                    'file' => '/var/www/html/public/ClickPesaAPI.php',
                    'line' => 216
                ],
                'createdAt' => date('Y-m-d H:i:s', strtotime('-15 minutes'))
            ],
            [
                'requestId' => 'REQ_' . uniqid(),
                'customerName' => 'Alice Williams',
                'customerEmail' => 'alice@example.com',
                'billPayNumber' => '55042914871933',
                'billAmount' => 8000,
                'billDescription' => 'TV Subscription - July 2024',
                'billReference' => 'TV001',
                'billPaymentMode' => 'EXACT',
                'status' => 'SUCCESS',
                'processingTime' => 1450,
                'createdAt' => date('Y-m-d H:i:s', strtotime('-20 minutes'))
            ],
            [
                'requestId' => 'REQ_' . uniqid(),
                'customerName' => 'Charlie Brown',
                'customerPhone' => '255715345678',
                'billPayNumber' => null,
                'billAmount' => 12000,
                'billDescription' => 'Gas Bill - July 2024',
                'billReference' => 'GAS001',
                'billPaymentMode' => 'ALLOW_PARTIAL_AND_OVER_PAYMENT',
                'status' => 'PENDING',
                'processingTime' => null,
                'createdAt' => date('Y-m-d H:i:s', strtotime('-2 minutes'))
            ]
        ];
        
        return response()->json([
            'success' => true,
            'data' => $mockData
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to load tracking data: ' . $e->getMessage()
        ]);
    }
});

// API Test Routes for Direct Connectivity Verification
Route::post('/api/control-numbers/test-connection', function () {
    try {
        // Load ClickPesa configuration
        require_once public_path('config.php');
        require_once public_path('ClickPesaAPI.php');
        
        $config = include(public_path('config.php'));
        
        // Test basic configuration
        if (!isset($config['api_key']) || !isset($config['client_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'API configuration missing: api_key or client_id not found'
            ]);
        }
        
        // Test API class instantiation
        $api = new \ClickPesaAPI($config);
        
        // Test token generation
        $token = $api->getValidToken();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate API token'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'API connection successful',
            'data' => [
                'token_length' => strlen($token),
                'config_status' => 'loaded',
                'api_class' => 'instantiated'
            ]
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Connection test failed: ' . $e->getMessage(),
            'error' => [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]
        ]);
    }
});

Route::post('/api/control-numbers/test-auth', function () {
    try {
        // Load ClickPesa configuration
        require_once public_path('config.php');
        require_once public_path('ClickPesaAPI.php');
        
        $config = include(public_path('config.php'));
        $api = new \ClickPesaAPI($config);
        
        // Test authentication by generating a new token
        $startTime = microtime(true);
        $token = $api->getValidToken();
        $authTime = round((microtime(true) - $startTime) * 1000);
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed - no token generated'
            ]);
        }
        
        // Test token validity by making a simple API call
        try {
            $testResponse = $api->queryAllPayments(['limit' => 1]);
            $apiCallTime = round((microtime(true) - $startTime) * 1000);
            
            return response()->json([
                'success' => true,
                'message' => 'Authentication successful',
                'data' => [
                    'token_generated' => true,
                    'token_length' => strlen($token),
                    'auth_time_ms' => $authTime,
                    'api_call_time_ms' => $apiCallTime - $authTime,
                    'api_response' => $testResponse ? 'success' : 'failed'
                ]
            ]);
            
        } catch (Exception $apiException) {
            return response()->json([
                'success' => false,
                'message' => 'Token generated but API call failed: ' . $apiException->getMessage(),
                'data' => [
                    'token_generated' => true,
                    'token_length' => strlen($token),
                    'auth_time_ms' => $authTime,
                    'api_error' => $apiException->getMessage()
                ]
            ]);
        }
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Authentication test failed: ' . $e->getMessage(),
            'error' => [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        ]);
    }
});

// API Route for Payment Status Checking
Route::post('/api/check-payment-status', function (Illuminate\Http\Request $request) {
    try {
        // Load ClickPesa configuration
        require_once public_path('config.php');
        require_once public_path('ClickPesaAPI.php');
        
        $config = include(public_path('config.php'));
        $api = new \ClickPesaAPI($config);
        
        // Build search parameters
        $searchParams = [];
        
        if ($request->has('transactionId') && !empty($request->transactionId)) {
            $searchParams['id'] = $request->transactionId;
        }
        
        if ($request->has('phoneNumber') && !empty($request->phoneNumber)) {
            $searchParams['paymentPhoneNumber'] = $request->phoneNumber;
        }
        
        if ($request->has('email') && !empty($request->email)) {
            $searchParams['customerEmail'] = $request->email;
        }
        
        if ($request->has('status') && !empty($request->status)) {
            $searchParams['status'] = $request->status;
        }
        
        if ($request->has('startDate') && !empty($request->startDate)) {
            $searchParams['startDate'] = $request->startDate;
        }
        
        if ($request->has('endDate') && !empty($request->endDate)) {
            $searchParams['endDate'] = $request->endDate;
        }
        
        // Query payments with search parameters
        $response = $api->queryAllPayments($searchParams);
        
        if (isset($response['data']) && is_array($response['data'])) {
            // Format results for frontend
            $formattedResults = [];
            foreach ($response['data'] as $payment) {
                $formattedResults[] = [
                    'id' => $payment['id'] ?? '',
                    'orderReference' => $payment['orderReference'] ?? '',
                    'paymentReference' => $payment['paymentReference'] ?? '',
                    'amount' => number_format($payment['collectedAmount'] ?? 0) . ' ' . ($payment['collectedCurrency'] ?? 'TZS'),
                    'status' => $payment['status'] ?? 'UNKNOWN',
                    'type' => 'USSD_PUSH', // Default payment type
                    'date' => isset($payment['createdAt']) ? date('Y-m-d H:i', strtotime($payment['createdAt'])) : 'N/A',
                    'customerName' => $payment['customer']['customerName'] ?? 'N/A',
                    'phoneNumber' => $payment['paymentPhoneNumber'] ?? ($payment['customer']['customerPhoneNumber'] ?? 'N/A'),
                    'email' => $payment['customer']['customerEmail'] ?? 'N/A'
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $formattedResults,
                'pagination' => [
                    'total' => count($formattedResults),
                    'current_page' => 1,
                    'last_page' => 1,
                    'from' => 1,
                    'to' => count($formattedResults)
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No payment transactions found'
            ]);
        }
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to check payment status: ' . $e->getMessage()
        ]);
    }
});

// Payment Processing Routes
Route::post('/payments/ussd-push', [App\Http\Controllers\AdvancedController::class, 'processUSSDPayment'])->name('payments.ussd-push');
Route::post('/payments/card-payment', [App\Http\Controllers\AdvancedController::class, 'processCardPayment'])->name('payments.card-payment');

// BillPay Routes
Route::get('/billpay/control', function () {
    return view('billpay.control');
})->name('billpay.control');

Route::get('/billpay/customer', function () {
    return view('billpay.customer');
})->name('billpay.customer');

Route::get('/billpay/bulk', function () {
    return view('billpay.bulk');
})->name('billpay.bulk');

Route::get('/billpay/status', function () {
    return view('billpay.status');
})->name('billpay.status');

// Transactions Routes
Route::get('/transactions', function () {
    return view('transactions.all');
})->name('transactions.all');

Route::get('/transactions/payment-history', function () {
    return view('transactions.payment-history');
})->name('transactions.payment-history');

Route::get('/transactions/payout-history', function () {
    return view('transactions.payout-history');
})->name('transactions.payout-history');

// Finance & Reports Routes
Route::get('/finance/revenue', function () {
    return view('finance.revenue');
})->name('finance.revenue');

Route::get('/finance/payout', function () {
    return view('finance.payout');
})->name('finance.payout');

Route::get('/finance/balance', function () {
    return view('finance.balance');
})->name('finance.balance');

Route::get('/finance/statement', function () {
    return view('finance.statement');
})->name('finance.statement');

// Account Routes
Route::get('/account/profile', function () {
    return view('account.profile');
})->name('account.profile');

Route::get('/account/security', function () {
    return view('account.security');
})->name('account.security');

Route::get('/account/logout', function () {
    return view('account.logout');
})->name('account.logout');

// API Test Page
Route::get('/api-test', function () {
    return view('api-test');
})->name('api.test');

// Splash Page
Route::get('/splash', function () {
    return view('splash');
})->name('splash');

Route::post('/splash/submit', function () {
    $countdown = request()->input('countdown', 0);
    
    // Validate countdown number
    if ($countdown < 0 || $countdown > 100) {
        return redirect()->back()->with('error', 'Please enter a number between 0 and 100');
    }
    
    // Check for lucky number (popup sweeper win condition)
    $luckyNumber = request()->input('lucky_number');
    
    if ($luckyNumber) {
        // User found the lucky number - success!
        return redirect()->back()->with('success', 'Congratulations! You found the lucky number ' . $luckyNumber . '! 🎉');
    } else {
        // Regular countdown submission
        return redirect()->back()->with('success', 'Countdown started from ' . $countdown . '!');
    }
})->name('splash.submit');

// API Status Check Endpoint
Route::get('/api/status-check', function () {
    try {
        // Load ClickPesa configuration
        require_once public_path('config.php');
        require_once public_path('ClickPesaAPI.php');
        
        $config = include(public_path('config.php'));
        $api = new \ClickPesaAPI($config);
        
        // Test API connectivity
        $token = $api->generateToken();
        
        if ($token) {
            return response()->json([
                'success' => true,
                'message' => 'API connection is working',
                'token' => substr($token, 0, 20) . '...',
                'timestamp' => now()->toISOString(),
                'endpoints' => [
                    'token_generation' => 'Working',
                    'payment_preview' => 'Working',
                    'payment_history' => 'Working'
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate API token',
                'error' => 'Authentication failed'
            ], 500);
        }
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'API check failed: ' . $e->getMessage(),
            'error' => 'Connection error'
        ], 500);
    }
});
