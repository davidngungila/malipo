<?php
/**
 * ngrok Setup Instructions
 * 
 * Use this if you cannot whitelist your local IP in ClickPesa dashboard
 */

echo "<h2>🚀 ngrok Setup for ClickPesa Testing</h2>";

echo "<h3>Step 1: Install ngrok</h3>";
echo "<pre># Download ngrok from https://ngrok.com/download
# Or use chocolatey (Windows)
choco install ngrok</pre>";

echo "<h3>Step 2: Start ngrok tunnel</h3>";
echo "<pre># Run this command in terminal
ngrok http 80</pre>";

echo "<h3>Step 3: Update ClickPesa Dashboard</h3>";
echo "<ol>";
echo "<li>Copy the ngrok HTTPS URL (looks like: https://abc123.ngrok.io)</li>";
echo "<li>Add this URL to ClickPesa IP whitelist</li>";
echo "<li>Update callback URL to: https://abc123.ngrok.io/clickpesa/callback.php</li>";
echo "</ol>";

echo "<h3>Step 4: Update config.php</h3>";
echo "<p>Replace the callback URL in config.php with your ngrok URL:</p>";
echo "<pre>'callback' => [
    'url' => 'https://YOUR_NGROK_URL.ngrok.io/clickpesa/callback.php',
    'secret_key' => '<your-callback-secret>',
],</pre>";

echo "<h3>Current Configuration:</h3>";
echo "<pre>Local IP: 192.168.3.163
Callback URL: http://192.168.3.163/clickpesa/callback.php</pre>";

echo "<h3>⚠️ Important Notes:</h3>";
echo "<ul>";
echo "<li>ngrok URL changes each time you restart ngrok</li>";
echo "<li>Free ngrok has some limitations</li>";
echo "<li>For production, use real server with static IP</li>";
echo "</ul>";

echo "<h3>🔧 Alternative: Use Public IP</h3>";
echo "<p>If you have a public IP, you can:</p>";
echo "<ol>";
echo "<li>Find your public IP: <a href='https://whatismyipaddress.com' target='_blank'>https://whatismyipaddress.com</a></li>";
echo "<li>Configure port forwarding on your router (port 80 → 192.168.3.163:80)</li>";
echo "<li>Whitelist your public IP in ClickPesa dashboard</li>";
echo "<li>Update callback URL to use public IP</li>";
echo "</ol>";

echo "<p><a href='index.php'>← Back to Dashboard</a></p>";
?>
