<?php
require_once 'config.php';
require_once 'ClickPesaAPI.php';

$config = include 'config.php';

echo "<h2>Authentication Fix Center</h2>";

echo "<div style='background-color: #f8d7da; color: #721c24; padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
echo "<h3>Current Status: HTTP 401 - API Error</h3>";
echo "<p>Your IP whitelist has been removed from ClickPesa dashboard, causing authentication failures.</p>";
echo "</div>";

echo "<h3>Step-by-Step Solution:</h3>";

echo "<div class='step-card' style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 15px; border-left: 4px solid #667eea;'>";
echo "<h4>Step 1: Login to ClickPesa Dashboard</h4>";
echo "<p>Access your ClickPesa account at: <a href='https://dashboard.clickpesa.com' target='_blank'>https://dashboard.clickpesa.com</a></p>";
echo "</div>";

echo "<div class='step-card' style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 15px; border-left: 4px solid #28a745;'>";
echo "<h4>Step 2: Navigate to IP Whitelist</h4>";
echo "<p>Go to: Settings <i class='fas fa-arrow-right'></i> API Security <i class='fas fa-arrow-right'></i> IP Whitelist</p>";
echo "</div>";

echo "<div class='step-card' style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 15px; border-left: 4px solid #ffc107;'>";
echo "<h4>Step 3: Add Your IP Addresses</h4>";
echo "<p>Add BOTH of these IP addresses:</p>";
echo "<ul>";
echo "<li><strong>Public IP:</strong> <code>102.208.186.66</code> (Tanzania Commission for Science and Technology)</li>";
echo "<li><strong>Local IP:</strong> <code>192.168.3.163</code> (Your local network)</li>";
echo "</ul>";
echo "</div>";

echo "<div class='step-card' style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 15px; border-left: 4px solid #17a2b8;'>";
echo "<h4>Step 4: Save and Wait</h4>";
echo "<p>Click 'Save' and wait 2-3 minutes for changes to take effect.</p>";
echo "</div>";

echo "<h3>Current Configuration:</h3>";
echo "<div style='background: #e9ecef; padding: 15px; border-radius: 5px; font-family: monospace;'>";
echo "Client ID: " . $config['clickpesa']['client_id'] . "<br>";
echo "API Key: " . substr($config['clickpesa']['api_key'], 0, 12) . "...<br>";
echo "API Base URL: " . $config['clickpesa']['api_base_url'] . "<br>";
echo "</div>";

echo "<h3>Test Connection:</h3>";
echo "<div class='d-flex gap-2 mb-3'>";
echo "<button class='btn btn-primary' onclick='testConnection()'>";
echo "<i class='fas fa-plug me-2'></i>Test Connection";
echo "</button>";
echo "<button class='btn btn-success' onclick='testToken()'>";
echo "<i class='fas fa-key me-2'></i>Test Token Generation";
echo "</button>";
echo "</div>";

echo "<div id='testResults'></div>";

echo "<h3>Alternative Solutions:</h3>";
echo "<div class='alert alert-warning'>";
echo "<h5>If you cannot whitelist IPs:</h5>";
echo "<ol>";
echo "<li><strong>Use ngrok:</strong> Create a public tunnel to your localhost</li>";
echo "<li><strong>VPS Server:</strong> Deploy on a server with static IP</li>";
echo "<li><strong>Contact ClickPesa Support:</strong> Request IP whitelist assistance</li>";
echo "<li><strong>Check API Key:</strong> Ensure your API key hasn't expired</li>";
echo "</ol>";
echo "</div>";

echo "<h3>Quick Links:</h3>";
echo "<div class='d-flex flex-column gap-2'>";
echo "<a href='debug_auth.php' class='btn btn-outline-primary'>";
echo "<i class='fas fa-bug me-2'></i>Debug Authentication";
echo "</a>";
echo "<a href='update_credentials.php' class='btn btn-outline-info'>";
echo "<i class='fas fa-sync me-2'></i>Update Credentials";
echo "</a>";
echo "<a href='index.php' class='btn btn-outline-secondary'>";
echo "<i class='fas fa-home me-2'></i>Back to Dashboard";
echo "</a>";
echo "</div>";

// JavaScript for testing
echo "<script>";
echo "
function testConnection() {
    const resultsDiv = document.getElementById('testResults');
    resultsDiv.innerHTML = '<div class=\"alert alert-info\"><i class=\"fas fa-spinner fa-spin me-2\"></i>Testing connection...</div>';
    
    fetch('debug_auth.php')
        .then(response => response.text())
        .then(html => {
            resultsDiv.innerHTML = '<div class=\"alert alert-success\"><i class=\"fas fa-check me-2\"></i>Connection test completed. Check the debug page for details.</div>';
            window.open('debug_auth.php', '_blank');
        })
        .catch(error => {
            resultsDiv.innerHTML = '<div class=\"alert alert-danger\"><i class=\"fas fa-times me-2\"></i>Connection test failed: ' + error.message + '</div>';
        });
}

function testToken() {
    const resultsDiv = document.getElementById('testResults');
    resultsDiv.innerHTML = '<div class=\"alert alert-info\"><i class=\"fas fa-spinner fa-spin me-2\"></i>Testing token generation...</div>';
    
    fetch('debug_401.php')
        .then(response => response.text())
        .then(html => {
            resultsDiv.innerHTML = '<div class=\"alert alert-success\"><i class=\"fas fa-check me-2\"></i>Token test completed. Check the debug page for details.</div>';
            window.open('debug_401.php', '_blank');
        })
        .catch(error => {
            resultsDiv.innerHTML = '<div class=\"alert alert-danger\"><i class=\"fas fa-times me-2\"></i>Token test failed: ' + error.message + '</div>';
        });
}
";
echo "</script>";

echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>";
echo "<style>";
echo ".step-card { transition: all 0.3s ease; }";
echo ".step-card:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }";
echo "</style>";
?>
