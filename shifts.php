<?php
    session_start();
    include 'config.php';
    
    if(!isset($_SESSION['user_id'])){
        header("Location: index.php");
        exit();
    }

    $sql = "SELECT * FROM shifts ORDER BY shift_id DESC";
    $result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shifts Management - Cashiers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="logo_icon.png" rel="icon">
    <link href="css&js/procurement.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="bg-custom-green text-white p-3 d-flex align-items-center mb-4 fixed-top shadow-sm">
        <a href="admin_dashboard.php" class="text-white text-decoration-none me-3 fs-4">&larr;</a>
        <h4 class="m-0">
            <i class="fa-solid fa-clock-history px-2"></i> Cashier Shifts Log
        </h4>
    </div>

    <div class="container-fluid px-4" style="margin-top: 100px;">
        <div class="row">

            <div class="col-lg-12">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-secondary mb-0"><i class="fa-solid fa-list me-2"></i>Shifts Overview</h5>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th>Shift ID</th>
                                    <th>User ID</th>
                                    <th>Date</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Starting Cash</th>
                                    <th>Expected Cash</th>
                                    <th>Expected QR</th> <th>Actual Cash</th>
                                    <th>Status</th>
                                    <th>Branch ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if(mysqli_num_rows($result) > 0){
                                        while($row = mysqli_fetch_assoc($result)){
                                            echo '<tr>';
                                                echo '<td class="fw-bold text-secondary">#' . $row['shift_id'] . '</td>';
                                                echo '<td class="fw-bold text-dark">' . $row['user_id'] . '</td>';
                                                echo '<td>' . date('d-M-Y', strtotime($row['shift_date'])) . '</td>';

                                                // បង្ហាញ Start Time ជាទម្រង់ AM / PM
                                                echo '<td><span class="badge bg-light text-dark border">' . date('h:i A', strtotime($row['start_time'])) . '</span></td>';

                                                if($row['end_time'] != null){
                                                    echo '<td><span class="badge bg-light text-dark border">' . date('h:i A', strtotime($row['end_time'])) . '</span></td>';
                                                } else {
                                                    echo '<td><span class="text-secondary small bg-warning bg-opacity-10 px-2 py-1 rounded-pill"><i class="fa-solid fa-spinner fa-spin me-1"></i> Ongoing...</span></td>';
                                                }

                                                echo '<td class="fw-bold text-primary">$' . number_format($row['starting_cash'], 2) . '</td>';

                                                if($row['expected_cash'] !== null){
                                                    echo '<td class="fw-bold text-dark">$' . number_format($row['expected_cash'], 2) . '</td>';
                                                } else {
                                                    echo '<td class="text-muted">—</td>';
                                                }

                                                // 🟢 ថែម៖ បង្ហាញទិន្នន័យលុយបាញ់ QRcode (ពណ៌ខៀវ text-info)
                                                if(isset($row['expected_qr']) && $row['expected_qr'] !== null){
                                                    echo '<td class="fw-bold text-info">$' . number_format($row['expected_qr'], 2) . '</td>';
                                                } else {
                                                    echo '<td class="fw-bold text-info">$0.00</td>';
                                                }

                                                if($row['actual_cash'] !== null){
                                                    echo '<td class="fw-bold text-success">$' . number_format($row['actual_cash'], 2) . '</td>';
                                                } else {
                                                    echo '<td class="text-muted">—</td>';
                                                }

                                                if($row['status'] == 1){
                                                    echo '<td><span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill"><i class="fa-solid fa-circle-dot me-1 small"></i> Active</span></td>';
                                                } else {
                                                    echo '<td><span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">Closed</span></td>';
                                                }

                                                echo '<td><span class="text-secondary fw-bold">Branch ' . $row['branch_id'] . '</span></td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr>';
                                        echo '<td colspan="11" class="text-center text-muted py-5"><i class="fa-solid fa-folder-open fs-2 d-block mb-2 text-light"></i>No shift records found!</td>';
                                        echo '</tr>';
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