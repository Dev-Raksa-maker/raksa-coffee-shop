<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier - Open Shift</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="logo_icon.png" rel="icon">
    <link href="css&js/procurement.css" rel="stylesheet">
</head>
<body class="bg-light"> 

    <div class="container" style="margin-top: 80px;"> 
        <div class="row justify-content-center"> 
            <div class="col-lg-6 col-md-8 mb-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="card" style="width: 5rem;">
                            <img src="logo.png" class="card-img" alt="...">
                        </div>
                        <h4 class="text-center text-success fw-bold mb-4">Welcome to Raksa Coffee Shop.</h4>
                        <?php
                        $current_date = date('Y-m-d');
                        $current_time = date('h:i A'); // សម្រាប់បង្ហាញលើ UI (AM/PM)

                        $user_id = $_SESSION['user_id'];
                        $username = $_SESSION['username'];
                        $branch_id = $_SESSION['branch_id'];

                        // ឆែកមើលវេនលក់ដែលមិនទាន់បិទក្នុងសាខានេះ
                        $check_active = mysqli_query($conn, "SELECT s.*, u.username 
                                                             FROM shifts s 
                                                             JOIN users u ON s.user_id = u.user_id 
                                                             WHERE s.status = 1 AND s.branch_id = '$branch_id'");

                        if (mysqli_num_rows($check_active) > 0) {
                            $active_shift = mysqli_fetch_assoc($check_active);
                            $current_cashier = $active_shift['username'];

                            echo "<div class='alert alert-danger rounded-4 shadow-sm p-4 text-center mb-0'>";
                            echo "<h4><i class='fa-solid fa-ban me-2'></i> Cannot Open Shift!</h4>";
                            echo "<p class='mb-3'>Today <b>$current_cashier</b> still running a sales shift. Cannot run two overlapping shifts on the same machine!</p>";
                            echo "<a href='index.php' class='btn btn-light rounded-pill px-4 border shadow-sm'>Back to Login</a>";
                            echo "</div>";

                            $lock_shift = true; 
                        } else {
                            $lock_shift = false;
                        }
                        ?>

                        <?php if(!$lock_shift): ?>
                            
                            <form action="process_open_shift.php" method="POST">
                                <div class="mb-3">
                                    <label class="text-secondary small fw-bold">Starting Cash</label>
                                    <input type="number" step="0.01" class="form-control text-center fw-bold fs-5 rounded-pill" name="starting_cash" value="100.00" required>
                                </div>

                                <div class="mb-3">
                                    <label class="text-secondary small fw-bold">User ID</label>
                                    <input type="text" class="form-control bg-secondary bg-opacity-10 text-muted rounded-pill" 
                                           value="<?php echo $user_id . ' | ' . htmlspecialchars($username); ?>" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="text-secondary small fw-bold">Shift Date</label>
                                    <input type="text" class="form-control bg-secondary bg-opacity-10 text-muted rounded-pill" 
                                           value="<?php echo date('d-M-Y', strtotime($current_date)); ?>" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="text-secondary small fw-bold">Starting Time</label>
                                    <input type="text" class="form-control bg-secondary bg-opacity-10 text-muted rounded-pill" 
                                           value="<?php echo $current_time; ?>" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="text-secondary small fw-bold">Status</label>
                                    <input type="text" class="form-control bg-success bg-opacity-10 text-success fw-bold rounded-pill" 
                                           value="Open / Ready to Sales" readonly>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <a href="index.php" class="btn btn-light px-4 rounded-pill border">Back</a>
                                    <button type="submit" class="btn btn-success px-5 rounded-pill bg-custom-green border-0 shadow-sm">OPEN SHIFT</button>
                                </div>
                            </form>
                            
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>