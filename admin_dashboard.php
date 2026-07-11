<?php
session_start();
include 'config.php' ;

// Security system: Check if you are not logged in and immediately go to the index.php page.
if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

// ========================================================
// Calculate monthly income and expense data for the 12 selected years.
// ========================================================

$revenue_by_months = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
$expense_by_months = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

// Capture the year that the Admin clicked to select from the URL link (if there is no click to select, the current year will be automatically taken).
$selected_year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));

// Pull the income sum
$chart_rev_query = mysqli_query($conn, "SELECT MONTH(order_date) AS month_num, SUM(grand_total) AS total FROM orders WHERE YEAR(order_date) = '$selected_year' GROUP BY MONTH(order_date)");
if($chart_rev_query) {
    while($row = mysqli_fetch_assoc($chart_rev_query)) {
        $month_index = intval($row['month_num']) - 1;
        $revenue_by_months[$month_index] = floatval($row['total']);
    }
}
// Real-time in this year
$chart_exp_query = mysqli_query($conn, "SELECT MONTH(order_date) AS month_num, SUM(total_amount) AS total FROM purchase_orders WHERE YEAR(order_date) = '$selected_year' GROUP BY MONTH(order_date)");
if($chart_exp_query) {
    while($row = mysqli_fetch_assoc($chart_exp_query)) {
        $month_index = intval($row['month_num']) - 1;
        $expense_by_months[$month_index] = floatval($row['total']);
    }
}

// Table Staff
$sql = "SELECT * FROM staff ORDER BY staff_id ASC";
$result = mysqli_query($conn, $sql);

// Notification (SOS)
$count_query = mysqli_query($conn, "SELECT COUNT(id) AS total_unread FROM notifications WHERE status = 'unread'");
$count_result = mysqli_fetch_assoc($count_query);
$unread_count = $count_result['total_unread'] ?? 0;

$notes_query = mysqli_query($conn, "SELECT * FROM notifications WHERE status = 'unread' ORDER BY created_at DESC LIMIT 5");



// ========================================================
//  (DYNAMIC METRICS)
// ========================================================

// (Total Revenue) from Table orders
$query_rev = mysqli_query($conn, "SELECT SUM(grand_total) AS total_revenue FROM orders");
$row_rev = mysqli_fetch_assoc($query_rev);
$total_revenue = $row_rev['total_revenue'] ?? 0.00;

// (Active Customers) from Table customers
$query_cust = mysqli_query($conn, "SELECT COUNT(customer_id) AS total_cust FROM customers");
$row_cust = mysqli_fetch_assoc($query_cust);
$total_customers = $row_cust['total_cust'] ?? 0;

// (Total Orders)
$query_ord_count = mysqli_query($conn, "SELECT COUNT(order_id) AS total_orders FROM orders");
$row_ord_count = mysqli_fetch_assoc($query_ord_count);
$total_orders = $row_ord_count['total_orders'] ?? 0;

// (Today's Sales)
$today_date = date('Y-m-d');
$query_today = mysqli_query($conn, "SELECT SUM(grand_total) AS today_revenue FROM orders WHERE DATE(order_date) = '$today_date'");
$row_today = mysqli_fetch_assoc($query_today);
$today_revenue = $row_today['today_revenue'] ?? 0.00;

// Download the last 5 rows of sales history
$recent_orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY order_id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - POS System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="logo_icon.png" rel="icon">
    <link rel="stylesheet" href="css&js/admin_dashboard.css">
