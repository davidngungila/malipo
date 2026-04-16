<?php
require_once 'config.php';
require_once 'ClickPesaAPI.php';

$config = include 'config.php';

echo "<h2>ClickPesa API Authentication Debug</h2>";

echo "<h3>Configuration Check:</h3>";
echo "<pre>";
echo "API Base URL: " . $config['clickpesa']['api_base_url'] . "\n";
echo "Client ID: " . $config['clickpesa']['client_id'] . "\n";
echo "API Key: " . substr($config['clickpesa']['api_key'], 0, 8) . "...\n";
echo "Timeout: " . $config['clickpesa']['timeout'] . " seconds\n";
echo "</pre>";

echo "<h3>Testing API Connection:</h3>";

try {
    $api = new ClickPesaAPI($config);
    
    echo "<p>Attempting to generate token...</p>";
    
    // Test token generation
    $token = $api->generateToken();
    
    echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>✅ Success!</strong> Token generated successfully.<br>";
    echo "Token: " . substr($token, 0, 50) . "...<br>";
    echo "Token Length: " . strlen($token) . " characters";
    echo "</div>";
    
    // Test a simple API call
    echo "<p>Testing API call with token...</p>";
    
    $params = ['limit' => 1];
    $response = $api->queryAllPayments($params);
    
    echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>✅ API Call Successful!</strong><br>";
    echo "Response received from payments endpoint<br>";
    echo "Total Count: " . ($response['totalCount'] ?? 'N/A');
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>❌ Error:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Error Type:</strong> " . get_class($e) . "<br>";
    echo "<strong>File:</strong> " . $e->getFile() . "<br>";
    echo "<strong>Line:</strong> " . $e->getLine();
    echo "</div>";
    
    echo "<h3>Troubleshooting Steps:</h3>";
    echo "<ol>";
    echo "<li>Verify your Client ID is correct: <code>" . htmlspecialchars($config['clickpesa']['client_id']) . "</code></li>";
    echo "<li>Verify your API Key is correct and hasn't expired</li>";
    echo "<li>Check if your IP is whitelisted in ClickPesa dashboard</li>";
    echo "<li>Ensure your account is active and not suspended</li>";
    echo "<li>Try regenerating the API key from ClickPesa dashboard</li>";
    echo "<li>Check if there are any rate limits applied to your account</li>";
    echo "</ol>";
}

echo "<h3>cURL Test (Direct API Call):h3>";

// Test direct cURL call
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
        "client-id: " . $config['clickpesa']['client_id']
    ],
    CURLOPT_HEADER => true,
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
    echo "<strong>Response Headers:</strong><br><pre>" . htmlspecialchars($headers) . "</pre>";
    echo "<strong>Response Body:</strong><br><pre>" . htmlspecialchars($body) . "</pre>";
    echo "</div>";
}

echo "<h3>Next Steps:</h3>";
echo "<p>If you're still getting 401 errors, please:</p>";
echo "<ul>";
echo "<li>Double-check your credentials in the ClickPesa dashboard</li>";
echo "<li>Ensure the API key hasn't been revoked or expired</li>";
echo "<li>Contact ClickPesa support if the issue persists</li>";
echo "<li>Try creating a new API key from the dashboard</li>";
echo "</ul>";

echo "<p><a href='index.php'>← Back to Dashboard</a></p>";
?>
