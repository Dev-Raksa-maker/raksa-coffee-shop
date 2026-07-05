<?php
    session_start();
    include 'config.php';

    if(!isset($_SESSION['user_id'])){
        header("Location: index.php");
        exit();
    }

    // ទាញយកទិន្នន័យ Products មកបង្ហាញដោយ JOIN ជាមួយ Categories
    $sql = "SELECT p.*, c.category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            ORDER BY p.product_id ASC";
    
            
    $result = mysqli_query($conn, $sql);

    // ==========================================
    //  Notification (SOS)
    // ==========================================
    $count_query = mysqli_query($conn, "SELECT COUNT(id) AS total_unread FROM notifications WHERE status = 'unread'");
    $count_result = mysqli_fetch_assoc($count_query);
    $unread_count = $count_result['total_unread'];
    $notes_query = mysqli_query($conn, "SELECT * FROM notifications WHERE status = 'unread' ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css&js/admin_dashboard.css">
    <link href="logo_icon.png" rel="icon">
</head>
<body class="bg-light">

     <nav class="navbar navbar-expand-lg bg-white shadow-sm mb-4 px-4 py-2 fixed-top"> 
        <div class="container-fluid">
            
            <div class="card border-0" style="width: 5rem;">
                <img src="logo.png" class="card-img" alt="Logo">
            </div>
            
            <div class="collapse navbar-collapse justify-content-center">
                <ul class="navbar-nav gap-4">
                    <li class="nav-item"><a class="nav-link text-secondary" href="admin_dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                    <li class="nav-item border-bottom border-primary border-3">
                        <a class="nav-link text-primary fw-bold" href="products.php"><i class="fa-solid fa-box"></i> Products</a>
                    </li>
                    <li class="nav-item"><a class="nav-link text-secondary" href="procurement.php"><i class="fa-solid fa-cart-arrow-down"></i> Procurement</a></li>
                    <li class="nav-item"><a class="nav-link text-secondary" href="shifts.php"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-shift-fill" viewBox="0 0 16 16">
                          <path d="M7.27 2.047a1 1 0 0 1 1.46 0l6.345 6.77c.6.638.146 1.683-.73 1.683H11.5v3a1 1 0 0 1-1 1h-5a1 1 0 0 1-1-1v-3H1.654C.78 10.5.326 9.455.924 8.816z"/>
                        </svg> Shifts</a></li>
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
                    $current_user_id = $_SESSION['user_id'];
                    $query_profile = mysqli_query($conn, "SELECT username, email, image_staff FROM users WHERE user_id = '$current_user_id'");
                    $admin_info = mysqli_fetch_assoc($query_profile);
                ?>
                <div class="d-flex align-items-center bg-light border rounded-pill px-3 py-1">
                    <img src="uploads/<?php echo $admin_info['image_staff']; ?>" alt="Profile" class="rounded-circle me-2" width="35" height="35" style="object-fit: cover; border: 1px solid #ccc;">
                    <div style="line-height: 1;">
                        <small class="d-block fw-bold text-dark"><?php echo $admin_info['username']; ?></small>
                        <small class="text-muted" style="font-size: 0.7rem;"><?php echo $admin_info['email']; ?></small>
                    </div>
                </div>
        
                <div class="dropdown">
                    <button class="btn btn-light rounded-circle border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
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
            <h3 class="fw-bold text-success">Products Overview</h3>
            <p class="text-muted">Manage all your coffee, drinks, and bakery items here.</p>
        </div>
            
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Products List</h5>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="text-muted border-bottom">
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                    if(mysqli_num_rows($result) > 0){
                                        while($row = mysqli_fetch_assoc($result)){
                                            echo '<tr>';
                                                echo '<td class="fw-bold text-secondary">#'.$row['product_id'].'</td>';
                                                echo '<td><img src="uploads_product/'.$row['image'].'" class="rounded shadow-sm" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover;"></td>';
                                                echo '<td class="fw-bold">'.$row['product_name'].'</td>';
                                                echo '<td>'.$row['category_name'].'</td>';
                                                echo '<td class="text-success fw-bold">$'.number_format($row['base_price'], 2).'</td>';
                                                
                                                if($row['is_available'] == 1){
                                                    echo '<td><span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Available</span></td>';
                                                } else {
                                                    echo '<td><span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Out of Stock</span></td>';
                                                }
                                                echo '<td>
                                                      <a href="delete_product.php?id=' . $row['product_id'] . '" 
                                                         class="btn btn-sm btn-danger rounded-pill px-3" 
                                                         onclick="return confirm(\'Are you sure you want to delete this product?\');">
                                                          <i class="fa-solid fa-trash me-1"></i> Delete
                                                      </a>
                                                    </td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr>';
                                        echo '<td colspan="6" class="text-center text-muted py-5"><i class="fa-solid fa-box-open fs-1 d-block mb-2 text-light"></i>No Data in the database!</td>';
                                        echo '</tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php 
                $categories_dropdown_query = mysqli_query($conn, "SELECT category_id, category_name FROM categories ORDER BY category_id ASC");
            ?>
            
            <div class="col-lg-4 col-md-5 mb-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="text-center text-success fw-bold mb-4">Insert Product</h4>
                                    
                        <form id="productForm" class="needs-validation" novalidate action="insert_product_process.php" method="POST" enctype="multipart/form-data">
                                    
                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="product_name" id="productNameInput" required placeholder="Ex: Iced Latte">
                                <div class="invalid-feedback">Please enter a product name!</div>
                            </div>
                                    
                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Category <span class="text-danger">*</span></label>
                                <select class="form-select" name="category_id" required>
                                    <option value="" selected disabled>-- Select Category --</option>
                                    <?php 
                                        if(mysqli_num_rows($categories_dropdown_query) > 0) {
                                            while($cat = mysqli_fetch_assoc($categories_dropdown_query)) {
                                                echo '<option value="' . $cat['category_id'] . '">' . htmlspecialchars($cat['category_name']) . '</option>';
                                            }
                                        }
                                    ?>
                                </select>
                                <div class="invalid-feedback">Please select a category!</div>
                            </div>
                                    
                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="is_available" required>
                                    <option value="1">Available</option>
                                    <option value="0">Out of Stock</option>
                                </select>
                            </div>
                                    
                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Unit Price ($) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">$</span>
                                    <input type="number" step="0.01" class="form-control" name="base_price" id="priceInput" required placeholder="0.00">
                                    <div class="invalid-feedback">Please enter a valid price!</div>
                                </div>
                            </div>
                                    
                            <div class="mb-4">
                                <label class="text-secondary small fw-bold">Product Picture <span class="text-danger">*</span></label>
                                <input type="file" name="image" accept="image/*" class="form-control" id="imageInput" required>
                                <div class="invalid-feedback">Please select a product image!</div>
                            </div>
                                    
                            <div class="d-flex justify-content-between mt-4">
                                <button type="reset" id="cancelBtn" class="btn btn-outline-danger px-4 rounded-pill">Cancel</button>
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
                    Product added successfully!
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const form = document.getElementById('productForm');
        const toastLiveExample = document.getElementById('successToast');
        const cancelBtn = document.getElementById('cancelBtn');
    
        form.addEventListener('submit', function (event) {
            // បើបំពេញព័ត៌មានមិនទាន់គ្រប់គ្រាន់ ឬអត់ទាន់បានរើសរូបថត
            if (!form.checkValidity()) {
                event.preventDefault(); 
                event.stopPropagation();
            } else {
                // បើបំពេញត្រឹមត្រូវ និងមានរូបថតគ្រប់គ្រាន់ហើយ
                event.preventDefault(); // ទប់ទំព័រកុំឱ្យទើប Refresh ភ្លាមៗ
                
                // លោតបង្ហាញសារ Toast ជោគជ័យ
                const toast = new bootstrap.Toast(toastLiveExample);
                toast.show();
                
                // ទុកពេល ១.៥ វិនាទីឱ្យ User មើល Toast រួចទើបបាញ់ទិន្នន័យទៅ PHP
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