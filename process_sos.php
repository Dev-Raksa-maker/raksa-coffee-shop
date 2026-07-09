<?php
session_start();
require_once 'config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Data capture and cleaning to prevent hacking (SQL Injection)
    $email = $conn->real_escape_string(trim($_POST['email']));
    $type  = $conn->real_escape_string(trim($_POST['type']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));

    $sql = "INSERT INTO notifications (email, phone, type) 
            VALUES ('$email', '$phone', '$type')";

    if ($conn->query($sql) === TRUE) {

        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

} else {
    header("Location: index.php");
    exit();
}
?>