<?php

/**
 * ClickPesa API Client
 * 
 * A comprehensive PHP class for interacting with ClickPesa payment APIs
 */

class ClickPesaAPI {
    private $config;
    private $token = null;
    private $tokenExpiry = null;
    
    public function __construct($config) {
        $this->config = $config['clickpesa'];
    }
    
    /**
     * Generate JWT Authorization Token
     */
    public function generateToken() {
        $url = $this->config['api_base_url'] . '/generate-token';
        
        $headers = [
            'api-key: ' . $this->config['api_key'],
            'client-id: ' . $this->config['client_id']
        ];
        
        $response = $this->makeRequest('POST', $url, null, $headers);
        
        if ($response && isset($response['success']) && $response['success']) {
            // Store the raw token (without Bearer prefix) since we'll add it in headers
            $rawToken = $response['token'];
            if (strpos($rawToken, 'Bearer ') === 0) {
                $this->token = substr($rawToken, 7); // Remove "Bearer " prefix
            } else {
                $this->token = $rawToken;
            }
            // Token is valid for 1 hour
            $this->tokenExpiry = time() + 3600;
            return $response['token']; // Return full token for external use
        }
        
        throw new Exception('Failed to generate token: ' . ($response['message'] ?? 'Unknown error'));
    }
    
    /**
     * Get valid token (generate new one if expired)
     */
    private function getValidToken() {
        if (!$this->token || $this->tokenExpiry <= time()) {
            $this->generateToken();
        }
        return $this->token;
    }
    
    /**
     * Preview USSD-PUSH request
     */
    public function previewUSSDPush($amount, $orderReference, $phoneNumber, $fetchSenderDetails = false, $checksum = null) {
        $url = $this->config['api_base_url'] . '/payments/preview-ussd-push-request';
        
        $data = [
            'amount' => $amount,
            'currency' => $this->config['currency'],
            'orderReference' => $orderReference,
            'phoneNumber' => $phoneNumber,
            'fetchSenderDetails' => $fetchSenderDetails
        ];
        
        if ($checksum) {
            $data['checksum'] = $checksum;
        }
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken(),
            'Content-Type: application/json'
        ];
        
