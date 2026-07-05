<?php
session_start();
require_once 'config.php';

// ==========================================
//   (REGISTER) - 🟢 ជួសជុលលំហូរកូដឱ្យត្រូវតាមស្តង់ដារសុវត្ថិភាព
// ==========================================
if(isset($_POST['register'])){
    $username  = $conn->real_escape_string(trim($_POST['username']));
    $email     = $conn->real_escape_string(trim($_POST['email']));
    $role      = $conn->real_escape_string($_POST['role']);
    $staff_id  = $conn->real_escape_string(trim($_POST['staff_id']));
    $branch_id = $conn->real_escape_string(trim($_POST['branch_id']));

    $pwd = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // 🔒 ជាន់ទី ១៖ ឆែកមើលថាតើ Staff ID នេះមានវត្តមានពិតប្រាកដក្នុងតារាង staff មេដែរឬទេ
    $checkStaffExist = $conn->query("SELECT staff_id FROM staff WHERE staff_id = '$staff_id'");
    
    // 🔒 ជាន់ទី ២៖ ឆែកមើលថាតើ Staff ID នេះត្រូវបានគេយកទៅបង្កើត User ដែល "ផ្ទៀងផ្ទាត់រួចរាល់ (is_active=1)" ហើយឬនៅ
    $checkStaffUsed = $conn->query("SELECT user_id FROM users WHERE staff_id = '$staff_id' AND is_active = 1");

    // 🔒 ជាន់ទី ៣៖ ឆែកមើល Username ឬ Email ជាន់គ្នាជាមួយគណនីដែល "សកម្ម (is_active=1)"
    $checkUser = $conn->query("SELECT user_id FROM users WHERE (username = '$username' OR email = '$email') AND is_active = 1");

    if($checkStaffExist->num_rows == 0) {
        $_SESSION['register_error'] = 'លេខសម្គាល់បុគ្គលិក (Staff ID) នេះមិនត្រឹមត្រូវទេ!';
        $_SESSION['active_form'] = 'register';
        header("Location: index.php");
        exit();
    } 
    else if($checkStaffUsed->num_rows > 0) {
        $_SESSION['register_error'] = 'Staff ID នេះត្រូវបានចុះឈ្មោះប្រើប្រាស់រួចរាល់ហើយ!';
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
        // 🟢 ប្រសិនបើឆ្លងកាត់ (ឬជាគណនីចាស់ដែលមិនទាន់ផ្ទៀងផ្ទាត់ is_active=0) គឺយើងលុបអាខោន unverified ចាស់នោះចោលសិន ការពារកុំឱ្យជាន់ Key
        $conn->query("DELETE FROM users WHERE (staff_id = '$staff_id' OR email = '$email') AND is_active = 0");

        // 🟢 កែសម្រួលលក្ខខណ្ឌឆែករូបភាពឱ្យកាន់តែហ្មត់ចត់
        $image_name = 'default_profile.png'; 

        if (isset($_FILES['image_staff']) && $_FILES['image_staff']['error'] === UPLOAD_ERR_OK) {
            $original_name = $_FILES['image_staff']['name'];
            $image_name = time() . '_' . $original_name; 
            $target_path = "uploads/" . $image_name;
            
            move_uploaded_file($_FILES['image_staff']['tmp_name'], $target_path);
        }

        // ហៅ File ជំនួយផ្ញើអ៊ីមែលដែលទើបដំឡើងជាមួយ Composer មិញ
        require_once 'mail_helper.php';
        
        // បង្កើតលេខ random ៦ ខ្ទង់ និងម៉ោងផុតកំណត់ ១៥ នាទី
        $otp_code = rand(100000, 999999);
        $expire_time = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        // បញ្ជា Insert ចូល Database តែមួយគត់ (ដោយកំណត់លំនាំដើម is_active = 0 ដើម្បីរង់ចាំ OTP)
        $sql = "INSERT INTO users (username, email, password_hash, role, staff_id, branch_id, image_staff, verification_code, code_expires_at, is_active) 
                VALUES ('$username', '$email', '$pwd', '$role', '$staff_id', '$branch_id', '$image_name', '$otp_code', '$expire_time', 0)";
        
        if($conn->query($sql)){
            
            // 💥 ហៅមុខងារ PHPMailer បាញ់អ៊ីមែលទៅកាន់បុគ្គលិកភ្លាមៗ
            $is_sent = sendVerificationEmail($email, $username, $otp_code);
            
            if($is_sent) {
                $_SESSION['verify_email'] = $email; // កត់ចំណាំទុកយកទៅឆែកនៅទំព័រ OTP
                header("Location: verify_otp.php"); // រុញទៅផ្ទាំងវាយលេខ ៦ ខ្ទង់
            } else {
                $_SESSION['register_error'] = 'ការចុះឈ្មោះជោគជ័យ តែប្រព័ន្ធមានបញ្ហាមិនអាចបាញ់អ៊ីមែល OTP ទៅកាន់ប្រអប់សំបុត្របានទេ !';
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
// (LOGIN) - រក្សាទុកកូដចាស់ដើរធម្មតាត្រឹមត្រូវ
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