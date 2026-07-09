<?php
session_start();
include 'config.php';

if (!isset($_SESSION['verify_email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['verify_email'];
$error_msg = '';
$success_msg = $_SESSION['resend_success'] ?? ''; 
unset($_SESSION['resend_success']);

// Capture the code to verify when the user clicks the Submit button.
if (isset($_POST['verify_otp'])) {
    $otp_input = $conn->real_escape_string(trim($_POST['otp_code']));
    $current_time = date('Y-m-d H:i:s');

    // Run to find user accounts via saved emails.
    $query = $conn->query("SELECT * FROM users WHERE email = '$email'");
    
    if ($query->num_rows > 0) {
        $user = $query->fetch_assoc();

        // Check if the entered code matches the code in the database.
        if ($user['verification_code'] === $otp_input) {
            
            // Check the system time to see if this code has expired in 15 minutes.
            if (strtotime($current_time) <= strtotime($user['code_expires_at'])) {
                
                $update = $conn->query("UPDATE users SET is_active = 1, verification_code = NULL, code_expires_at = NULL WHERE email = '$email'");
                
                if ($update) {
                    unset($_SESSION['verify_email']); // Delete email notifications to clear the session.
                    
                    $_SESSION['register_success'] = 'Email verification successful! You can log in to the system.';
                    $_SESSION['active_form'] = 'login'; // Force the form pane to jump to the Auto Login side.
                    
                    header("Location: index.php");
                    exit();
                } else {
                    $error_msg = 'Experiencing technical problems storing data: ' . $conn->error;
                }
            } else {
                $error_msg = 'This OTP code has expired for 15 minutes!';
            }
        } else {
            $error_msg = 'The OTP code is incorrect! Please check the email again.';
        }
    } else {
        $error_msg = 'This email account was not found in the system.!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - RAKSA COFFEE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="http://localhost/Raksa_Coffee Project/logo_icon.png?v=1" rel="icon">
    <style>
        body {
            background-color: #71621e; 
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Montserrat', sans-serif;
        }
        .otp-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 450px;
            width: 100%;
        }
        .btn-custom {
            background-color: #2EBA7F; /* ពណ៌បៃតងស្នូល */
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #259666;
            color: white;
        }
        .otp-input {
            letter-spacing: 8px;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="container d-flex justify-content-center">
        <div class="otp-card text-center">
            <div class="mb-4">
                <i class="fa-solid fa-envelope-open-text text-success fs-1"></i>
            </div>
            
            <h3 class="fw-bold text-dark mb-2">Staff Account Verify</h3>
            <p class="text-muted small">The system sent code 6 digit to your email account.<br>
                <b class="text-dark"><?php echo htmlspecialchars($email); ?></b>
            </p>

            <?php if(!empty($error_msg)): ?>
                <div class="alert alert-danger p-2 small border-0" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <?php if(!empty($success_msg)): ?>
                <div class="alert alert-success p-2 small border-0" role="alert">
                    <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <form action="verify_otp.php" method="POST" class="mt-4">
                <div class="mb-3">
                    <label class="form-label small text-secondary fw-semibold">Input code OPT 6 digit here.</label>
                    <input type="text" name="otp_code" class="form-control form-control-lg otp-input border-2" 
                           placeholder="000000" maxlength="6" autocomplete="off" required pattern="\d{6}">
                </div>
                
                <button type="submit" name="verify_otp" class="btn btn-custom btn-lg w-100 rounded-pill my-3 shadow-sm">
                    <i class="fa-solid fa-shield-check me-2"></i> Verify Now
                </button>
                
                <p class="small text-muted mb-0">Are you valid Email incrrect? 
                    <a href="index.php" class="text-decoration-none text-success fw-bold">Back</a>
                </p>

                <p class="small text-muted mb-0">Are not you get a code? 
                    <a href="resend_otp.php" class="text-decoration-none text-primary fw-bold">Resend Code</a>
                </p>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>