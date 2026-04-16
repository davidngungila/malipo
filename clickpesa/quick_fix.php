<?php
/**
 * Quick Fix for IP Whitelist Issue
 */

echo "<h2>🔧 Quick Fix for IP Whitelist</h2>";

echo "<div style='background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<strong>⚠️ Current Issue:</strong> Your IP (192.168.3.163) is not whitelisted in ClickPesa dashboard";
echo "</div>";

echo "<h3>🚀 Solution 1: Use ngrok (Recommended for Testing)</h3>";
echo "<ol>";
echo "<li><strong>Download ngrok:</strong> <a href='https://ngrok.com/download' target='_blank'>https://ngrok.com/download</a></li>";
echo "<li><strong>Start ngrok:</strong> Open terminal and run: <code>ngrok http 80</code></li>";
echo "<li><strong>Copy ngrok URL:</strong> It will show something like <code>https://abc123.ngrok.io</code></li>";
echo "<li><strong>Update config:</strong> Replace callback URL with ngrok URL</li>";
echo "</ol>";

// Auto-generate ngrok config update
echo "<h3>📝 Auto-generated config update:</h3>";
echo "<textarea style='width: 100%; height: 100px; font-family: monospace;'>";
echo "// Replace in config.php line 26:
'url' => 'https://YOUR_NGROK_URL.ngrok.io/clickpesa/callback.php',";
echo "</textarea>";

echo "<h3>🌐 Solution 2: Whitelist Your IP</h3>";
echo "<ol>";
echo "<li><strong>Login to ClickPesa Dashboard</strong></li>";
echo "<li><strong>Go to Settings → API Security</strong></li>";
echo "<li><strong>Add IP addresses:</strong>";
echo "<ul>";
echo "<li><code>192.168.3.163</code> (your local IP)</li>";
echo "<li><code>102.208.186.66</code> (your current public IP)</li>";
echo "</ul></li>";
echo "<li><strong>Save settings</strong></li>";
echo "</ol>";

echo "<h3>🔍 Find Your Public IP:</h3>";
echo "<p>Your current public IP: <strong>102.208.186.66</strong></p>";
echo "<p><a href='https://whatismyipaddress.com' target='_blank'>Verify at whatismyipaddress.com</a></p>";

echo "<h3>⚡ Quick Test Commands:</h3>";
echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px;'>";
echo "<h4>Test after whitelisting:</h4>";
echo "<p>Visit: <a href='debug_auth.php'>debug_auth.php</a></p>";
echo "<p>Or test payment: <a href='initiate_payment.php'>initiate_payment.php</a></p>";
echo "</div>";

echo "<div style='background-color: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<strong>💡 Note:</strong> ngrok is fastest way to test. Whitelisting may take a few minutes to take effect.";
echo "</div>";

echo "<p><a href='index.php'>← Back to Dashboard</a></p>";
?>
