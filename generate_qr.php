<?php

// Set credentials from ABA Bank
$req_time    = date("YmdHis");
$merchant_id = "raksa_coffee_001"; 
$api_key     = "your_aba_secret_api_key_here"; 
$aba_url     = "https://checkout.ababank.com/api/payment-gateway/v1/khqr"; 

$amount = isset($_POST['total_amount']) ? $_POST['total_amount'] : "0.00";

// Capture the real invoice number from the sales system (or auto-generate one if it doesn't exist)
$order_id = isset($_POST['invoice_no']) ? $_POST['invoice_no'] : "INV-" . time();

$currency = "USD"; 
// Compile data and create a Hash (Signature)
$hash_str = $req_time . $merchant_id . $order_id . $amount . $currency;
$signature = base64_encode(hash_hmac('sha512', $hash_str, $api_key, true));

// (Payload)
$fields = [
    'req_time'    => $req_time,
    'merchant_id' => $merchant_id,
    'tran_id'     => $order_id,
    'amount'      => $amount,
    'currency'    => $currency,
    'hash'        => $signature,
    'type'        => 'khqr' 
];

// Use cURL to send this data to ABA.
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $aba_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

// JSON that ABA sends back
$result = json_decode($response, true);
header('Content-Type: application/json');

// If ABA returns something that is not JSON (it is an HTML Error)
if ($result === null) {
    echo json_encode([
        'status' => 'error',
        'message' => 'ABA Server Rejected! May be due to incorrect Merchant ID or API Key not meeting the actual standards.',
        'debug_raw' => strip_tags($response) 
    ]);
    exit;
}

if(isset($result['status']) && $result['status'] == 0) {
    echo json_encode([
        'status' => 'success',
        'qr_image' => $result['qr_image'],
        'amount' => $amount
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => $result['description'] ?? 'This account has been declined by the bank.!'
    ]);
}
?>