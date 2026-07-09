<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id       = $_SESSION['user_id'];
    $branch_id     = $_SESSION['branch_id'];
    $starting_cash = floatval($_POST['starting_cash']);
    
    $shift_date    = date('Y-m-d');
    $start_time    = date('H:i:s'); 

    if ($starting_cash < 0) {
        echo "Error: Starting cash cannot be negative!";
        exit();
    }

    // Check the database again in case someone hacked it a second ago.
    $check_again = mysqli_query($conn, "SELECT shift_id FROM shifts WHERE status = 1 AND branch_id = '$branch_id'");
    if (mysqli_num_rows($check_again) > 0) {
        echo "Error: Another cashier just opened a shift! Redirecting...";
        header("Refresh: 2; url=index.php");
        exit();
    }

    // end_time, expected_cash, actual_cash, leave it as NULL (wait until the sale is over before closing the session)
    $sql = "INSERT INTO shifts (shift_date, start_time, end_time, starting_cash, expected_cash, actual_cash, status, user_id, branch_id) 
            VALUES ('$shift_date', '$start_time', NULL, '$starting_cash', NULL, NULL, 1, '$user_id', '$branch_id')";

    if ($conn->query($sql) === TRUE) {
        
        // Get the shift_id of the shift you just created, put it in the Session, and save it.
        // So that the POS sales page (cas_dashboard.php) knows how many coffees sold to count in the shift number!
        $_SESSION['current_shift_id'] = $conn->insert_id;

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