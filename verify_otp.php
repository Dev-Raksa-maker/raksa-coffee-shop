<?php
session_start();
include 'config.php';

// 🔒 ប្រព័ន្ធការពារ៖ ឆែកមើលបើគ្មាន Email ដេកចាំក្នុង Session ទេ ឱ្យដេញត្រលប់ទៅទំព័រដើមវិញភ្លាម
if (!isset($_SESSION['verify_email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['verify_email'];
$error_msg = '';
$success_msg = $_SESSION['resend_success'] ?? ''; // ចាប់យកសារផ្ញើឡើងវិញជោគជ័យ
unset($_SESSION['resend_success']);

// ចាប់យកលេខកូដមកផ្ទៀងផ្ទាត់នៅពេល User ចុចប៊ូតុង Submit
if (isset($_POST['verify_otp'])) {
    $otp_input = $conn->real_escape_string(trim($_POST['otp_code']));
    $current_time = date('Y-m-d H:i:s');

    // រត់ទៅស្វែងរកគណនីយូសឺរតាមរយៈអ៊ីមែលដែលបានកត់ចំណាំទុក
    $query = $conn->query("SELECT * FROM users WHERE email = '$email'");
    
    if ($query->num_rows > 0) {
        $user = $query->fetch_assoc();

        // ១. ឆែកមើលថាតើលេខកូដដែលវាយបញ្ចូល ត្រូវគ្នាជាមួយលេខកូដក្នុង Database ដែរឬទេ
        if ($user['verification_code'] === $otp_input) {
            
            // ២. ឆែកមើលប្រព័ន្ធពេលវេលា ថាតើកូដនេះផុតកំណត់ ១៥ នាទីហើយឬនៅ
            if (strtotime($current_time) <= strtotime($user['code_expires_at'])) {
                
                // 🏆 កូដត្រឹមត្រូវ និងស្ថិតក្នុងម៉ោងសុពលភាព ➔ ធ្វើបច្ចុប្បន្នភាពបើកអាខោនភ្លាម
                $update = $conn->query("UPDATE users SET is_active = 1, verification_code = NULL, code_expires_at = NULL WHERE email = '$email'");
                
                if ($update) {
                    unset($_SESSION['verify_email']); // លុបកត់សម្គាល់អ៊ីមែលចោលដើម្បីសម្អាត Session យាមផ្លូវ
                    
                    $_SESSION['register_success'] = 'ផ្ទៀងផ្ទាត់អ៊ីមែលពិតប្រាកដជោគជ័យហើយ! អាច Login ចូលប្រព័ន្ធបាន។';
                    $_SESSION['active_form'] = 'login'; // បង្ខំឱ្យផ្ទាំងទម្រង់លោតទៅខាង Login អូតូ
                    
                    header("Location: index.php");
                    exit();
                } else {
                    $error_msg = 'ជួបបញ្ហាបច្ចេកទេសក្នុងការរក្សាទុកទិន្នន័យ៖ ' . $conn->error;
                }
            } else {
                $error_msg = '❌ លេខកូដ OTP នេះបានផុតកំណត់ (Expired) រយៈពេល ១៥នាទីហើយ!';
            }
        } else {
            $error_msg = '❌ លេខកូដ OTP មិនត្រឹមត្រូវទេ! សូមពិនិត្យមើលសារក្នុងអ៊ីមែលឡើងវិញ។';
        }
    } else {
        $error_msg = 'រកមិនឃើញគណនីអ៊ីមែលនេះនៅក្នុងប្រព័ន្ធឡើយ!';
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