<?php
require_once 'config.php';

$config = include 'config.php';

echo "<h2>Direct API Test with Your Credentials</h2>";

echo "<h3>Testing Token Generation:</h3>";

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api.clickpesa.com/third-parties/generate-token",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
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
    echo "<div class='alert alert-danger'>";
    echo "<strong>cURL Error:</strong> " . $err;
    echo "</div>";
} else {
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    echo "<div class='mb-3'>";
    echo "<strong>HTTP Status:</strong> " . $httpCode . "<br>";
    echo "<strong>Headers:</strong><br><pre style='font-size: 12px; max-height: 200px; overflow-y: auto;'>" . htmlspecialchars($headers) . "</pre>";
    echo "<strong>Response Body:</strong><br><pre style='font-size: 12px; max-height: 200px; overflow-y: auto;'>" . htmlspecialchars($body) . "</pre>";
    echo "</div>";
    
    if ($httpCode === 200) {
        echo "<div class='alert alert-success'>";
        echo "<strong>Success!</strong> Token generation working. Your credentials are valid.";
        echo "</div>";
        
        // Parse token and test a payment API call
        $tokenData = json_decode($body, true);
        if (isset($tokenData['token'])) {
            echo "<h3>Testing Payment Preview API:</h3>";
            testPaymentPreview($tokenData['token'], $config);
        }
    } else {
        echo "<div class='alert alert-danger'>";
        echo "<strong>Error:</strong> HTTP $httpCode - Authentication failed";
        echo "</div>";
        
        echo "<h3>Troubleshooting:</h3>";
        echo "<div class='alert alert-warning'>";
        echo "<ol>";
        echo "<li><strong>IP Whitelist:</strong> Ensure your IPs are whitelisted in ClickPesa dashboard</li>";
        echo "<li><strong>Credentials:</strong> Verify Client ID and API Key are correct</li>";
        echo "<li><strong>Account Status:</strong> Check if your account is active</li>";
        echo "<li><strong>API Key:</strong> May need to regenerate the API key</li>";
        echo "</ol>";
        echo "</div>";
    }
}

function testPaymentPreview($token, $config) {
    $curl = curl_init();
    
    $testData = [
        'amount' => '1000',
        'currency' => 'TZS',
        'orderReference' => 'TEST_' . time(),
        'phoneNumber' => '255712345678',
        'fetchSenderDetails' => false
    ];
    
    curl_setopt_array($curl, [
      CURLOPT_URL => "https://api.clickpesa.com/third-parties/payments/preview-ussd-push-request",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($testData),
      CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . $token,
        "Content-Type: application/json"
      ],
      CURLOPT_HEADER => true,
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

    curl_close($curl);

    if ($err) {
        echo "<div class='alert alert-danger'>";
        echo "<strong>cURL Error:</strong> " . $err;
        echo "</div>";
    } else {
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        
        echo "<div class='mb-3'>";
        echo "<strong>HTTP Status:</strong> " . $httpCode . "<br>";
        echo "<strong>Response Body:</strong><br><pre style='font-size: 12px; max-height: 200px; overflow-y: auto;'>" . htmlspecialchars($body) . "</pre>";
        echo "</div>";
        
        if ($httpCode === 200) {
            echo "<div class='alert alert-success'>";
            echo "<strong>Payment API Working!</strong> Your system is fully functional.";
            echo "</div>";
        } else {
            echo "<div class='alert alert-warning'>";
            echo "<strong>Payment API Error:</strong> HTTP $httpCode";
            echo "</div>";
        }
    }
}

echo "<h3>Current Configuration:</h3>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
echo "<strong>Client ID:</strong> " . $config['clickpesa']['client_id'] . "<br>";
echo "<strong>API Key:</strong> " . substr($config['clickpesa']['api_key'], 0, 12) . "...<br>";
echo "<strong>API Base URL:</strong> " . $config['clickpesa']['api_base_url'] . "<br>";
echo "</div>";

echo "<div class='mt-3'>";
echo "<a href='fix_authentication.php' class='btn btn-primary me-2'>Fix Authentication</a>";
echo "<a href='index.php' class='btn btn-secondary'>Back to Dashboard</a>";
echo "</div>";

echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
?>
