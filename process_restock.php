<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $item_id     = intval($_POST['item_id']);
    $supplier_id = intval($_POST['supplier_id']);
    $qty         = floatval($_POST['qty']);
    $unit_price  = floatval($_POST['unit_price']);
    
    $line_total   = $qty * $unit_price;
    $total_amount = $line_total; 

    if ($qty <= 0 || $unit_price <= 0) {
        echo "Error: Invalid quantity or price!";
        exit();
    }

    // purchase_orders 
    $sql_order = "INSERT INTO purchase_orders (order_date, total_amount, status, supplier_id, branch_id) 
                  VALUES (NOW(), '$total_amount', 'Completed', '$supplier_id', 1)";
    
    if ($conn->query($sql_order) === TRUE) {
        
        $po_id = $conn->insert_id;

        // purchase_order_details
        $sql_details = "INSERT INTO purchase_order_details (qty, unit_price, line_total, po_id, item_id) 
                        VALUES ('$qty', '$unit_price', '$line_total', '$po_id', '$item_id')";
        
        if ($conn->query($sql_details) === TRUE) {

            header("Location: procurement.php");
            exit();

        } else {
            echo "Error inserting purchase_order_details table: " . $conn->error;
        }
    } else {
        echo "Error inserting purchase_orders table: " . $conn->error;
    }

} else {
    header("Location: procurement.php");
    exit();
}
?>