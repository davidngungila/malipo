<?php
require_once 'config.php';
require_once 'ClickPesaAPI.php';

$config = include 'config.php';

echo "<h2>🔍 HTTP 401 Detailed Debug</h2>";

echo "<h3>📋 Current Configuration:</h3>";
echo "<pre>";
echo "Client ID: " . $config['clickpesa']['client_id'] . "\n";
echo "API Key: " . substr($config['clickpesa']['api_key'], 0, 12) . "...\n";
echo "API Key Length: " . strlen($config['clickpesa']['api_key']) . " characters\n";
echo "API Base URL: " . $config['clickpesa']['api_base_url'] . "\n";
echo "</pre>";

echo "<h3>🧪 Testing Individual Components:</h3>";

// Test 1: Direct cURL with verbose output
echo "<h4>Test 1: Direct API Call with Headers</h4>";
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $config['clickpesa']['api_base_url'] . '/generate-token',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => $config['clickpesa']['timeout'],
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_HTTPHEADER => [
        "api-key: " . $config['clickpesa']['api_key'],
        "client-id: " . $config['clickpesa']['client_id'],
        "Content-Type: application/json",
        "User-Agent: ClickPesa-PHP-Client/1.0"
    ],
    CURLOPT_HEADER => true,
    CURLOPT_VERBOSE => true,
    CURLOPT_STDERR => fopen('php://stderr', 'w'),
]);

$response = curl_exec($curl);
$err = curl_error($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
curl_close($curl);

if ($err) {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
    echo "<strong>cURL Error:</strong> " . $err;
    echo "</div>";
} else {
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>HTTP Status:</strong> " . $httpCode . "<br>";
    echo "<strong>Response Headers:</strong><br><pre style='font-size: 12px; max-height: 200px; overflow-y: auto;'>" . htmlspecialchars($headers) . "</pre>";
    echo "<strong>Response Body:</strong><br><pre style='font-size: 12px; max-height: 200px; overflow-y: auto;'>" . htmlspecialchars($body) . "</pre>";
    echo "</div>";
}

// Test 2: Validate API Key format
echo "<h4>Test 2: API Key Format Validation</h4>";
$apiKey = $config['clickpesa']['api_key'];
echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px;'>";
echo "<strong>API Key Length:</strong> " . strlen($apiKey) . " characters<br>";
echo "<strong>Expected Length:</strong> ~50-60 characters<br>";
echo "<strong>Contains only valid chars:</strong> " . (ctype_alnum($apiKey) ? "Yes" : "No - contains special chars") . "<br>";
echo "<strong>Starts with expected prefix:</strong> " . (preg_match('/^[A-Z]/', $apiKey) ? "Yes" : "No") . "<br>";
echo "</div>";

// Test 3: Client ID format
echo "<h4>Test 3: Client ID Format Validation</h4>";
$clientId = $config['clickpesa']['client_id'];
echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px;'>";
echo "<strong>Client ID:</strong> " . $clientId . "<br>";
echo "<strong>Client ID Length:</strong> " . strlen($clientId) . " characters<br>";
echo "<strong>Expected Length:</strong> ~40-50 characters<br>";
echo "<strong>Format:</strong> " . (preg_match('/^[A-Za-z0-9]+$/', $clientId) ? "Valid" : "Invalid format") . "<br>";
echo "</div>";

// Test 4: API endpoint accessibility
echo "<h4>Test 4: API Endpoint Accessibility</h4>";
$endpoints = [
    '/generate-token' => 'POST',
    '/payments/all' => 'GET'
];

foreach ($endpoints as $endpoint => $method) {
    $testUrl = $config['clickpesa']['api_base_url'] . $endpoint;
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $testUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_NOBODY => true,
    ]);
    
    $result = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $err = curl_error($curl);
    curl_close($curl);
    
    echo "<div style='background-color: #f8f9fa; padding: 10px; border-radius: 5px; margin: 5px 0;'>";
    echo "<strong>$method $endpoint:</strong> ";
    if ($err) {
        echo "<span style='color: red;'>Error - $err</span>";
    } else {
        echo "<span style='color: " . ($httpCode == 401 ? "orange" : "green") . ";'>HTTP $httpCode</span>";
    }
    echo "</div>";
}

echo "<h3>🔧 Potential Solutions:</h3>";
echo "<div style='background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<ol>";
echo "<li><strong>Regenerate API Key:</strong> Current key might be expired or revoked</li>";
echo "<li><strong>Check Client ID:</strong> Ensure it matches your dashboard exactly</li>";
echo "<li><strong>Account Status:</strong> Verify account is active and not suspended</li>";
echo "<li><strong>API Permissions:</strong> Check if API access is enabled for your account</li>";
echo "<li><strong>Rate Limits:</strong> You might have hit API rate limits</li>";
echo "<li><strong>Environment:</strong> Ensure you're using correct API endpoint (sandbox vs production)</li>";
echo "</ol>";
echo "</div>";

echo "<h3>🔄 Next Steps:</h3>";
echo "<ol>";
echo "<li>Login to ClickPesa dashboard</li>";
echo "<li>Generate a new API key</li>";
echo "<li>Update config.php with new credentials</li>";
echo "<li>Test again with debug_auth.php</li>";
echo "</ol>";

echo "<p><a href='index.php'>← Back to Dashboard</a> | <a href='debug_auth.php'>← Previous Debug</a></p>";
?>
