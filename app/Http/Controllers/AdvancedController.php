<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class AdvancedController extends Controller
{
    private $clickPesaAPI;

    public function __construct()
    {
        // Load ClickPesa API
        require_once public_path('config.php');
        require_once public_path('ClickPesaAPI.php');
        
        $config = include(public_path('config.php'));
        $this->clickPesaAPI = new \ClickPesaAPI($config);
    }

    /**
     * Helper method to get ClickPesa API instance
     */
    private function clickPesaAPI()
    {
        return $this->clickPesaAPI;
    }
    /**
     * Display the advanced dashboard page
     */
    public function index(): View
    {
        return view('advanced.dashboard');
    }

    /**
     * Display live payment status page
     */
    public function liveStatus(): View
    {
        return view('advanced.live-status');
    }

    /**
     * Display payment history page
     */
    public function paymentHistory(): View
    {
        return view('advanced.payment-history');
    }

    /**
     * Display payment status check page
     */
    public function checkStatus(): View
    {
        return view('advanced.check-status');
    }

    /**
     * API endpoint to check payment status
     */
    public function checkPaymentStatus(Request $request): JsonResponse
    {
        try {
            $reference = $request->input('reference');
            
            if (empty($reference)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment reference is required'
                ], 400);
            }

            // Initialize ClickPesa API
            $paymentStatus = $this->clickPesaAPI()->queryPaymentStatus($reference);
            
            return response()->json([
                'success' => true,
                'data' => $paymentStatus,
                'message' => 'Payment status retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to get payment history
     */
    public function getPaymentHistory(Request $request): JsonResponse
    {
        try {
            // Get query parameters
            $params = [];
            if ($request->has('limit')) {
                $params['limit'] = $request->input('limit');
            }
            if ($request->has('offset')) {
                $params['offset'] = $request->input('offset');
            }
            if ($request->has('status')) {
                $params['status'] = $request->input('status');
            }
            if ($request->has('startDate')) {
                $params['startDate'] = $request->input('startDate');
            }
            if ($request->has('endDate')) {
                $params['endDate'] = $request->input('endDate');
            }
            
            // Query all payments
            $payments = $this->clickPesaAPI()->queryAllPayments($params);
            
            return response()->json([
                'success' => true,
                'data' => $payments,
                'message' => 'Payment history retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to get account balance
     */
    public function getAccountBalance(): JsonResponse
    {
        try {
            // Get account balance
            $balance = $this->clickPesaAPI()->getAccountBalance();
            
            return response()->json([
                'success' => true,
                'data' => $balance,
                'message' => 'Account balance retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to get account statement
     */
    public function getAccountStatement(Request $request): JsonResponse
    {
        try {
            // Get query parameters
            $currency = $request->input('currency', 'TZS');
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');
            
            // Get account statement
            $statement = $this->clickPesaAPI()->getAccountStatement($currency, $startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'data' => $statement,
                'message' => 'Account statement retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to get banks list
     */
    public function getBanksList(): JsonResponse
    {
        try {
            // Get banks list
            $banks = $this->clickPesaAPI()->getBanksList();
            
            return response()->json([
                'success' => true,
                'data' => $banks,
                'message' => 'Banks list retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process USSD Push Payment
     */
    public function processUSSDPayment(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'customerName' => 'required|string|max:255',
                'customerPhone' => 'required|string|regex:/^255[67]\d{8}$/',
                'customerEmail' => 'nullable|email|max:255',
                'amount' => 'required|numeric|min:100|max:1000000',
                'description' => 'required|string|max:500',
                'orderReference' => 'nullable|string|max:255',
                'fetchSenderDetails' => 'boolean',
                'sendSMS' => 'boolean',
                'sendEmail' => 'boolean'
            ]);

            // Load config like working implementation
            $config = include(public_path('config.php'));
            
            // Generate order reference if not provided
            if (empty($validatedData['orderReference'])) {
                $validatedData['orderReference'] = $this->clickPesaAPI()->generateOrderReference();
            }

            // Format amount and validate phone number - exact same logic as working file
            $amount = $this->clickPesaAPI()->formatAmount($validatedData['amount']);
            $phoneNumber = $this->clickPesaAPI()->validatePhoneNumber($validatedData['customerPhone']);
            
            if (!$phoneNumber) {
                throw new Exception('Invalid phone number. Please use format: 255712345678');
            }
            
            if ($amount < $config['clickpesa']['payment']['min_amount'] || $amount > $config['clickpesa']['payment']['max_amount']) {
                throw new Exception('Amount must be between ' . $config['clickpesa']['payment']['min_amount'] . ' and ' . $config['clickpesa']['payment']['max_amount'] . ' TZS');
            }
            
            // Preview payment first - exact same logic as working file
            $preview = $this->clickPesaAPI()->previewUSSDPush($amount, $validatedData['orderReference'], $phoneNumber, true);
            
            if (isset($preview['activeMethods']) && !empty($preview['activeMethods'])) {
                // Initiate payment - exact same logic as working file
                $payment = $this->clickPesaAPI()->initiateUSSDPush($amount, $validatedData['orderReference'], $phoneNumber);
                
                return response()->json([
                    'success' => true,
                    'data' => $payment,
                    'message' => 'Payment initiated successfully! USSD Push sent to ' . $phoneNumber . '. Transaction ID: ' . $payment['id']
                ]);
            } else {
                throw new Exception('No active payment methods available for this phone number');
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process Card Payment
     */
    public function processCardPayment(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'customerName' => 'required|string|max:255',
                'customerEmail' => 'required|email|max:255',
                'customerPhone' => 'required|string|regex:/^255[67]\d{8}$/',
                'cardNumber' => 'required|string|regex:/^\d{13,19}$/',
                'cardholderName' => 'required|string|max:255',
                'expiryMonth' => 'required|string|regex:/^(0[1-9]|1[0-2])$/',
                'expiryYear' => 'required|string|regex:/^\d{4}$/',
                'cvv' => 'required|string|regex:/^\d{3,4}$/',
                'amount' => 'required|numeric|min:100|max:1000000',
                'description' => 'required|string|max:500',
                'orderReference' => 'nullable|string|max:255',
                'enable3DS' => 'boolean',
                'saveCard' => 'boolean',
                'sendReceipt' => 'boolean'
            ]);

            // Generate order reference if not provided
            if (empty($validatedData['orderReference'])) {
                $validatedData['orderReference'] = 'FEEDTAN' . strtoupper(uniqid());
            }

            // Validate card expiry
            $expiryDate = new DateTime($validatedData['expiryYear'] . '-' . $validatedData['expiryMonth'] . '-01');
            $expiryDate->modify('last day of this month');
            $today = new DateTime();
            
            if ($expiryDate <= $today) {
                return response()->json([
                    'success' => false,
                    'message' => 'Card has expired'
                ], 400);
            }

            // Prepare payment data for ClickPesa API
            $paymentData = [
                'amount' => $validatedData['amount'],
                'currency' => 'TZS',
                'external_id' => $validatedData['orderReference'],
                'payer' => [
                    'phone_number' => $validatedData['customerPhone'],
                    'email' => $validatedData['customerEmail'],
                    'name' => $validatedData['customerName']
                ],
                'payment_method' => 'CARD',
                'card' => [
                    'number' => str_replace([' ', '-'], '', $validatedData['cardNumber']),
                    'holder_name' => $validatedData['cardholderName'],
                    'expiry_month' => $validatedData['expiryMonth'],
                    'expiry_year' => $validatedData['expiryYear'],
                    'cvv' => $validatedData['cvv']
                ],
                'metadata' => [
                    'description' => $validatedData['description'],
                    'enable_3ds' => $validatedData['enable3DS'] ?? true,
                    'save_card' => $validatedData['saveCard'] ?? false,
                    'send_receipt' => $validatedData['sendReceipt'] ?? true
                ]
            ];

            // Initiate card payment
            $payment = $this->clickPesaAPI()->initiatePayment($paymentData);

            return response()->json([
                'success' => true,
                'data' => $payment,
                'message' => 'Card payment processed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
