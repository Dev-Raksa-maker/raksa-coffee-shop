<?php
session_start();
require_once 'config.php';

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']); 

    $sql = "UPDATE products SET is_available = 0 WHERE product_id = $product_id";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['delete_success'] = "Product has been archived/hidden successfully!";
    } else {
        $_SESSION['delete_error'] = "Error: " . $conn->error;
    }
}

header("Location: products.php");
exit();
?>