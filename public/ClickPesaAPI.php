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
     * Query All Payments with optional filters
     */
    public function queryAllPayments($params = []) {
        // Use the correct ClickPesa API endpoint
        $url = $this->config['api_base_url'] . '/payments/all';
        
        // Add default parameters
        $defaultParams = [
            'orderBy' => 'DESC',
            'limit' => 20
        ];
        $params = array_merge($defaultParams, $params);
        
        // Add query parameters
        if (!empty($params)) {
            $queryString = http_build_query($params);
            $url .= '?' . $queryString;
        }
        
        $headers = [
            'Authorization: Bearer ' . $this->getValidToken()
        ];
        
        $response = $this->makeRequest('GET', $url, null, $headers);
        
        // Return the response as-is - let the route handle the structure
        return $response;
    }
    
    /**
     * Make HTTP request to API with enhanced error tracking
     */
    private function makeRequest($method, $url, $data = null, $headers = []) {
        $ch = curl_init();
        $startTime = microtime(true);
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $defaultHeaders = [
            "Authorization: Bearer " . $this->getValidToken(),
            "Content-Type: application/json",
            "Accept: application/json",
            "User-Agent: FEEDTAN-System/1.0"
        ];
        
        $allHeaders = array_merge($defaultHeaders, $headers);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);
        
        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $requestTime = round((microtime(true) - $startTime) * 1000); // in milliseconds
        
        curl_close($ch);
        
        // Log request details
        $this->logApiRequest($method, $url, $data, $httpCode, $requestTime, $err);
        
        if ($err) {
            $this->logError('CURL_ERROR', [
                'url' => $url,
                'method' => $method,
                'error' => $err,
                'request_time' => $requestTime
            ]);
            throw new Exception("cURL Error: " . $err);
        }
        
        $decodedResponse = json_decode($response, true);
        
        if ($httpCode >= 400) {
            $errorMessage = $decodedResponse['message'] ?? 'API request failed';
            $errorCode = $decodedResponse['errorCode'] ?? 'UNKNOWN';
            
            $this->logError('API_ERROR', [
                'url' => $url,
                'method' => $method,
                'http_code' => $httpCode,
                'error_code' => $errorCode,
                'error_message' => $errorMessage,
                'response' => $decodedResponse,
                'request_time' => $requestTime
            ]);
            
            throw new Exception("API Error (HTTP $httpCode, Code: $errorCode): " . $errorMessage);
        }
        
        // Log successful request
        $this->logSuccess('API_SUCCESS', [
            'url' => $url,
            'method' => $method,
            'http_code' => $httpCode,
            'request_time' => $requestTime,
            'response_keys' => array_keys($decodedResponse ?? [])
        ]);
        
        return $decodedResponse;
    }
    
    /**
     * Log API request details
     */
    private function logApiRequest($method, $url, $data, $httpCode, $requestTime, $error = null) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $method,
            'url' => $url,
            'http_code' => $httpCode,
            'request_time_ms' => $requestTime,
            'has_data' => !empty($data),
            'error' => $error
        ];
        
        // In production, this would log to a file or database
        error_log("API Request: " . json_encode($logEntry));
    }
    
    /**
     * Log error details
     */
    private function logError($type, $details) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'details' => $details
        ];
        
        error_log("API Error: " . json_encode($logEntry));
    }
    
    /**
     * Log success details
     */
    private function logSuccess($type, $details) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'details' => $details
        ];
        
        error_log("API Success: " . json_encode($logEntry));
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
     * Create Order Control Number
     */
    public function createOrderControlNumber($data) {
        $url = $this->config['api_base_url'] . '/billpay/create-order-control-number';
        
        $payload = [
            'billDescription' => $data['billDescription'],
            'billPaymentMode' => $data['billPaymentMode'] ?? 'ALLOW_PARTIAL_AND_OVER_PAYMENT'
        ];
        
        if (isset($data['billAmount'])) {
            $payload['billAmount'] = $this->formatAmount($data['billAmount']);
        }
        
        if (isset($data['billReference'])) {
            $payload['billReference'] = $data['billReference'];
        }
        
        return $this->makeRequest('POST', $url, $payload);
    }
    
    /**
     * Create Customer Control Number
     */
    public function createCustomerControlNumber($data) {
        $url = $this->config['api_base_url'] . '/billpay/create-customer-control-number';
        
        $payload = [
            'customerName' => $data['customerName'],
            'billDescription' => $data['billDescription'],
            'billPaymentMode' => $data['billPaymentMode'] ?? 'ALLOW_PARTIAL_AND_OVER_PAYMENT'
        ];
        
        if (isset($data['customerEmail'])) {
            $payload['customerEmail'] = $data['customerEmail'];
        }
        
        if (isset($data['customerPhone'])) {
            $payload['customerPhone'] = $this->validatePhoneNumber($data['customerPhone']);
        }
        
        if (isset($data['billAmount'])) {
            $payload['billAmount'] = $this->formatAmount($data['billAmount']);
        }
        
        if (isset($data['billReference'])) {
            $payload['billReference'] = $data['billReference'];
        }
        
        return $this->makeRequest('POST', $url, $payload);
    }
    
    /**
     * Bulk Create Order Control Numbers
     */
    public function bulkCreateOrderControlNumbers($controlNumbers) {
        $url = $this->config['api_base_url'] . '/billpay/bulk-create-order-control-numbers';
        
        $payload = ['controlNumbers' => []];
        
        foreach ($controlNumbers as $item) {
            $controlItem = [
                'billDescription' => $item['billDescription']
            ];
            
            if (isset($item['billAmount'])) {
                $controlItem['billAmount'] = $this->formatAmount($item['billAmount']);
            }
            
            if (isset($item['billReference'])) {
                $controlItem['billReference'] = $item['billReference'];
            }
            
            $payload['controlNumbers'][] = $controlItem;
        }
        
        return $this->makeRequest('POST', $url, $payload);
    }
    
    /**
     * Bulk Create Customer Control Numbers
     */
    public function bulkCreateCustomerControlNumbers($controlNumbers) {
        $url = $this->config['api_base_url'] . '/billpay/bulk-create-customer-control-numbers';
        
        $payload = ['controlNumbers' => []];
        
        foreach ($controlNumbers as $item) {
            $controlItem = [
                'customerName' => $item['customerName']
            ];
            
            if (isset($item['customerEmail'])) {
                $controlItem['customerEmail'] = $item['customerEmail'];
            }
            
            if (isset($item['customerPhone'])) {
                $controlItem['customerPhone'] = $this->validatePhoneNumber($item['customerPhone']);
            }
            
            if (isset($item['billAmount'])) {
                $controlItem['billAmount'] = $this->formatAmount($item['billAmount']);
            }
            
            if (isset($item['billReference'])) {
                $controlItem['billReference'] = $item['billReference'];
            }
            
            if (isset($item['billDescription'])) {
                $controlItem['billDescription'] = $item['billDescription'];
            }
            
            $payload['controlNumbers'][] = $controlItem;
        }
        
        return $this->makeRequest('POST', $url, $payload);
    }
    
    /**
     * Query BillPay Number Details
     */
    public function queryBillPayNumber($billPayNumber) {
        $url = $this->config['api_base_url'] . '/billpay/' . $billPayNumber;
        
        return $this->makeRequest('GET', $url);
    }
    
    /**
     * Update BillPay Reference
     */
    public function updateBillPayReference($billPayNumber, $data) {
        $url = $this->config['api_base_url'] . '/billpay/' . $billPayNumber;
        
        $payload = [];
        
        if (isset($data['billAmount'])) {
            $payload['billAmount'] = $this->formatAmount($data['billAmount']);
        }
        
        if (isset($data['billDescription'])) {
            $payload['billDescription'] = $data['billDescription'];
        }
        
        if (isset($data['billPaymentMode'])) {
            $payload['billPaymentMode'] = $data['billPaymentMode'];
        }
        
        if (isset($data['billStatus'])) {
            $payload['billStatus'] = $data['billStatus'];
        }
        
        return $this->makeRequest('PATCH', $url, $payload);
    }
    
    /**
     * Update BillPay Number Status
     */
    public function updateBillPayStatus($billPayNumber, $status) {
        $url = $this->config['api_base_url'] . '/billpay/update-status';
        
        $payload = [
            'billPayNumber' => $billPayNumber,
            'status' => $status
        ];
        
        return $this->makeRequest('PUT', $url, $payload);
    }
}
