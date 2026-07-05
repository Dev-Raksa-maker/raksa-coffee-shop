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

// Update ចូលទៅជំនួសកូដចាស់ក្នុង Database
$update = $conn->query("UPDATE users SET verification_code = '$new_otp', code_expires_at = '$new_expire' WHERE email = '$email' AND is_active = 0");

if ($update) {
    // ទាញយកឈ្មោះយូសឺរមកដើម្បីផ្ញើអ៊ីមែល
    $user_query = $conn->query("SELECT username FROM users WHERE email = '$email'");
    $user = $user_query->fetch_assoc();
    
    // 💥 បាញ់អ៊ីមែលកូដថ្មីទៅភ្លាមៗ
    sendVerificationEmail($email, $user['username'], $new_otp);
    
    $_SESSION['resend_success'] = "✅ បានផ្ញើលេខកូដ OTP ថ្មីទៅកាន់អ៊ីមែលរបស់អ្នករួចរាល់ហើយ!";
} else {
    $_SESSION['resend_error'] = "មានបញ្ហាក្នុងការផ្ញើលេខកូដឡើងវិញ!";
}

header("Location: verify_otp.php");
exit();
?>