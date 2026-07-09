<?php
session_start();
include 'config.php';
require_once 'mail_helper.php';

if (!isset($_SESSION['verify_email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['verify_email'];

$new_otp = rand(100000, 999999);
$new_expire = date('Y-m-d H:i:s', strtotime('+15 minutes'));

// Update to replace old code in Database
$update = $conn->query("UPDATE users SET verification_code = '$new_otp', code_expires_at = '$new_expire' WHERE email = '$email' AND is_active = 0");

if ($update) {
    // Download username to send email
    $user_query = $conn->query("SELECT username FROM users WHERE email = '$email'");
    $user = $user_query->fetch_assoc();
    
    sendVerificationEmail($email, $user['username'], $new_otp);
    
    $_SESSION['resend_success'] = "✅ A new OTP code has been sent to your email.!";
} else {
    $_SESSION['resend_error'] = "There was a problem resending the code.!";
}

header("Location: verify_otp.php");
exit();
?>