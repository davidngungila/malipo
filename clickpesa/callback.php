<?php
// =============================
// 1) Receive JSON from gateway
// =============================
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Log for debugging (very useful)
file_put_contents("callback_log.txt", date("Y-m-d H:i:s")." ".$rawData.PHP_EOL, FILE_APPEND);

// =============================
// 2) Basic validation
// =============================
if (!$data) {
    http_response_code(400);
    echo json_encode(["status"=>"error","message"=>"Invalid payload"]);
    exit;
}

// Example fields (may vary depending on ClickPesa response)
$transaction_id = $data['transaction_id'] ?? null;
$status         = $data['status'] ?? null;
$amount         = $data['amount'] ?? null;
$phone          = $data['phone'] ?? null;

// =============================
// 3) Process payment status
// =============================
if ($status === "SUCCESS") {

    // TODO: update your database here
    // e.g. mark transaction as paid
    file_put_contents("payments_success.txt",
        "$transaction_id | $amount | $phone".PHP_EOL,
        FILE_APPEND
    );

} else {

    // log failed transactions
    file_put_contents("payments_failed.txt",
        "$transaction_id | FAILED".PHP_EOL,
        FILE_APPEND
    );
}

// =============================
// 4) Respond to gateway
// =============================
http_response_code(200);
echo json_encode([
    "status" => "received"
]);