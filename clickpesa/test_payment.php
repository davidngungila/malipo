<?php
// ============================
// CONFIG (TEST ONLY)
// ============================
$client_id = "ID6o6iORRUxzu7FHKts2uUOXSDL3M1HE";
$api_key   = "SKZqqc7Miwmy5U0ssdxy0jSSw2OHDq8oF0wV67f394";
$base_url  = "https://api.clickpesa.com";

// ============================
// HANDLE FORM SUBMIT
// ============================
$responseData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $amount = $_POST['amount'] ?? 1000;
    $phone  = $_POST['phone'] ?? "";
    $email  = $_POST['email'] ?? "";

    $payload = [
        "amount" => $amount,
        "currency" => "TZS",
        "customer" => [
            "phone" => $phone,
            "email" => $email
        ],
        "callback_url" => "https://yourdomain.com/callback.php"
    ];

    $ch = curl_init("$base_url/checkout");

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $api_key",
            "Client-Id: $client_id",
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => json_encode($payload)
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if (!$error) {
        $responseData = json_decode($response, true);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>FEEDTAN PAY – Test Payment</title>
<style>
body{
    font-family: Arial;
    background: linear-gradient(135deg,#1e3c72,#2a5298);
    height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}
.container{
    background:#fff;
    width:400px;
    padding:25px;
    border-radius:12px;
    box-shadow:0 10px 30px rgba(0,0,0,.3);
}
h2{text-align:center;margin-bottom:20px}
input,button{
    width:100%;
    padding:12px;
    margin:8px 0;
    border-radius:6px;
    border:1px solid #ccc;
}
button{
    background:#1e3c72;
    color:white;
    border:none;
    font-weight:bold;
    cursor:pointer;
}
button:hover{background:#16325c}
.response{
    background:#f1f1f1;
    padding:10px;
    margin-top:10px;
    border-radius:6px;
    font-size:13px;
}
.success{color:green}
.error{color:red}
</style>
</head>
<body>

<div class="container">
<h2>💳 FEEDTAN PAY TEST</h2>

<form method="POST">
    <label>Phone (e.g 2557xxxxxxx)</label>
    <input type="text" name="phone" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Amount (TZS)</label>
    <input type="number" name="amount" value="1000" required>

    <button type="submit">Pay Now (USSD PUSH)</button>
</form>

<?php if ($error): ?>
    <div class="response error">Error: <?= $error ?></div>
<?php endif; ?>

<?php if ($responseData): ?>
    <div class="response success">
        <b>Response:</b><br>
        <pre><?= print_r($responseData, true) ?></pre>

        <?php if (!empty($responseData['checkout_url'])): ?>
            <a href="<?= $responseData['checkout_url'] ?>" target="_blank">
                Open Checkout
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

</div>
</body>
</html>