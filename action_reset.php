<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$notif_id = $_GET['id'] ?? '';
$user_email = $_GET['email'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email_to_update = $conn->real_escape_string($_POST['email']);
    $id_to_update    = $conn->real_escape_string($_POST['notif_id']);
    
    //(Hash)
    $new_pwd = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $update_user = "UPDATE users SET password_hash = '$new_pwd' WHERE email = '$email_to_update'";
    
    if ($conn->query($update_user) === TRUE) {
        // If the password change is successful, you must change the message status from unread to read.
        $update_notif = "UPDATE notifications SET status = 'read' WHERE id = '$id_to_update'";
        $conn->query($update_notif);

        header("Location: admin_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Action - Password Reset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="logo_icon.png" rel="icon">
    <link href="css&js/procurement.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="bg-custom-green text-white p-3 d-flex align-items-center mb-4 fixed-top shadow-sm">
        <a href="admin_dashboard.php" class="text-white text-decoration-none me-3 fs-4">&larr;</a>
        <h4 class="m-0"><i class="fa-solid fa-key px-2"></i> Reset Password</h4>
    </div>

    <div class="container" style="margin-top: 120px;">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8 mb-4">
                <div class="card border-0 shadow rounded-4">
                    <div class="card-body p-4">
                        <h2 class="text-center text-success fw-bold mb-4">Solve Problem</h2>
                        <p class="text-center text-muted small mb-4">You can set a new password for this staff member.</p>
                        
                        <form id="resetForm" class="needs-validation" novalidate action="action_reset.php" method="POST">
                            
                            <input type="hidden" name="notif_id" value="<?php echo htmlspecialchars($notif_id); ?>">

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Email</label>
                                <input type="email" class="form-control bg-light" name="email" value="<?php echo htmlspecialchars($user_email); ?>" readonly>
                            </div>

                            <div class="mb-4">
                                <label class="text-secondary small fw-bold">New Password</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="new_password" id="newPasswordInput" required placeholder="Ex: 123456">
                                    <div class="invalid-feedback">Please enter a new password!</div>
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

    <div class="toast-container position-fixed bottom-0 start-0 p-3">
        <div id="successToast" class="toast align-items-center bg-white border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body text-success">
                    <strong class="text-dark"><i class="fa-solid fa-circle-check text-success me-2"></i>Success</strong><br>
                    Password was reset successfully.
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const form = document.getElementById('resetForm');
        const toastLiveExample = document.getElementById('successToast');
        const cancelBtn = document.getElementById('cancelBtn');
    
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault(); 
                event.stopPropagation();
            } else {
                event.preventDefault(); 
                
                const toast = new bootstrap.Toast(toastLiveExample);
                toast.show();

                setTimeout(function() {
                    form.submit(); 
                }, 1500);
            }
            form.classList.add('was-validated'); 
        }, false);

        cancelBtn.addEventListener('click', function () {
            form.classList.remove('was-validated');
        });
    </script>
</body>
</html>