<?php
/**
 * Update ClickPesa Credentials
 */

echo "<h2>🔐 Update ClickPesa Credentials</h2>";

echo "<div style='background-color: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<strong>⚠️ 401 Error Resolution:</strong> Your IP is now whitelisted, but credentials need updating.";
echo "</div>";

echo "<h3>📋 Steps to Fix:</h3>";
echo "<ol>";
echo "<li><strong>Login to ClickPesa Dashboard</strong></li>";
echo "<li><strong>Go to API Keys section</strong></li>";
echo "<li><strong>Create New API Key:</strong>";
echo "<ul>";
echo "<li>Name: FEEDTAN PAY v2</li>";
echo "<li>Permissions: Full Access</li>";
echo "<li>Copy the new API Key immediately</li>";
echo "</ul></li>";
echo "<li><strong>Update config.php</strong> with new credentials</li>";
echo "</ol>";

echo "<h3>📝 Current Configuration:</h3>";
echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px;'>";
echo "<pre>";
echo "Client ID: ID6o6iORRUxzu7FHKts2uUOXSDL3M1HE\n";
echo "API Key: SKZqqc7Miwmy5U0ssdxy0jSSw2OHDq8oF0wV67f394\n";
echo "Status: ❌ HTTP 401 Error\n";
echo "</pre>";
echo "</div>";

echo "<h3>🔄 Update Form:</h3>";
echo "<form method='POST' action=''>";
echo "<div class='mb-3'>";
echo "<label for='client_id' class='form-label'>Client ID:</label>";
echo "<input type='text' class='form-control' id='client_id' name='client_id' ";
echo "value='ID6o6iORRUxzu7FHKts2uUOXSDL3M1HE' required>";
echo "</div>";

echo "<div class='mb-3'>";
echo "<label for='api_key' class='form-label'>New API Key:</label>";
echo "<input type='text' class='form-control' id='api_key' name='api_key' ";
echo "placeholder='Enter new API Key from ClickPesa dashboard' required>";
echo "</div>";

echo "<button type='submit' class='btn btn-primary'>Update Credentials</button>";
echo "</form>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newClientId = $_POST['client_id'];
    $newApiKey = $_POST['api_key'];
    
    echo "<h3>✅ Updated Configuration:</h3>";
    echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
    echo "<pre>";
    echo "'client_id' => '$newClientId',\n";
    echo "'api_key' => '$newApiKey',\n";
    echo "</pre>";
    echo "<p><strong>Copy this and update config.php lines 12-13</strong></p>";
    echo "</div>";
}

echo "<h3>🧪 After Update:</h3>";
echo "<p>Test with: <a href='debug_auth.php'>debug_auth.php</a></p>";
echo "<p>Or try payment: <a href='initiate_payment.php'>initiate_payment.php</a></p>";

echo "<div style='background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<strong>💡 Important:</strong> API Keys are only shown once when created. Save them securely!";
echo "</div>";

echo "<p><a href='index.php'>← Back to Dashboard</a></p>";
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
