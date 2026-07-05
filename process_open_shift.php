<?php
session_start();
require_once 'config.php';

// កំណត់ម៉ោងស្រុកខ្មែរ
date_default_timezone_set('Asia/Phnom_Penh');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ១. ចាប់យកទិន្នន័យពី Session និង Form
    $user_id       = $_SESSION['user_id'];
    $branch_id     = $_SESSION['branch_id'];
    $starting_cash = floatval($_POST['starting_cash']);
    
    // ចាប់យកថ្ងៃខែ និង ម៉ោងទម្រង់ ២៤ម៉ោង (H:i:s) ដាច់ខាតត្រូវប្រើទម្រង់នេះទើប MySQL យល់ព្រមរក្សាទុក
    $shift_date    = date('Y-m-d');
    $start_time    = date('H:i:s'); 

    if ($starting_cash < 0) {
        echo "Error: Starting cash cannot be negative!";
        exit();
    }

    // ២. ការពារ ២ ជាន់៖ ឆែកមើលក្នុង Database ម្តងទៀតក្រែងលោមានអ្នកលួចបើកមុនមួយវិនាទីមុន
    $check_again = mysqli_query($conn, "SELECT shift_id FROM shifts WHERE status = 1 AND branch_id = '$branch_id'");
    if (mysqli_num_rows($check_again) > 0) {
        echo "Error: Another cashier just opened a shift! Redirecting...";
        header("Refresh: 2; url=index.php");
        exit();
    }

    // ៣. រូបមន្ត SQL គោះទិន្នន័យចូលតារាង shifts
    // end_time, expected_cash, actual_cash ទុកឱ្យវាដេកជា NULL សិន (ចាំលក់ចប់ទើបបិទវេន)
    $sql = "INSERT INTO shifts (shift_date, start_time, end_time, starting_cash, expected_cash, actual_cash, status, user_id, branch_id) 
            VALUES ('$shift_date', '$start_time', NULL, '$starting_cash', NULL, NULL, 1, '$user_id', '$branch_id')";

    if ($conn->query($sql) === TRUE) {
        
        // 🔴 គន្លឹះពិសេស៖ ចាប់យក shift_id នៃវេនដែលទើបតែបង្កើតនេះ ញាត់ចូលទៅក្នុង Session ទុក
        // ដើម្បីឱ្យទំព័រលក់ POS (cas_dashboard.php) ដឹងថាត្រូវយកកាហ្វេដែលលក់ដាច់ ទៅរាប់ចូលក្នុងវេនលេខប៉ុន្មាន!
        $_SESSION['current_shift_id'] = $conn->insert_id;

        // ជោគជ័យ! បាញ់ Redirect ទៅកាន់ផ្ទាំងលក់ POS របស់ Cashier តែម្តង
        header("Location: cas_dashboard.php");
        exit();
    } else {
        echo "Database Error: " . $conn->error;
    }

} else {
    header("Location: index.php");
    exit();
}
?>