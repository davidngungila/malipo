<?php
require_once 'config.php';
require_once 'ClickPesaAPI.php';

$config = include 'config.php';

echo "<h2>Testing Fixed ClickPesa API</h2>";

try {
    $api = new ClickPesaAPI($config);
    
    echo "<h3>Step 1: Token Generation</h3>";
    $token = $api->generateToken();
    echo "<div class='alert alert-success'>";
    echo "<strong>Success!</strong> Token generated successfully.<br>";
    echo "Token: " . substr($token, 0, 50) . "...<br>";
    echo "Token Length: " . strlen($token) . " characters";
    echo "</div>";
    
    echo "<h3>Step 2: Testing Payment Preview API</h3>";
    $preview = $api->previewUSSDPush('1000', 'TEST_' . time(), '255712345678', true);
    
    echo "<div class='alert alert-success'>";
    echo "<strong>Success!</strong> Payment preview API working.<br>";
    echo "Response: " . json_encode($preview, JSON_PRETTY_PRINT);
    echo "</div>";
    
    echo "<h3>Step 3: Testing Payment History API</h3>";
    $history = $api->queryAllPayments(['limit' => 5]);
    
    echo "<div class='alert alert-success'>";
    echo "<strong>Success!</strong> Payment history API working.<br>";
    echo "Total Count: " . ($history['totalCount'] ?? 'N/A') . "<br>";
    echo "Records Retrieved: " . count($history['data'] ?? []) . "<br>";
    echo "</div>";
    
    echo "<div class='alert alert-info'>";
    echo "<strong>Excellent!</strong> All API endpoints are working correctly.<br>";
    echo "Your ClickPesa payment system is now fully functional!";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<strong>Error:</strong> " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine();
    echo "</div>";
    
    echo "<div class='alert alert-warning'>";
    echo "<strong>Troubleshooting:</strong><br>";
    echo "1. Check if IP whitelist is properly configured<br>";
    echo "2. Verify API credentials are correct<br>";
    echo "3. Ensure ClickPesa account is active<br>";
    echo "</div>";
}

echo "<div class='mt-4'>";
echo "<h3>Quick Actions:</h3>";
echo "<a href='index.php' class='btn btn-primary me-2'>Go to Dashboard</a>";
echo "<a href='initiate_payment.php' class='btn btn-success me-2'>Initiate Payment</a>";
echo "<a href='live_status.php' class='btn btn-info me-2'>Live Status</a>";
echo "<a href='advanced_dashboard.php' class='btn btn-warning'>Advanced Dashboard</a>";
echo "</div>";

echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
?>
