<?php

// កំណត់ព័ត៌មាន credentials ដែលបានមកពីខាងធនាគារ ABA
$req_time    = date("YmdHis");
$merchant_id = "raksa_coffee_001"; 
$api_key     = "your_aba_secret_api_key_here"; 
$aba_url     = "https://checkout.ababank.com/api/payment-gateway/v1/khqr"; 

$amount = isset($_POST['total_amount']) ? $_POST['total_amount'] : "0.00";

// ចាប់យកលេខវិក្កយបត្រពិតពីប្រព័ន្ធលក់ (ឬបង្កើតអូតូបើមិនទាន់មាន)
$order_id = isset($_POST['invoice_no']) ? $_POST['invoice_no'] : "INV-" . time();

$currency = "USD"; // ឬ "KHR" ទៅតាមការកំណត់ក្នុងហាង
// ធ្វើការចងក្រងទិន្នន័យ និងធ្វើ Hash (Signature)
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

// ប្រើប្រាស់ cURL ដើម្បីបាញ់ដុំទិន្នន័យនេះទៅកាន់ ABA
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $aba_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

// ៦. ផ្ដាច់ដុំ JSON ដែល ABA ផ្ញើមកវិញ
$result = json_decode($response, true);
header('Content-Type: application/json');

//  បើ ABA បោះមកមិនមែនជា JSON (វាជា HTML Error)
if ($result === null) {
    echo json_encode([
        'status' => 'error',
        'message' => 'ABA Server បដិសេធ! អាចមកពីខុស Merchant ID ឬ API Key មិនទាន់ត្រូវស្តង់ដារពិត។',
        'debug_raw' => strip_tags($response) // បង្ហាញសារឆៅដែល ABA ឆ្លើយមក
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
        'message' => $result['description'] ?? 'គណនីនេះត្រូវបានបដិសេធដោយធនាគារ!'
    ]);
}
?>