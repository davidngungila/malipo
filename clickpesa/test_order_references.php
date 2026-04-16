<?php
require_once 'config.php';
require_once 'ClickPesaAPI.php';

$config = include 'config.php';
$api = new ClickPesaAPI($config);

echo "<h2>Testing Order Reference Generation</h2>";

// Test order reference generation
echo "<h3>Generated Order References:</h3>";
echo "<div class='mb-3'>";
for ($i = 0; $i < 10; $i++) {
    $ref = $api->generateOrderReference('CP');
    echo "CP Reference: $ref (Length: " . strlen($ref) . ")<br>";
}
echo "</div>";

echo "<div class='mb-3'>";
for ($i = 0; $i < 10; $i++) {
    $ref = $api->generateOrderReference('PY');
    echo "PY Reference: $ref (Length: " . strlen($ref) . ")<br>";
}
echo "</div>";

// Test payment initiation with corrected references
echo "<h3>Testing Payment Initiation:</h3>";
try {
    $amount = 1000;
    $phoneNumber = '255712345678';
    $orderReference = $api->generateOrderReference('CP');
    
    echo "<div class='alert alert-info'>";
    echo "Testing payment with reference: $orderReference (Length: " . strlen($orderReference) . ")<br>";
    echo "Amount: $amount TZS<br>";
    echo "Phone: $phoneNumber<br>";
    echo "</div>";
    
    // Preview payment
    $preview = $api->previewUSSDPush($amount, $orderReference, $phoneNumber, true);
    
    if (isset($preview['activeMethods']) && !empty($preview['activeMethods'])) {
        echo "<div class='alert alert-success'>";
        echo "<strong>Payment Preview Successful!</strong><br>";
        echo "Active Methods: " . count($preview['activeMethods']) . "<br>";
        echo "First Method: " . ($preview['activeMethods'][0]['name'] ?? 'Unknown') . "<br>";
        echo "</div>";
        
        // Initiate payment
        $payment = $api->initiateUSSDPush($amount, $orderReference, $phoneNumber);
        
        echo "<div class='alert alert-success'>";
        echo "<strong>Payment Initiated Successfully!</strong><br>";
        echo "Payment ID: " . ($payment['id'] ?? 'N/A') . "<br>";
        echo "Status: " . ($payment['status'] ?? 'N/A') . "<br>";
        echo "Order Reference: " . ($payment['orderReference'] ?? 'N/A') . "<br>";
        echo "</div>";
        
    } else {
        echo "<div class='alert alert-warning'>";
        echo "<strong>No Active Methods Available</strong><br>";
        echo "This phone number may not support USSD payments<br>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<strong>Payment Test Failed:</strong> " . $e->getMessage() . "<br>";
    echo "</div>";
}

// Test payout initiation with corrected references
echo "<h3>Testing Mobile Money Payout:</h3>";
try {
    $amount = 1000;
    $phoneNumber = '255712345678';
    $currency = 'TZS';
    $orderReference = $api->generateOrderReference('PY');
    
    echo "<div class='alert alert-info'>";
    echo "Testing payout with reference: $orderReference (Length: " . strlen($orderReference) . ")<br>";
    echo "Amount: $amount $currency<br>";
    echo "Phone: $phoneNumber<br>";
    echo "</div>";
    
    // Preview payout
    $preview = $api->previewMobileMoneyPayout($amount, $phoneNumber, $currency, $orderReference);
    
    if (isset($preview['amount']) || isset($preview['balance'])) {
        echo "<div class='alert alert-success'>";
        echo "<strong>Payout Preview Successful!</strong><br>";
        echo "Preview Amount: " . ($preview['amount'] ?? 'N/A') . "<br>";
        echo "Balance: " . ($preview['balance'] ?? 'N/A') . "<br>";
        echo "Fee: " . ($preview['fee'] ?? 'N/A') . "<br>";
        echo "</div>";
        
        // Create payout
        $payout = $api->createMobileMoneyPayout($amount, $phoneNumber, $currency, $orderReference);
        
        echo "<div class='alert alert-success'>";
        echo "<strong>Payout Created Successfully!</strong><br>";
        echo "Payout ID: " . ($payout['id'] ?? 'N/A') . "<br>";
        echo "Status: " . ($payout['status'] ?? 'N/A') . "<br>";
        echo "Order Reference: " . ($payout['orderReference'] ?? 'N/A') . "<br>";
        echo "Amount: " . ($payout['amount'] ?? 'N/A') . "<br>";
        echo "</div>";
        
    } else {
        echo "<div class='alert alert-warning'>";
        echo "<strong>Payout Preview Failed</strong><br>";
        echo "Response: " . json_encode($preview) . "<br>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<strong>Payout Test Failed:</strong> " . $e->getMessage() . "<br>";
    echo "</div>";
}

echo "<h3>Quick Links:</h3>";
echo "<div class='d-flex gap-2 mb-3'>";
echo "<a href='initiate_payment.php' class='btn btn-primary'>Initiate Payment</a>";
echo "<a href='initiate_payout.php' class='btn btn-success'>Initiate Payout</a>";
echo "<a href='index.php' class='btn btn-secondary'>Dashboard</a>";
echo "</div>";

echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
?>
