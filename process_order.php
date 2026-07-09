<?php
session_start();
require_once 'config.php';

date_default_timezone_set('Asia/Phnom_Penh');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['current_shift_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Session expired! Please login again.']);
    exit();
}

$inputData = json_decode(file_get_contents('php://input'), true);

if (!$inputData || empty($inputData['cart'])) {
    echo json_encode(['status' => 'error', 'message' => 'Empty shopping cart!']);
    exit();
}

$user_id         = $_SESSION['user_id'];
$shift_id        = $_SESSION['current_shift_id'];
$order_date      = date('Y-m-d H:i:s');
$subtotal        = floatval($inputData['subtotal']);
$discount_amount = floatval($inputData['discount_amount']);
$grand_total     = floatval($inputData['grand_total']);
$payment_method  = $inputData['payment_method'] ?? 'Cash';
$cash_received   = floatval($inputData['cash_received']);
$cash_change     = floatval($inputData['cash_change']);
$customer_id     = !empty($inputData['customer_id']) ? intval($inputData['customer_id']) : null;
$promo_id        = !empty($inputData['promo_id']) ? intval($inputData['promo_id']) : null;
$cart            = $inputData['cart'];

$conn->begin_transaction();

try {
    $stmtOrder = $conn->prepare("INSERT INTO orders (order_date, subtotal, discount_amount, grand_total, cash_received, cash_change, payment_method, status, shift_id, user_id, customer_id, promo_id) VALUES (?, ?, ?, ?, ?, ?, ?, 'Paid', ?, ?, ?, ?)");
    
    if (!$stmtOrder) {
        throw new Exception("Prepare order failed: " . $conn->error);
    }

    $stmtOrder->bind_param("sdddddsiiii", $order_date, $subtotal, $discount_amount, $grand_total, $cash_received, $cash_change, $payment_method, $shift_id, $user_id, $customer_id, $promo_id);
    $stmtOrder->execute();
    
    $order_id = $conn->insert_id;

    $stmtDetail = $conn->prepare("INSERT INTO order_details (qty, unit_price, line_total, order_id, product_id) VALUES (?, ?, ?, ?, ?)");
    
    if (!$stmtDetail) {
        throw new Exception("Prepare detail failed: " . $conn->error);
    }

    foreach ($cart as $item) {
        $product_id = intval($item['id']);
        $qty        = intval($item['qty']);
        $unit_price = floatval($item['price']);
        $line_total = $unit_price * $qty;

        $stmtDetail->bind_param("iddii", $qty, $unit_price, $line_total, $order_id, $product_id);
        $stmtDetail->execute();
    }

    $stmtPayment = $conn->prepare("INSERT INTO payments (payment_method, amount_paid, payment_date, order_id) VALUES (?, ?, ?, ?)");
    
    if (!$stmtPayment) {
        throw new Exception("Prepare payment failed: " . $conn->error);
    }

    $stmtPayment->bind_param("sdsi", $payment_method, $grand_total, $order_date, $order_id);
    $stmtPayment->execute();

    // Update expected cash in Shifts
    if ($payment_method === 'QR') {
        $updateShiftSql = "UPDATE shifts SET expected_qr = IFNULL(expected_qr, 0) + $grand_total WHERE shift_id = $shift_id";
    } else {
        $updateShiftSql = "UPDATE shifts SET expected_cash = IFNULL(expected_cash, 0) + $grand_total WHERE shift_id = $shift_id";
    }
    $conn->query($updateShiftSql);

    $conn->commit();

    $stmtOrder->close();
    $stmtDetail->close();
    $stmtPayment->close();
    $conn->close();

    echo json_encode(['status' => 'success', 'order_id' => $order_id]);
    exit();

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Save failed: ' . $e->getMessage()]);
    exit();
}
?>