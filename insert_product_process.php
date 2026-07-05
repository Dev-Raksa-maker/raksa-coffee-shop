<?php
session_start();
require_once 'config.php'; 

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product_name = $conn->real_escape_string(trim($_POST['product_name']));
    $category_id  = intval($_POST['category_id']);
    $is_available = intval($_POST['is_available']);
    $base_price   = floatval($_POST['base_price']);

    $target_dir = "uploads_product/"; // Folder 

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $original_name = basename($_FILES["image"]["name"]);
    $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    $new_file_name = time() . '_' . uniqid() . '.' . $file_extension; 
    
    $target_file = $target_dir . $new_file_name;
    $upload_ok = true;

    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check === false) {
        echo "Error: File is not a valid image.";
        $upload_ok = false;
    }

    if ($_FILES["image"]["size"] > 5000000) {
        echo "Error: Your file is too large (Max 5MB).";
        $upload_ok = false;
    }

    if($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg" && $file_extension != "webp" ) {
        echo "Error: Only JPG, JPEG, PNG & WEBP files are allowed.";
        $upload_ok = false;
    }

    if ($upload_ok) {
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            
            $sql = "INSERT INTO products (product_name, category_id, base_price, is_available, image) 
                    VALUES ('$product_name', '$category_id', '$base_price', '$is_available', '$new_file_name')";

            if ($conn->query($sql) === TRUE) {
                header("Location: products.php");
                exit();
            } else {
                echo "Database Error: " . $conn->error;
            }
            
        } else {
            echo "Error: There was an error uploading your file to the folder.";
        }
    }

} else {
    header("Location: products.php");
    exit();
}
?>