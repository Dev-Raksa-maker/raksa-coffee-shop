<?php
session_start();
include 'config.php' ;

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

$order_query = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = '$current_user_id' ORDER BY order_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard - Raksa Coffee Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
    <link rel="stylesheet" href="css&js/cas_dashboard.css">
    <link href="http://localhost/Raksa_Coffee Project/logo_icon.png?v=1" rel="icon">
</head>
<body>

    <nav class="navbar navbar-dark bg-custom-green shadow-sm fixed-top px-4 py-2">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="card" style="width: 5rem;">
                    <img src="logo.png" class="card-img" alt="...">
                </div>
                <h4 class="text-white m-0 fw-bold">Raksa Coffee Shop</h4>
            </div>
            
            <div class="d-flex align-items-center gap-4">
                <a href="cas_dashboard.php"><i class="fa-solid fa-store text-white fs-5" title="Shop"></i></a>
                <a href="order_details.php"><i class="fa-solid fa-rectangle-list text-white fs-5 border-bottom border-3 py-1" title="Orders"></i></a>

                <?php
                    $query_profile = mysqli_query($conn, "SELECT username, email, image_staff FROM users WHERE user_id = '$current_user_id'");
                    $admin_info = mysqli_fetch_assoc($query_profile);
                ?>

                <div class="d-flex align-items-center bg-light rounded-pill px-3 py-1">
                    <img src="uploads/<?php echo $admin_info['image_staff']; ?>" alt="Profile" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover; border: 2px solid #ccc;">
                    <div style="line-height: 1;">
                        <small class="d-block fw-bold"><?php echo $admin_info['username']; ?></small>
                        <small class="text-muted" style="font-size: 0.7rem;"><?php echo $admin_info['email']; ?></small>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="btn btn-light rounded-circle py-2 px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-gear"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li>
                            <a class="dropdown-item" href="profile_cashier.php">
                                <i class="fa-solid fa-user me-2 text-secondary"></i> Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="change_pwd_cas.php">
                                <i class="fa-solid fa-lock me-2 text-secondary"></i> Change Password
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger fw-bold" href="close_shift.php">
                                <i class="fa-solid fa-right-from-bracket me-2"></i> Close Shift
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4" style="margin-top: 85px;">
        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-secondary mb-0"><i class="fa-solid fa-list me-2"></i>My Sales Overview</h5>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th>Order ID</th>
                                    <th>User ID</th>
                                    <th>Order Date</th>
                                    <th>Grand Total</th>
                                    <th>Cash Received</th>
                                    <th>Cash Change</th>
                                    <th>Status</th>
                                    <th>Payment Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    if(mysqli_num_rows($order_query) > 0) {
                                        while($row = mysqli_fetch_assoc($order_query)) {
                                ?>
                                            <tr>
                                                <td class="fw-bold text-dark">#<?php echo $row['order_id']; ?></td>
                                                <td><span class="badge bg-light text-dark border px-2 py-1">ID: <?php echo $row['user_id']; ?></span></td>
                                                <td class="text-muted small"><?php echo date('Y-m-d h:i A', strtotime($row['order_date'])); ?></td>
                                                <td class="fw-bold text-danger">$<?php echo number_format($row['grand_total'], 2); ?></td>
                                                <td class="text-success fw-semibold">$<?php echo number_format($row['cash_received'], 2); ?></td>
                                                <td class="text-secondary">$<?php echo number_format($row['cash_change'], 2); ?></td>
                                                <td>
                                                    <span class="badge bg-success rounded-pill px-3 py-1">
                                                        <i class="fa-solid fa-circle-check me-1"></i> <?php echo $row['status']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if(strtoupper($row['payment_method']) === 'QR'): ?>
                                                        <span class="badge bg-primary rounded-pill px-3 py-1">
                                                            <i class="fa-solid fa-qrcode me-1"></i> KHQR
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success rounded-pill px-3 py-1">
                                                            <i class="fa-solid fa-money-bill-wave me-1"></i> Cash
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="8" class="text-center text-muted py-5">
                                        <i class="fa-solid fa-folder-open d-block fs-2 mb-2 text-light">
                                        </i>There is no sales history for this account.!</td></tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>