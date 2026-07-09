<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user_id']) || !isset($_SESSION['current_shift_id'])){
    header("Location: index.php");
    exit();
}

$user_id  = $_SESSION['user_id'];
$shift_id = $_SESSION['current_shift_id'];

// Retrieve the email information of the actual Cashier who is currently logging in.
$query_user = mysqli_query($conn, "SELECT email FROM users WHERE user_id = '$user_id'");
$user_info  = mysqli_fetch_assoc($query_user);
$email      = $user_info['email'] ?? 'no-email@bakery.com';

// Press Shift to enter the database when pressing the SAVE button.
$error_msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['close_shift_btn'])) {
    
    $actual_cash = floatval($_POST['actual_cash']);
    $end_time    = date('H:i:s'); // form 24h

    // UPDATE Change shift status to closed (status = 0)
    $sql_close = "UPDATE shifts 
                  SET end_time = '$end_time', actual_cash = '$actual_cash', status = 0 
                  WHERE shift_id = '$shift_id' AND user_id = '$user_id'";

    if (mysqli_query($conn, $sql_close)) {
        // Clear the Shift key from the session so that he can open a new Shift the next day.
        unset($_SESSION['current_shift_id']);

        header("Location: index.php");
        exit();
    } else {
        $error_msg = "Error closing shift: " . mysqli_error($conn);
    }
}

// Actual time to display on UI screen (AM/PM format)
$current_time = date('h:i A'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Close Shift</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="logo_icon.png" rel="icon">
    <link href="css&js/procurement.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="bg-custom-green text-white p-3 d-flex align-items-center mb-4 fixed-top shadow-sm">
        <a href="cas_dashboard.php" class="text-white text-decoration-none me-3 fs-4">&larr;</a>
        <h4 class="m-0"><i class="fa-solid fa-right-from-bracket me-2"></i> Close Shift</h4>
    </div>

    <div class="container" style="margin-top: 120px;">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8 mb-4">

                <div class="card border-0 shadow rounded-4">
                    <div class="card-body p-4">
                        <h2 class="text-center text-success fw-bold mb-4">Take Care Yourself.</h2>
                        <p class="text-center text-muted small mb-4">Update your shift to keep your account secure.</p>
                        
                        <?php if(!empty($error_msg)): ?>
                            <div class="alert alert-danger rounded-3 small"><?php echo $error_msg; ?></div>
                        <?php endif; ?>

                        <form class="needs-validation" novalidate action="" method="POST">

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Email</label>
                                <div class="input-group">
                                    <input type="email" class="form-control bg-light rounded-pill px-3 text-muted" value="<?php echo htmlspecialchars($email); ?>" readonly>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">End Time</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light rounded-pill px-3 text-muted" value="<?php echo $current_time; ?>" readonly>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Actual Cash in Drawer</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-pill text-secondary px-3">$</span>
                                    <input type="number" step="0.01" class="form-control border-start-0 rounded-end-pill fw-bold fs-5 text-center text-danger" name="actual_cash" required placeholder="0.00">
                                    <div class="invalid-feedback text-start px-2">Please enter the actual cash in drawer!</div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                <button type="submit" name="close_shift_btn" class="btn btn-danger w-100 py-2 rounded-pill fw-bold shadow-sm">
                                    <i class="fa-solid fa-lock me-2"></i> CLOSE SHIFT & LOGOUT
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        (() => {
          'use strict'
          const forms = document.querySelectorAll('.needs-validation')
          Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
              if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
              }
              form.classList.add('was-validated')
            }, false)
          })
        })()
    </script>
</body>
</html>