</head>
<body>

     <nav class="navbar navbar-expand-lg bg-white shadow-sm mb-4 px-4 py-2 fixed-top"> 
        <div class="container-fluid">
            
            <div class="card" style="width: 5rem;">
                <img src="logo.png" class="card-img" alt="...">
            </div>
          
            <div class="collapse navbar-collapse justify-content-center">
                <ul class="navbar-nav gap-4">
                    <li class="nav-item border-bottom border-primary border-3">
                        <a class="nav-link text-primary fw-bold" href="admin_dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
                    </li>
                    <li class="nav-item"><a class="nav-link text-secondary" href="products.php"><i class="fa-solid fa-box"></i> Products</a></li>
                    <li class="nav-item"><a class="nav-link text-secondary" href="procurement.php"><i class="fa-solid fa-cart-arrow-down"></i> Procurement</a></li>
                    <li class="nav-item"><a class="nav-link text-secondary" href="shifts.php">
                        <i class="fa-solid fa-user-clock"></i> Shifts</a>
                    </li>
                    <li class="nav-item"><a class="nav-link text-secondary" href="branches.php"><i class="fa-solid fa-code-branch"></i> Branches</a></li>
                </ul>
            </div>

            <div class="d-flex align-items-center gap-3">
                
                <div class="dropdown me-4"> 
                    <a href="#" class="text-dark text-decoration-none position-relative" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-bell fs-5 text-secondary"></i>
                        <?php if($unread_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                <?php echo $unread_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                        
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="notificationDropdown" style="width: 320px;">
                        <li><h6 class="dropdown-header fw-bold text-success border-bottom pb-2">Notifications</h6></li>
                        
                        <?php if($unread_count > 0): ?>
                            <?php while($note = mysqli_fetch_assoc($notes_query)): ?>
                                <li>
                                    <div class="dropdown-item border-bottom py-2" style="white-space: normal;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-danger fw-bold"><i class="fa-solid fa-circle-exclamation me-1"></i><?php echo $note['type']; ?></small>
                                            <small class="text-muted" style="font-size: 0.7rem;"><?php echo date('d-M-Y H:i', strtotime($note['created_at'])); ?></small>
                                        </div>
                                        <small class="d-block text-dark"><b>Email:</b> <?php echo $note['email']; ?></small>
                                        <small class="d-block text-dark"><b>Phone:</b> <?php echo $note['phone']; ?></small>
                                        
                                        <div class="text-end mt-2">
                                            <a href="action_reset.php?id=<?php echo $note['id']; ?>&email=<?php echo $note['email']; ?>" class="btn btn-sm btn-outline-success py-0" style="font-size: 0.75rem;">Reset Now</a>
                                        </div>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                            <li><a class="dropdown-item text-center text-primary mt-2 small" href="#">View All Notifications</a></li>
                        <?php else: ?>
                            <li><span class="dropdown-item text-muted text-center py-3 small">No new notifications</span></li>
                        <?php endif; ?>
                    </ul>
                </div>

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
                    <button class="btn btn-light rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-gear"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li><a class="dropdown-item" href="profile.php"><i class="fa-solid fa-user me-2 text-secondary"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="change_password.php"><i class="fa-solid fa-lock me-2 text-secondary"></i> Change Password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger fw-bold" href="index.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4" style="margin-top: 100px;">
        
        <div class="mb-4">
            <h3 class="fw-bold">Dashboard Overview</h3>
            <p class="text-muted">Welcome back, Admin <?php echo $admin_info['username']; ?>. Here's what's happening today.</p>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl-5 col-lg-6">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                            <h6 class="text-muted mb-2"><i class="fa-solid fa-sack-dollar text-success"></i> Total Revenue</h6>
                            <h3 class="fw-bold text-dark">$ <?php echo number_format($total_revenue, 2); ?></h3>
                            <small class="text-success"><i class="fa-solid fa-arrow-up"></i> Lifetime sales</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                            <h6 class="text-muted mb-2"><i class="fa-solid fa-users text-warning"></i> Members</h6>
                            <h3 class="fw-bold text-dark"><?php echo $total_customers; ?></h3>
                            <small class="text-muted">Registered customers</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                            <h6 class="text-muted mb-2"><i class="fa-solid fa-calendar-day text-primary"></i> Today's Revenue</h6>
                            <h3 class="fw-bold text-primary">$ <?php echo number_format($today_revenue, 2); ?></h3>
                            <small class="text-muted">Real-time daily income</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                            <h6 class="text-muted mb-2"><i class="fa-solid fa-file-invoice text-danger"></i> Total Orders</h6>
                            <h3 class="fw-bold text-dark"><?php echo $total_orders; ?></h3>
                            <small class="text-danger">Invoices processed</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-7 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold mb-0">Financial Analytics</h5>
                            <small class="text-muted">Revenue vs Expenses Overview</small>
                        </div>
                        <select class="form-select w-auto bg-light border-0 rounded-pill fw-bold text-secondary text-center" onchange="window.location.href='admin_dashboard.php?year=' + this.value">
                            <?php
                                $start_year = 2026; 
                                $end_year = date('Y'); 

                                for($y = $start_year; $y <= $end_year; $y++) {
                                    $is_selected = ($y == $selected_year) ? 'selected' : '';
                                    echo "<option value='$y' $is_selected>$y</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div style="position: relative; height: 280px; width: 100%;">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-success mb-0">
                            <i class="fa-solid fa-basket-shopping me-2"></i> Recent Orders 
                        </h5>
                        <span class="badge bg-success rounded-pill px-3 py-2">Last 5 Sales</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date & Time</th>
                                    <th>Total</th>
                                    <th>Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if(mysqli_num_rows($recent_orders) > 0){
                                        while($ord = mysqli_fetch_assoc($recent_orders)){
                                            echo '<tr>';
                                                echo '<td class="fw-bold text-dark">#' . $ord['order_id'] . '</td>';
                                                echo '<td class="text-muted small">' . date('d-M h:i A', strtotime($ord['order_date'])) . '</td>';
                                                echo '<td class="fw-bold text-danger">$' . number_format($ord['grand_total'], 2) . '</td>';
                                                
                                                // បង្ហាញ Badge ប្រភេទលុយឱ្យស្អាត
                                                if(strtoupper($ord['payment_method']) === 'QR') {
                                                    echo '<td><span class="badge bg-primary rounded-pill px-2">QR</span></td>';
                                                } else {
                                                    echo '<td><span class="badge bg-success rounded-pill px-2">Cash</span></td>';
                                                }
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="4" class="text-center text-muted py-4">No sales records found today!</td></tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-secondary mb-0"><i class="fa-solid fa-users-gear me-2"></i> Staff Management</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary">
                                <tr><th>Full Name</th><th>Position</th><th>Phone</th><th>Hire Date</th></tr>
                            </thead>
                            <tbody>
                                <?php
                                    if(mysqli_num_rows($result) > 0){
                                      while($row = mysqli_fetch_assoc($result)){
                                            echo '<tr>';
                                                echo '<td class="fw-bold text-dark">'.$row['full_name'].'</td>';
                                                echo '<td><span class="badge bg-light text-dark border">'.$row['position'].'</span></td>';
                                                echo '<td class="text-muted small">'.$row['phone'].'</td>';
                                                echo '<td class="text-muted small">'.$row['hire_date'].'</td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="4" class="text-center text-danger py-4">No staff data in the database!</td></tr>';
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        window.dynamicRevenue = <?php echo json_encode($revenue_by_months); ?>;
        window.dynamicExpenses = <?php echo json_encode($expense_by_months); ?>;
    </script>

    <script src="css&js/chart_analytic.js?v=<?php echo time(); ?>"></script>
</body>
</html>