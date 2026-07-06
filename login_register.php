<?php
session_start();
require_once 'config.php';

// ==========================================
//   (REGISTER) - 🟢 Fix code flow to meet security standards
// ==========================================
if(isset($_POST['register'])){
    $username  = $conn->real_escape_string(trim($_POST['username']));
    $email     = $conn->real_escape_string(trim($_POST['email']));
    $role      = $conn->real_escape_string($_POST['role']);
    $staff_id  = $conn->real_escape_string(trim($_POST['staff_id']));
    $branch_id = $conn->real_escape_string(trim($_POST['branch_id']));

    $pwd = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // 🔒 Step 1: Check if this Staff ID actually exists in the main staff table.
    $checkStaffExist = $conn->query("SELECT staff_id FROM staff WHERE staff_id = '$staff_id'");
    
    // 🔒 Step 2: Check if this Staff ID has been used to create a User that is "verified (is_active=1)" or not.
    $checkStaffUsed = $conn->query("SELECT user_id FROM users WHERE staff_id = '$staff_id' AND is_active = 1");

    // 🔒 Step 3: Check for a username or email that matches an account that is "active (is_active=1)"
    $checkUser = $conn->query("SELECT user_id FROM users WHERE (username = '$username' OR email = '$email') AND is_active = 1");

    if($checkStaffExist->num_rows == 0) {
        $_SESSION['register_error'] = 'This Staff ID is invalid!';
        $_SESSION['active_form'] = 'register';
        header("Location: index.php");
        exit();
    } 
    else if($checkStaffUsed->num_rows > 0) {
        $_SESSION['register_error'] = 'This Staff ID is ready to Sign Up!';
        $_SESSION['active_form'] = 'register';
        header("Location: index.php");
        exit();
    } 
    else if($checkUser->num_rows > 0){
        $_SESSION['register_error'] = 'Username or Email is already taken!';
        $_SESSION['active_form'] = 'register';
        header("Location: index.php");
        exit();
    } 
    else {
        // 🟢 If it passes (or is an old account that has not been verified,
        //  is_active=0), we will delete the old unverified account first to prevent the key from being overwritten.
        $conn->query("DELETE FROM users WHERE (staff_id = '$staff_id' OR email = '$email') AND is_active = 0");

        // 🟢 Adjust image checking conditions to be more thorough.
        $image_name = 'default_profile.png'; 

        if (isset($_FILES['image_staff']) && $_FILES['image_staff']['error'] === UPLOAD_ERR_OK) {
            $original_name = $_FILES['image_staff']['name'];
            $image_name = time() . '_' . $original_name; 
            $target_path = "uploads/" . $image_name;
            
            move_uploaded_file($_FILES['image_staff']['tmp_name'], $target_path);
        }

        // Composer 
        require_once 'mail_helper.php';
        
        // Create the number to random 6 digit and time to 15 min
        $otp_code = rand(100000, 999999);
        $expire_time = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        // Insert command into database only (by default is_active = 0 to wait for OTP)
        $sql = "INSERT INTO users (username, email, password_hash, role, staff_id, branch_id, image_staff, verification_code, code_expires_at, is_active) 
                VALUES ('$username', '$email', '$pwd', '$role', '$staff_id', '$branch_id', '$image_name', '$otp_code', '$expire_time', 0)";
        
        if($conn->query($sql)){
            
            // 💥 Call the PHPMailer function to send emails to employees immediately.
            $is_sent = sendVerificationEmail($email, $username, $otp_code);
            
            if($is_sent) {
                $_SESSION['verify_email'] = $email; // Take note and check on the OTP page
                header("Location: verify_otp.php"); // Scroll to the 6-digit number entry panel
            } else {
                $_SESSION['register_error'] = 'Registration was successful, but the system had a problem and could not send the OTP email to the mailbox!';
                $_SESSION['active_form'] = 'register';
                //header("Location: index.php");
                //exit();
            }
        } else {
            $_SESSION['register_error'] = 'Error registering account: ' . $conn->error;
            $_SESSION['active_form'] = 'register';
            header("Location: index.php");
        }
        exit();  
    }
}

// ==========================================
// (LOGIN) Account
// ==========================================
if(isset($_POST['Login'])){
    $email = $conn->real_escape_string(trim($_POST['email']));
    $pwd   = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email = '$email' AND is_active = 1");
    
    if($result->num_rows > 0){
        $user = $result->fetch_assoc();
        
        if(password_verify($pwd, $user['password_hash'])){ 
            session_regenerate_id(true);
            
            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role']      = $user['role'];
            $_SESSION['branch_id'] = $user['branch_id'];

            if(strtolower($user['role']) == 'admin'){
                header("Location: admin_dashboard.php"); 
            } else {
                header("Location: pos_sales.php"); 
            }
            exit();
        }
    }

    $_SESSION['login_error'] = 'Invalid Email or Password!';
    $_SESSION['active_form'] = 'login'; 
    header("Location: index.php");
    exit();
}
?>