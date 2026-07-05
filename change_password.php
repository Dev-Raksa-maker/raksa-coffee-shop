<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error_msg = "";
$success_msg = "";

$query = mysqli_query($conn, "SELECT email, password_hash FROM users WHERE user_id = '$user_id'");
$user_data = mysqli_fetch_assoc($query);
$current_email = $user_data['email'];
$current_hash = $user_data['password_hash'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (password_verify($old_password, $current_hash)) {
        
        if ($new_password === $confirm_password) {
            
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET password_hash = '$new_hash' WHERE user_id = '$user_id'";
            
            if ($conn->query($update_sql) === TRUE) {
                $success_msg = "Password was changed successfully!";
            }
        } else {
            $error_msg = "New passwords do not match!";
        }
    } else {
        $error_msg = "Your old password is incorrect!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="logo_icon.png" rel="icon">
    <link href="css&js/procurement.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="bg-custom-green text-white p-3 d-flex align-items-center mb-4 fixed-top shadow-sm">
        <a href="admin_dashboard.php" class="text-white text-decoration-none me-3 fs-4">&larr;</a>
        <h4 class="m-0"><i class="fa-solid fa-shield-halved px-2"></i> Change Password</h4>
    </div>

    <div class="container" style="margin-top: 120px;">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8 mb-4">
                
                <?php if($error_msg != ""): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $error_msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if($success_msg != ""): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i> <?php echo $success_msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow rounded-4">
                    <div class="card-body p-4">
                        <h2 class="text-center text-success fw-bold mb-4">Security</h2>
                        <p class="text-center text-muted small mb-4">Update your password to keep your account secure.</p>
                        
                        <form id="changeForm" class="needs-validation" novalidate action="change_password.php" method="POST">

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Email (Read Only)</label>
                                <div class="input-group">
                                    <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($current_email); ?>" readonly>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Old Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="old_password" required placeholder="Enter current password">
                                    <div class="invalid-feedback">Please enter your old password!</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="new_password" id="newPassword" required placeholder="Enter new password">
                                    <div class="invalid-feedback">Please enter a new password!</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="text-secondary small fw-bold">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="confirm_password" id="confirmPassword" required placeholder="Re-type new password">
                                    <div class="invalid-feedback">Passwords do not match!</div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="reset" id="cancelBtn" class="btn btn-outline-danger px-4 rounded-pill">CANCEL</button>
                                <button type="submit" class="btn btn-success px-5 rounded-pill bg-custom-green border-0">SAVE</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const form = document.getElementById('changeForm');
        const newPassword = document.getElementById('newPassword');
        const confirmPassword = document.getElementById('confirmPassword');
        const cancelBtn = document.getElementById('cancelBtn');
    
        form.addEventListener('submit', function (event) {
            
            // ឆែកមើលថាតើប្រអប់លេខថ្មី និងប្រអប់បញ្ជាក់ វាយដូចគ្នាអត់?
            if (newPassword.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity("Passwords do not match");
            } else {
                confirmPassword.setCustomValidity(""); // បើដូចគ្នា លុបការហាមឃាត់ចេញ
            }

            if (!form.checkValidity()) {
                event.preventDefault(); 
                event.stopPropagation();
            } 
            form.classList.add('was-validated'); 
        }, false);

        cancelBtn.addEventListener('click', function () {
            form.classList.remove('was-validated');
        });
    </script>
</body>
</html>