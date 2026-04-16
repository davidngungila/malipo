# ClickPesa Payment System Documentation

## Table of Contents

1. [System Overview](#system-overview)
2. [Configuration](#configuration)
3. [Features](#features)
4. [API Integration](#api-integration)
5. [BillPay System](#billpay-system)
6. [Payment Processing](#payment-processing)
7. [Account Management](#account-management)
8. [Security Features](#security-features)
9. [User Interface](#user-interface)
10. [Error Handling](#error-handling)
11. [Deployment Guide](#deployment-guide)

---

## System Overview

The ClickPesa Payment System is a comprehensive payment processing platform built with PHP and integrated with the ClickPesa API. It provides a complete solution for managing payments, payouts, BillPay control numbers, and account operations.

### Key Components

- **Payment Gateway Integration** - Direct integration with ClickPesa API
- **BillPay Control Numbers** - Generate and manage payment control numbers
- **Mobile Money Processing** - Support for USSD push payments
- **Account Management** - Real-time balance and statement tracking
- **Transaction History** - Complete payment and payout records
- **Multi-currency Support** - Primary TZS currency with extensibility

### Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    ClickPesa Payment System                    │
├─────────────────────────────────────────────────────────┤
│  Frontend (HTML/CSS/JS)  │  Backend (PHP)        │
│  ┌─────────────────────┐    ┌─────────────────────┐  │
│  │ Dashboard Pages    │    │ ClickPesaAPI Class  │  │
│  │ Payment Forms     │    │ Config Management   │  │
│  │ History Views     │    │ Error Handling      │  │
│  └─────────────────────┘    └─────────────────────┘  │
├─────────────────────────────────────────────────────────┤
│  ClickPesa API Gateway  │
│  ┌─────────────────────────────────────────────────┐  │
│  │ JWT Authentication  │  │
│  │ Payment Processing │  │
│  │ BillPay Operations│  │
│  │ Account Queries   │  │
│  └─────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
```

---

## Configuration

### API Configuration

The system uses a configuration file (`config.php`) to store ClickPesa API credentials:

```php
return [
    'clickpesa' => [
        'api_base_url' => 'https://api.clickpesa.com/third-parties',
        'client_id' => 'YOUR_CLIENT_ID',
        'api_key' => 'YOUR_API_KEY',
        'timeout' => 30,
        'currency' => 'TZS',
        
        'payment' => [
            'default_amount' => 1000,
            'min_amount' => 100,
            'max_amount' => 1000000,
        ],
        
        'callback' => [
            'url' => 'http://your-domain.com/clickpesa/callback.php',
            'secret_key' => 'your-callback-secret',
        ],
    ],
];
```

### Environment Setup

1. **Development Environment**
   - Local development server (Laragon/XAMPP)
   - PHP 7.4+ required
   - cURL extension enabled
   - OpenSSL for JWT token generation

2. **Production Environment**
   - Web server with HTTPS
   - PHP 7.4+ required
   - Proper SSL certificates
   - Firewall configuration for API access

---

## Features

### Core Features

#### 1. Payment Processing
- **USSD Push Payments** - Send payment requests to mobile money
- **Mobile Money Integration** - Support for TIGO PESA, HALOPESA, M-PESA
- **Real-time Validation** - Phone number and amount validation
- **Transaction Tracking** - Complete payment status monitoring

#### 2. BillPay System
- **Control Number Generation** - FEEDTANPAY format with 2-digit suffix
- **Order Control Numbers** - For product/service payments
- **Customer Control Numbers** - For customer-specific payments
- **Bulk Operations** - Create multiple control numbers at once
- **Payment Modes** - Flexible, Exact amount options

#### 3. Account Management
- **Balance Inquiry** - Real-time account balance
- **Transaction Statements** - Detailed transaction history
- **Currency Support** - Multi-currency with TZS primary
- **Date Filtering** - Custom date range queries

#### 4. Advanced Features
- **Live Status Dashboard** - Real-time system monitoring
- **Advanced Analytics** - Payment statistics and trends
- **Multi-language Support** - Extensible localization
- **Responsive Design** - Mobile-friendly interface

---

## API Integration

### ClickPesaAPI Class

The core API integration class provides methods for all ClickPesa operations:

#### Authentication
```php
$api = new ClickPesaAPI($config);
```

#### Payment Methods
```php
// Preview USSD Push Payment
$preview = $api->previewUSSDPush($amount, $orderReference, $phoneNumber);

// Initiate USSD Push Payment
$payment = $api->initiateUSSDPush($amount, $orderReference, $phoneNumber);

// Preview Mobile Money Payment
$preview = $api->previewMobileMoneyPayment($amount, $orderReference, $phoneNumber, $channel);

// Initiate Mobile Money Payment
$payment = $api->initiateMobileMoneyPayment($amount, $orderReference, $phoneNumber, $channel);
```

#### Payout Methods
```php
// Preview Bank Transfer
$preview = $api->previewBankTransfer($amount, $orderReference, $bankBic, $accountNumber);

// Initiate Bank Transfer
$payout = $api->createBankTransfer($amount, $orderReference, $bankBic, $accountNumber);

// Preview Mobile Money Payout
$preview = $api->previewMobileMoneyPayout($amount, $orderReference, $phoneNumber, $channel);

// Initiate Mobile Money Payout
$payout = $api->createMobileMoneyPayout($amount, $orderReference, $phoneNumber, $channel);
```

#### BillPay Methods
```php
// Create Order Control Number
$orderControl = $api->createOrderControlNumber($description, $amount, $reference, $paymentMode);

// Create Customer Control Number
$customerControl = $api->createCustomerControlNumber($name, $email, $phone, $description, $amount, $reference, $paymentMode);

// Bulk Create Order Control Numbers
$bulkOrders = $api->bulkCreateOrderControlNumbers($orders);

// Bulk Create Customer Control Numbers
$bulkCustomers = $api->bulkCreateCustomerControlNumbers($customers);

// Query BillPay Number
$details = $api->queryBillPayNumber($billPayNumber);

// Update BillPay Reference
$updated = $api->updateBillPayReference($billPayNumber, $amount, $description, $paymentMode, $status);
```

#### Account Methods
```php
// Get Account Balance
$balance = $api->getAccountBalance('TZS');

// Get Account Statement
$statement = $api->getAccountStatement('TZS', $startDate, $endDate);
```

### Utility Methods
```php
// Generate Order Reference (FEEDTAN prefix)
$reference = $api->generateOrderReference('FEEDTAN');

// Generate FEEDTANPAY Control Number
$controlNumber = $api->generateFeedtanPayControlNumber();

// Generate FEEDTAN Transaction ID
$transactionId = $api->generateFeedtanTransactionId();

// Validate Phone Number (Tanzania)
$phone = $api->validatePhoneNumber('255712345678');

// Format Amount
$amount = $api->formatAmount(50000.50);

// Get Status Description
$status = $api->getStatusDescription('SUCCESS');
```

---

## BillPay System

### Control Number Format

All BillPay control numbers use the FEEDTANPAY format:

```
FEEDTANPAY + 2-digit suffix
Examples:
- FEEDTANPAY01
- FEEDTANPAY12
- FEEDTANPAY99
```

### Payment Modes

#### ALLOW_PARTIAL_AND_OVER_PAYMENT
- Customers can pay partial amounts
- Overpayments are accepted
- Suitable for flexible payment scenarios

#### EXACT
- Customers must pay exact amount only
- No partial payments accepted
- Suitable for fixed-price scenarios

### Bulk Operations

#### Bulk Order Creation
```php
$orders = [
    [
        'billDescription' => 'Web Hosting Plan - Basic',
        'billAmount' => 25000,
        'billReference' => 'FEEDTANPAY12'
    ],
    [
        'billDescription' => 'Domain Registration - .com',
        'billAmount' => 15000,
        'billReference' => 'FEEDTANPAY34'
    ]
];

$result = $api->bulkCreateOrderControlNumbers($orders);
```

#### Bulk Customer Creation
```php
$customers = [
    [
        'customerName' => 'John Doe',
        'customerEmail' => 'john@example.com',
        'customerPhone' => '255712345678',
        'billDescription' => 'Monthly Subscription',
        'billAmount' => 25000,
        'billReference' => 'FEEDTANPAY56'
    ]
];

$result = $api->bulkCreateCustomerControlNumbers($customers);
```

---

## Payment Processing

### USSD Push Payments

#### Payment Flow
1. **Validation** - Phone number and amount validation
2. **Preview** - Check available payment methods
3. **Initiation** - Send USSD push request
4. **Confirmation** - Receive payment confirmation
5. **Callback** - Process payment status updates

#### Transaction IDs
All transactions use FEEDTAN prefix:
```
Format: FEEDTAN + Random Digits + Timestamp
Example: FEEDTAN78654FBE900550
```

### Mobile Money Channels

#### Supported Channels
- **TIGO PESA** - TIGO mobile money
- **HALOPESA** - HALO mobile money  
- **M-PESA** - Safaricom mobile money
- **AIRTEL MONEY** - Airtel mobile money
- **TCL** - Tanzania Commercial Bank

#### Phone Number Validation
Tanzania phone numbers must follow format:
```
Format: 255[67]XXXXXXXX
Examples: 255712345678, 255689012345
Regex: ^255[67]\d{8}$
```

---

## Account Management

### Balance Inquiry

Real-time account balance with currency information:

```php
$balance = $api->getAccountBalance('TZS');

// Response Structure
[
    'currency' => 'TZS',
    'balance' => 150000.50,
    'availableBalance' => 125000.00,
    'frozenBalance' => 25000.50
];
```

### Transaction Statements

Comprehensive transaction history with filtering options:

```php
$statement = $api->getAccountStatement('TZS', '2024-01-01', '2024-12-31');

// Response Structure
[
    'currency' => 'TZS',
    'openingBalance' => 100000.00,
    'closingBalance' => 150000.50,
    'totalCredits' => 75000.00,
    'totalDebits' => 24500.50,
    'transactions' => [
        [
            'date' => '2024-04-11 15:48:32',
            'description' => 'Payment - Customer - SUCCESS',
            'amount' => 49500.00,
            'balance' => 150000.50,
            'entry' => 'Credit',
            'orderReference' => 'FEEDTAN78654FBE900550'
        ]
    ]
];
```

---

## Security Features

### JWT Authentication

Secure token-based authentication with ClickPesa API:

```php
// Automatic token generation and refresh
$token = $api->getValidToken();

// Token includes Bearer prefix
$headers = [
    'Authorization: Bearer ' . $token
];
```

### Input Validation

#### Server-side Validation
- **Phone Numbers** - Tanzania format validation
- **Amounts** - Range and format validation
- **Email Addresses** - Email format validation
- **Control Numbers** - FEEDTANPAY format validation

#### Client-side Validation
- **HTML5 form validation** where appropriate
- **JavaScript validation** for dynamic forms
- **Real-time feedback** for user input

### Callback Security

Secure webhook processing with secret key validation:

```php
// Verify callback authenticity
$secretKey = $config['clickpesa']['callback']['secret_key'];
$signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';

if ($signature === hash_hmac('sha256', $payload, $secretKey)) {
    // Process valid callback
}
```

---

## User Interface

### Dashboard Pages

#### Main Dashboard (`index.php`)
- **Quick Statistics** - Payment counts and totals
- **Recent Transactions** - Latest payment activity
- **Quick Actions** - Easy access to common tasks
- **Navigation Menu** - Access to all system features

#### Payment Pages
- **Initiate Payment** (`initiate_payment.php`) - Send USSD push payments
- **Initiate Payout** (`initiate_payout.php`) - Send money to recipients
- **Payment Status** (`payment_status.php`) - Track payment status
- **Payout Status** (`payout_status.php`) - Track payout status

#### BillPay Pages
- **Create Control Number** (`billpay_create.php`) - Generate BillPay numbers
- **Manage Control Numbers** (`billpay_manage.php`) - View and manage BillPay
- **List Control Numbers** (`billpay_list.php`) - View all active control numbers

#### Account Pages
- **Account Management** (`account_management.php`) - Balance and statements
- **Advanced Dashboard** (`advanced_dashboard.php`) - Detailed analytics

#### History Pages
- **Payment History** (`payment_history.php`) - Complete payment records
- **Payout History** (`payout_history.php`) - Complete payout records

### Design Features

#### Responsive Design
- **Mobile-First** - Optimized for mobile devices
- **Bootstrap 5** - Modern CSS framework
- **Card-Based Layout** - Clean, organized interface
- **Interactive Elements** - Hover effects and transitions

#### User Experience
- **Real-time Feedback** - Immediate validation responses
- **Loading Indicators** - Visual feedback during processing
- **Success Messages** - Clear confirmation of actions
- **Error Handling** - User-friendly error messages

---

## Error Handling

### Common Error Scenarios

#### API Errors
```php
try {
    $result = $api->initiatePayment($data);
} catch (Exception $e) {
    $error = $e->getMessage();
    // Log error for debugging
    error_log("Payment Error: " . $e->getMessage());
}
```

#### Input Validation
```php
// Phone number validation
if (!$api->validatePhoneNumber($phoneNumber)) {
    throw new Exception('Invalid Tanzania phone number format');
}

// Amount validation
if ($amount < $config['min_amount'] || $amount > $config['max_amount']) {
    throw new Exception('Amount out of allowed range');
}
```

#### Network Issues
- **Timeout Handling** - Configurable API timeouts
- **Retry Logic** - Automatic retry for failed requests
- **Fallback Messages** - Graceful degradation

### Logging

Comprehensive logging for debugging and monitoring:

```php
// Error logging
error_log("ClickPesa Error: " . $errorMessage);

// Transaction logging
file_put_contents("transactions.log", json_encode($transaction) . PHP_EOL, FILE_APPEND);

// Callback logging
file_put_contents("callback_log.txt", date("Y-m-d H:i:s") . " " . $rawData . PHP_EOL, FILE_APPEND);
```

---

## Deployment Guide

### Development Setup

#### Local Development
1. **Install Requirements**
   ```bash
   # PHP extensions
   sudo apt-get install php-curl php-openssl php-json
   
   # Local server
   # Install Laragon or XAMPP
   ```

2. **Configuration**
   ```bash
   # Copy config template
   cp config.example.php config.php
   
   # Edit configuration
   nano config.php
   ```

3. **Database Setup** (Optional)
   ```sql
   CREATE TABLE transactions (
       id INT AUTO_INCREMENT PRIMARY KEY,
       transaction_id VARCHAR(50),
       amount DECIMAL(15,2),
       phone VARCHAR(20),
       status VARCHAR(20),
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

#### Production Deployment

#### Server Requirements
- **PHP 7.4+** with required extensions
- **Web Server** - Apache/Nginx with HTTPS
- **SSL Certificate** - Valid SSL configuration
- **Firewall** - Open ports 80, 443, API access

#### Environment Configuration
```bash
# Production .htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Environment variables
SetEnv APP_ENV production
```

### Security Configuration

#### File Permissions
```bash
# Secure configuration files
chmod 600 config.php
chmod 600 callback.php

# Secure logs
chmod 644 logs/
```

#### SSL Configuration
```apache
<VirtualHost *:443>
    ServerName your-domain.com
    SSLEngine on
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key
</VirtualHost>
```

---

## API Reference

### ClickPesa API Endpoints

#### Authentication
- **Base URL**: `https://api.clickpesa.com/third-parties`
- **Authentication**: Bearer JWT Token
- **Token Refresh**: Automatic

#### Payment Operations
- **POST** `/payments/mobile-money`
- **POST** `/payments/ussd-push`
- **POST** `/payouts/mobile-money`
- **POST** `/payouts/bank-transfer`

#### BillPay Operations
- **POST** `/billpay/create-order-control-number`
- **POST** `/billpay/create-customer-control-number`
- **POST** `/billpay/bulk-create-order-control-numbers`
- **POST** `/billpay/bulk-create-customer-control-numbers`
- **GET** `/billpay/query-billpay-number`
- **PUT** `/billpay/update-billpay-reference`

#### Account Operations
- **GET** `/accounts/balance`
- **GET** `/accounts/statement`

### Response Formats

#### Success Response
```json
{
    "status": "success",
    "data": {
        "transactionId": "FEEDTAN78654FBE900550",
        "amount": 50000,
        "currency": "TZS",
        "status": "PENDING"
    }
}
```

#### Error Response
```json
{
    "status": "error",
    "message": "Invalid phone number format",
    "code": "INVALID_PHONE"
}
```

---

## Troubleshooting

### Common Issues

#### Payment Failures
1. **Invalid Phone Number**
   - Check Tanzania format: 255[67]XXXXXXXX
   - Verify no spaces or special characters

2. **Insufficient Balance**
   - Check account balance before payments
   - Verify minimum balance requirements

3. **Network Timeouts**
   - Increase timeout configuration
   - Check internet connectivity

4. **API Authentication**
   - Verify client credentials
   - Check JWT token generation

### Debug Mode

Enable debug logging for development:

```php
// In config.php
'clickpesa' => [
    'debug_mode' => true, // Enable detailed logging
    'log_level' => 'DEBUG'  // Log all requests
],
```

### Performance Optimization

#### Caching Strategy
```php
// Token caching
private $cachedToken = null;
private $tokenExpiry = null;

public function getValidToken() {
    if ($this->cachedToken && $this->tokenExpiry > time()) {
        return $this->cachedToken;
    }
    // Generate new token
    return $this->generateNewToken();
}
```

#### Database Optimization
```sql
-- Add indexes for performance
CREATE INDEX idx_transactions_date ON transactions(date);
CREATE INDEX idx_transactions_status ON transactions(status);
CREATE INDEX idx_transactions_phone ON transactions(phone);
```

---

## Support

### Contact Information

For technical support and questions:
- **Documentation**: This file
- **API Documentation**: ClickPesa Developer Portal
- **Support Email**: support@clickpesa.com
- **Status Page**: ClickPesa API Status

### Version Information

- **Current Version**: 2.0.0
- **Last Updated**: 2026-04-11
- **Compatibility**: PHP 7.4+, ClickPesa API v2

---

## License

This ClickPesa Payment System is proprietary software developed for FEEDTAN PAY operations. All rights reserved.

© 2026 FEEDTAN PAY. All rights reserved.
