<?php
session_start();
require_once 'config.php';

// ១. ការពារមិនឱ្យអ្នកអត់ Login ចូលមកបាន
if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

// ២. ទាញយកទិន្នន័យដោយភ្ជាប់តារាង users និង staff បញ្ចូលគ្នា (JOIN)
$user_id = $_SESSION['user_id'];
$sql = "SELECT u.email, u.image_staff,u.branch_id, s.full_name, s.position, s.phone 
        FROM users u 
        LEFT JOIN staff s ON u.staff_id = s.staff_id 
        WHERE u.user_id = '$user_id'";

$result = mysqli_query($conn, $sql);
$profile = mysqli_fetch_assoc($result);

// ៣. រៀបចំទិន្នន័យ (បើអត់មានទិន្នន័យ ឱ្យវាលោតពាក្យ N/A)
$email     = $profile['email'] ?? 'N/A';
$full_name = $profile['full_name'] ?? 'N/A';
$position  = $profile['position'] ?? 'N/A';
$phone     = $profile['phone'] ?? 'N/A';
$branch    = $profile['branch_id'] ?? 'N/A';
$image     = !empty($profile['image_staff']) ? $profile['image_staff'] : 'default_profile.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account - Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="logo_icon.png" rel="icon">
    <link href="css&js/procurement.css" rel="stylesheet">
</head>
<body class="bg-light"> <div class="bg-success text-white p-3 d-flex align-items-center mb-4 fixed-top shadow-sm">
        <a href="admin_dashboard.php" class="text-white text-decoration-none me-3 fs-4">&larr;</a>
        <h4 class="m-0"><i class="fa-solid fa-user me-2 text-white"></i> Profile</h4>
    </div>

    <div class="container" style="margin-top: 80px;"> 
        <div class="row justify-content-center"> 
            <div class="col-lg-6 col-md-8 mb-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="text-center text-success fw-bold mb-4">Staff Account</h4>
                        
                        <form>
                            <div class="d-flex justify-content-center mb-4">
                                <img src="uploads/<?php echo $image; ?>" alt="Profile" class="rounded-circle shadow-sm" width="150" height="150" style="object-fit: cover; border: 3px solid #198754;">  
                            </div>

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Email</label>
                                <input type="text" class="form-control bg-light" value="<?php echo $email; ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Full Name</label>
                                <input type="text" class="form-control bg-light" value="<?php echo $full_name; ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Position</label>
                                <input type="text" class="form-control bg-light" value="<?php echo $position; ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Phone Number</label>
                                <input type="text" class="form-control bg-light" value="<?php echo $phone; ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Work At</label>
                                <input type="text" class="form-control bg-light" value="RAKSA COFFEE" readonly>
                            </div>

                            <div class="mb-4">
                                <label class="text-secondary small fw-bold">Branch ID</label>
                                <input type="text" class="form-control bg-light" value="<?php echo $branch; ?>" readonly>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>