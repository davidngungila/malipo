<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaymentController
{
    /**
     * Process payment requests
     */
    public function processPayment(Request $request): JsonResponse
    {
        $success = '';
        $error = '';
        $paymentData = null;
        $amount = null;
        $phoneNumber = null;
        $orderReference = null;
        $startTime = microtime(true);
        $requestId = 'REQ_' . uniqid();

        try {
            // Log request start
            error_log("[$requestId] Public Payment Request Started");
            
            // Load ClickPesa configuration
            require_once public_path('config.php');
            require_once public_path('ClickPesaAPI.php');
            
            $config = include(public_path('config.php'));
            $api = new \ClickPesaAPI($config);
            
            // Test API connectivity first
            $testToken = $api->getValidToken();
            if (!$testToken) {
                throw new \Exception('API authentication failed - unable to generate token');
            }
            
            // Validate and sanitize input
            $amount = $request->input('amount');
            $phoneNumber = $request->input('phone_number');
            $memberName = $request->input('member_name');
            $paymentPurpose = $request->input('payment_purpose');
            $collectionReference = $request->input('collection');
            
            error_log("[$requestId] Input validation: " . json_encode([
                'amount' => $amount,
                'phoneNumber' => $phoneNumber,
                'memberName' => $memberName,
                'paymentPurpose' => $paymentPurpose,
                'collectionReference' => $collectionReference
            ]));
            
            // Check for collection order reference in URL
            if ($collectionReference) {
                // Display collection success page
                $success = "HAKIKISHA UNADISPLAY NA Collection Order Reference<br><strong>FEEDTANF6A31C8526711</strong><br><br>KWENYE PAGE YA MALIPO YAMEANZISHWA...";
                error_log("[$requestId] Collection reference displayed: $collectionReference");
            } elseif ($request->isMethod('post')) {
                // Validate required fields
                if (empty($amount)) {
                    throw new \Exception('Amount is required');
                }
                
                if (empty($phoneNumber)) {
                    throw new \Exception('Phone number is required');
                }
                
                if (empty($memberName)) {
                    throw new \Exception('Member name is required');
                }
                
                if (empty($paymentPurpose)) {
                    throw new \Exception('Payment purpose is required');
                }
                
                // Format and validate amount
                $formattedAmount = $api->formatAmount($amount);
                if (!$formattedAmount || $formattedAmount < 100 || $formattedAmount > 1000000) {
                    throw new \Exception('Amount must be between 100 and 1,000,000 TZS');
                }
                
                // Validate phone number
                $validatedPhone = $api->validatePhoneNumber($phoneNumber);
                if (!$validatedPhone) {
                    throw new \Exception('Invalid phone number format. Use format: 255712345678');
                }
                
                // Generate order reference
                $orderReference = $api->generateOrderReference();
                
                error_log("[$requestId] Validated data: " . json_encode([
                    'amount' => $formattedAmount,
                    'phoneNumber' => $validatedPhone,
                    'memberName' => $memberName,
                    'paymentPurpose' => $paymentPurpose,
                    'orderReference' => $orderReference
                ]));
                
                // Create payment data for API
                $paymentData = [
                    'amount' => $formattedAmount,
                    'currency' => 'TZS',
                    'phoneNumber' => $validatedPhone,
                    'customerName' => $memberName,
                    'description' => $paymentPurpose,
                    'orderReference' => $orderReference,
                    'sendSMS' => true,
                    'fetchSenderDetails' => true
                ];
                
                error_log("[$requestId] Sending payment data to ClickPesa API");
                
                // Try to initiate payment with error handling
                try {
                    // First try to preview payment
                    $preview = $api->previewUSSDPush($formattedAmount, $orderReference, $validatedPhone, true);
                    
                    if (isset($preview['activeMethods']) && !empty($preview['activeMethods'])) {
                        error_log("[$requestId] Payment preview successful, initiating payment");
                        
                        // Initiate payment
                        $payment = $api->initiateUSSDPush($formattedAmount, $orderReference, $validatedPhone);
                        
                        if ($payment && isset($payment['id'])) {
                            $paymentData = $payment;
                            $success = 'Malipo yako yameanza! Tafadhali thibitisha malipo kwenye simu yako. Muamala ID: ' . $payment['id'];
                            error_log("[$requestId] Payment successful: " . json_encode($payment));
                        } else {
                            throw new \Exception('Payment initiation failed - no valid response from ClickPesa API');
                        }
                    } else {
                        throw new \Exception('Payment preview failed - no active payment methods available');
                    }
                    
                } catch (\Exception $apiException) {
                    error_log("[$requestId] ClickPesa API Error: " . $apiException->getMessage());
                    throw new \Exception('Payment processing failed: ' . $apiException->getMessage());
                }
                
            } else {
                throw new \Exception('Invalid request method');
            }
            
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $processingTime = round((microtime(true) - $startTime) * 1000);
            
            error_log("[$requestId] Payment processing failed: " . json_encode([
                'error' => $e->getMessage(),
                'processingTime' => $processingTime,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]));
        }

        return response()->json([
            'success' => !empty($error),
            'message' => $success ?: $error,
            'data' => $paymentData,
            'requestId' => $requestId,
            'processingTime' => $processingTime ?? 0
        ]);
    }
}
