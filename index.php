<?php
session_start();

$error = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? ''
];

// 🟢 ថែម៖ ចាប់យកសារចុះឈ្មោះជោគជ័យ
$register_success = $_SESSION['register_success'] ?? '';

$activeForm = $_SESSION['active_form'] ?? 'login';

session_unset();

function showError($error){
    return !empty($error) ? "<p class=\"error-message\" style=\"color:red; background:#ffe6e6; padding:8px; border-radius:5px;\">$error</p>" : '';
}

// 🟢 ថែម៖ អនុគមន៍បង្ហាញសារពណ៌បៃតងពេល Sign Up ជោគជ័យ
function showSuccess($success){
    return !empty($success) ? "<p class=\"success-message\" style=\"color:green; background:#e6ffe6; padding:8px; border-radius:5px;\">$success</p>" : '';
}

function isActiveForm($formName, $activeForm){
    return $formName === $activeForm ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Sign up - RAKSA COFFEE</title>
    <link rel="stylesheet" href="css&js/login.css">
    <link href="logo_icon.png" rel="icon">
</head>
<body class="montserrat">

    <div class="container">

        <div class="form-box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
            <form action="login_register.php" method="post">
                <h1 align="center">RAKSA COFFEE Shop</h1><br>
                
                <?= showError($error['login']); ?>
                <?= showSuccess($register_success); ?>
                
                <input type="email" name="email" placeholder="Email" autocomplete="off" required>
                <input type="password" name="password" placeholder="Password" required>
                <p><a href="reset_pwd.php">Forgot Password?</a></p>
                <button type="submit" name="Login">Log In</button>
                <p>Do not have an account? <a href="#" onclick="showForm('register-form')">Sign up</a></p>
            </form>
        </div>

        <div class="form-box <?= isActiveForm('register', $activeForm); ?>" id="register-form">
            <form action="login_register.php" method="post" enctype="multipart/form-data">
                <h2>Sign Up Staff</h2>
                <?= showError($error['register']); ?>
                <input type="email" name="email" placeholder="Your Email" autocomplete="off" required>
                <input type="text" name="username" placeholder="Full Name" autocomplete="off" required>
                <input type="password" name="password" placeholder="Create Password" autocomplete="new-password" required>
                
                <select name="role" required>
                    <option value="">--Select Role--</option>
                    <option value="Cashier">Cashier</option>
                    <option value="Admin">Admin</option>
                </select>

                <input type="text" name="staff_id" placeholder="Staff ID" autocomplete="off" required>
                <input type="text" name="branch_id" placeholder="Branch ID" autocomplete="off" required>

                <label class="text-secondary small mt-2">Profile Picture</label>
                <input type="file" name="image_staff" accept="image/*" class="form-control mb-3" required>
                <button type="submit" name="register">Submit</button>

                <p>Already have an account? <a href="#" onclick="showForm('login-form')">Login</a></p>
            </form>
        </div>
    </div>

    <script src="css&js/login.js"></script>
</body>
</html>