        return $this->makeRequest('POST', $url, json_encode($data), $headers);
    }
    
    /**
     * Initiate USSD-PUSH request
     */
    public function initiateUSSDPush($amount, $orderReference, $phoneNumber, $checksum = null) {
        $url = $this->config['api_base_url'] . '/payments/initiate-ussd-push-request';
        
        $data = [
            'amount' => $amount,
            'currency' => $this->config['currency'],
            'orderReference' => $orderReference,
            'phoneNumber' => $phoneNumber
        ];
        
        if ($checksum) {
            $data['checksum'] = $checksum;
        }
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken(),
            'Content-Type: application/json'
        ];
        
        return $this->makeRequest('POST', $url, json_encode($data), $headers);
    }
    
    /**
     * Query Payment Status by Order Reference
     */
    public function queryPaymentStatus($orderReference) {
        $url = $this->config['api_base_url'] . '/payments/' . urlencode($orderReference);
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken()
        ];
        
        return $this->makeRequest('GET', $url, null, $headers);
    }
    
    /**
     * Query All Payments with filtering and pagination
     */
    public function queryAllPayments($params = []) {
        $url = $this->config['api_base_url'] . '/payments/all';
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken()
        ];
        
        return $this->makeRequest('GET', $url, null, $headers);
    }
    
    /**
     * Make HTTP request using cURL
     */
    private function makeRequest($method, $url, $data = null, $headers = []) {
        $curl = curl_init();
        
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $this->config['timeout'],
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ];
        
        if ($data) {
            $options[CURLOPT_POSTFIELDS] = $data;
        }
        
        curl_setopt_array($curl, $options);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        curl_close($curl);
        
        if ($err) {
            throw new Exception("cURL Error: " . $err);
        }
        
        $decodedResponse = json_decode($response, true);
        
        if ($httpCode >= 400) {
            $message = $decodedResponse['message'] ?? 'API Error';
            throw new Exception("HTTP {$httpCode}: {$message}");
        }
        
        return $decodedResponse;
    }
    
    /**
     * Preview Mobile Money Payout
     */
    public function previewMobileMoneyPayout($amount, $phoneNumber, $currency = 'TZS', $orderReference = null, $checksum = null) {
        $url = $this->config['api_base_url'] . '/payouts/preview-mobile-money-payout';
        
        $data = [
            'amount' => $amount,
            'phoneNumber' => $phoneNumber,
            'currency' => $currency
        ];
        
        if ($orderReference) $data['orderReference'] = $orderReference;
        if ($checksum) $data['checksum'] = $checksum;
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken(),
            'Content-Type: application/json'
        ];
        
        return $this->makeRequest('POST', $url, json_encode($data), $headers);
    }
    
    /**
     * Create Mobile Money Payout
     */
    public function createMobileMoneyPayout($amount, $phoneNumber, $currency = 'TZS', $orderReference = null, $checksum = null) {
        $url = $this->config['api_base_url'] . '/payouts/create-mobile-money-payout';
        
        $data = [
            'amount' => $amount,
            'phoneNumber' => $phoneNumber,
            'currency' => $currency
        ];
        
        if ($orderReference) $data['orderReference'] = $orderReference;
        if ($checksum) $data['checksum'] = $checksum;
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken(),
            'Content-Type: application/json'
        ];
        
        return $this->makeRequest('POST', $url, json_encode($data), $headers);
    }
    
    /**
     * Preview Bank Payout
     */
    public function previewBankPayout($amount, $currency, $bankAccount, $bankCode, $accountName, $orderReference = null, $checksum = null) {
        $url = $this->config['api_base_url'] . '/payouts/preview-bank-payout';
        
        $data = [
            'amount' => $amount,
            'currency' => $currency,
            'bankAccount' => $bankAccount,
            'bankCode' => $bankCode,
            'accountName' => $accountName
        ];
        
        if ($orderReference) $data['orderReference'] = $orderReference;
        if ($checksum) $data['checksum'] = $checksum;
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken(),
            'Content-Type: application/json'
        ];
        
        return $this->makeRequest('POST', $url, json_encode($data), $headers);
    }
    
    /**
     * Create Bank Payout
     */
    public function createBankPayout($amount, $currency, $bankAccount, $bankCode, $accountName, $orderReference = null, $checksum = null) {
        $url = $this->config['api_base_url'] . '/payouts/create-bank-payout';
        
        $data = [
            'amount' => $amount,
            'currency' => $currency,
            'bankAccount' => $bankAccount,
            'bankCode' => $bankCode,
            'accountName' => $accountName
        ];
        
        if ($orderReference) $data['orderReference'] = $orderReference;
        if ($checksum) $data['checksum'] = $checksum;
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken(),
            'Content-Type: application/json'
        ];
        
        return $this->makeRequest('POST', $url, json_encode($data), $headers);
    }
    
    /**
     * Query Payout Status
     */
    public function queryPayoutStatus($payoutReference) {
        $url = $this->config['api_base_url'] . '/payouts/' . urlencode($payoutReference);
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken()
        ];
        
        return $this->makeRequest('GET', $url, null, $headers);
    }
    
    /**
     * Query All Payouts
     */
    public function queryAllPayouts($params = []) {
        $url = $this->config['api_base_url'] . '/payouts/all';
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken()
        ];
        
        return $this->makeRequest('GET', $url, null, $headers);
    }
    
    /**
     * Retrieve Banks List
     */
    public function getBanksList() {
        $url = $this->config['api_base_url'] . '/list/banks';
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken()
        ];
        
        return $this->makeRequest('GET', $url, null, $headers);
    }
    
    /**
     * Generate unique order reference (max 20 characters)
     */
    public function generateOrderReference($prefix = 'FEEDTAN') {
        // Generate a unique ID and ensure total length is <= 20 characters
        $uniqueId = strtoupper(uniqid());
        $timestamp = time();
        
        $reference = $prefix . substr($uniqueId, -8) . substr($timestamp, -6);
        
        // Ensure it's not longer than 20 characters
        if (strlen($reference) > 20) {
            $reference = substr($reference, 0, 20);
        }
        
        return $reference;
    }
    
    /**
     * Format amount for API
     */
    public function formatAmount($amount) {
        return number_format((float)$amount, 0, '.', '');
    }
    
    /**
     * Get payment status description
     */
    public function getStatusDescription($status) {
        $statuses = [
            'SUCCESS' => 'Payment completed successfully',
            'SETTLED' => 'Payment has been settled',
            'PROCESSING' => 'Payment is being processed',
            'PENDING' => 'Payment is pending',
            'FAILED' => 'Payment failed'
        ];
        
        return $statuses[$status] ?? 'Unknown status';
    }
    
    /**
     * Validate phone number for Tanzania
     */
    public function validatePhoneNumber($phoneNumber) {
        // Remove any non-digit characters
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Check if it starts with country code and has correct length for Tanzania
        if (preg_match('/^255[67]\d{8}$/', $cleaned)) {
            return $cleaned;
        }
        
        return false;
    }
    
    /**
     * Generate FEEDTANPAY control number with 2-digit suffix
     */
    public function generateFeedtanPayControlNumber($suffix = null) {
        if ($suffix === null) {
            // Generate random 2-digit suffix
            $suffix = str_pad(mt_rand(1, 99), 2, '0', STR_PAD_LEFT);
        } else {
            // Ensure suffix is exactly 2 digits
            $suffix = str_pad($suffix, 2, '0', STR_PAD_LEFT);
            $suffix = substr($suffix, -2); // Take last 2 digits
        }
        
        return 'FEEDTANPAY' . $suffix;
    }
    
    /**
     * Generate FEEDTAN transaction ID for internal tracking
     */
    public function generateFeedtanTransactionId($prefix = 'FEEDTAN') {
        // Generate a unique transaction ID with FEEDTAN prefix
        $uniqueId = strtoupper(uniqid());
        $timestamp = time();
        $random = str_pad(mt_rand(100, 999), 3, '0', STR_PAD_LEFT);
        
        $transactionId = $prefix . $random . substr($timestamp, -6);
        
        // Ensure it's not longer than 20 characters (API limit)
        if (strlen($transactionId) > 20) {
            $transactionId = substr($transactionId, 0, 20);
        }
        
        return $transactionId;
    }
    
    // ==================== BILLPAY METHODS ====================
    
    /**
     * Create Order Control Number
     */
    public function createOrderControlNumber($billDescription, $billAmount = null, $billReference = null, $billPaymentMode = 'ALLOW_PARTIAL_AND_OVER_PAYMENT') {
        $url = $this->config['api_base_url'] . '/billpay/create-order-control-number';
        
        $data = [
            'billDescription' => $billDescription,
            'billPaymentMode' => $billPaymentMode
        ];
        
        if ($billAmount !== null) $data['billAmount'] = $billAmount;
        if ($billReference) $data['billReference'] = $billReference;
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken(),
            'Content-Type: application/json'
        ];
        
        return $this->makeRequest('POST', $url, json_encode($data), $headers);
    }
    
    /**
     * Create Customer Control Number
     */
    public function createCustomerControlNumber($customerName, $customerEmail = null, $customerPhone = null, $billDescription = null, $billAmount = null, $billReference = null, $billPaymentMode = 'ALLOW_PARTIAL_AND_OVER_PAYMENT') {
        $url = $this->config['api_base_url'] . '/billpay/create-customer-control-number';
        
        $data = [
            'customerName' => $customerName,
            'billPaymentMode' => $billPaymentMode
        ];
        
        if ($customerEmail) $data['customerEmail'] = $customerEmail;
        if ($customerPhone) $data['customerPhone'] = $customerPhone;
        if ($billDescription) $data['billDescription'] = $billDescription;
        if ($billAmount !== null) $data['billAmount'] = $billAmount;
        if ($billReference) $data['billReference'] = $billReference;
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken(),
            'Content-Type: application/json'
        ];
        
        return $this->makeRequest('POST', $url, json_encode($data), $headers);
    }
    
    /**
     * Bulk Create Order Control Numbers
     */
    public function bulkCreateOrderControlNumbers($controlNumbers) {
        $url = $this->config['api_base_url'] . '/billpay/bulk-create-order-control-numbers';
        
        $data = [
            'controlNumbers' => $controlNumbers
        ];
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken(),
            'Content-Type: application/json'
        ];
        
        return $this->makeRequest('POST', $url, json_encode($data), $headers);
    }
    
    /**
     * Bulk Create Customer Control Numbers
     */
    public function bulkCreateCustomerControlNumbers($controlNumbers) {
        $url = $this->config['api_base_url'] . '/billpay/bulk-create-customer-control-numbers';
        
        $data = [
            'controlNumbers' => $controlNumbers
        ];
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken(),
            'Content-Type: application/json'
        ];
        
        return $this->makeRequest('POST', $url, json_encode($data), $headers);
    }
    
    /**
     * Query BillPay Number Details
     */
    public function queryBillPayNumber($billPayNumber) {
        $url = $this->config['api_base_url'] . '/billpay/' . $billPayNumber;
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken()
        ];
        
        return $this->makeRequest('GET', $url, null, $headers);
    }
    
    /**
     * Update BillPay Reference
     */
    public function updateBillPayReference($billPayNumber, $billAmount = null, $billDescription = null, $billPaymentMode = null, $billStatus = null) {
        $url = $this->config['api_base_url'] . '/billpay/' . $billPayNumber;
        
        $data = [];
        
        if ($billAmount !== null) $data['billAmount'] = $billAmount;
        if ($billDescription) $data['billDescription'] = $billDescription;
        if ($billPaymentMode) $data['billPaymentMode'] = $billPaymentMode;
        if ($billStatus) $data['billStatus'] = $billStatus;
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken(),
            'Content-Type: application/json'
        ];
        
        return $this->makeRequest('PATCH', $url, json_encode($data), $headers);
    }
    
    /**
     * Update BillPay Number Status (Deprecated - use updateBillPayReference instead)
     */
    public function updateBillPayNumberStatus($billPayNumber, $status) {
        $url = $this->config['api_base_url'] . '/billpay/update-status';
        
        $data = [
            'billPayNumber' => $billPayNumber,
            'status' => $status
        ];
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken(),
            'Content-Type: application/json'
        ];
        
        return $this->makeRequest('PUT', $url, json_encode($data), $headers);
    }
    
    // ==================== ACCOUNT METHODS ====================
    
    /**
     * Retrieve Account Balance
     */
    public function getAccountBalance() {
        $url = $this->config['api_base_url'] . '/account/balance';
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken()
        ];
        
        return $this->makeRequest('GET', $url, null, $headers);
    }
    
    /**
     * Retrieve Account Statement
     */
    public function getAccountStatement($currency = 'TZS', $startDate = null, $endDate = null) {
        $url = $this->config['api_base_url'] . '/account/statement';
        
        $params = ['currency' => $currency];
        if ($startDate) $params['startDate'] = $startDate;
        if ($endDate) $params['endDate'] = $endDate;
        
        $queryString = http_build_query($params);
        $url .= '?' . $queryString;
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken()
        ];
        
        return $this->makeRequest('GET', $url, null, $headers);
    }
}
