<?php
session_start();
include 'config.php' ;

// ១. ប្រព័ន្ធសុវត្ថិភាព៖ ឆែកមើលបើមិនទាន់ Login ឱ្យដេញចេញទៅទំព័រ index.php ភ្លាម
if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

// Notification (SOS)
$count_query = mysqli_query($conn, "SELECT COUNT(id) AS total_unread FROM notifications WHERE status = 'unread'");
$count_result = mysqli_fetch_assoc($count_query);
$unread_count = $count_result['total_unread'] ?? 0;

$notes_query = mysqli_query($conn, "SELECT * FROM notifications WHERE status = 'unread' ORDER BY created_at DESC LIMIT 5");

// ទាញទិន្នន័យសាខា ភ្ជាប់ជាមួយព័ត៌មាន Manager និងរាប់ចំនួនបុគ្គលិក (Dynamic SQL)
$sql_branches = "
    SELECT 
        b.*, 
        u.username AS manager_name, 
        u.email AS manager_email, 
        u.image_staff AS manager_profile,
        (SELECT COUNT(*) FROM users WHERE branch_id = b.branch_id AND is_active = 1) AS staff_total
    FROM branches b
    LEFT JOIN users u ON b.manager_id = u.user_id
    ORDER BY b.branch_id DESC
";
$branches_query = mysqli_query($conn, $sql_branches);

// ចាប់យកលីងផែនទីសាខាដំបូងគេបង្អស់ ដើម្បីធ្វើជាតម្លៃលំនាំដើមពេលបើកទំព័រដំបូង (Default Map)
$default_map = "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3908.7706798031355!2d104.8872439!3d11.5675841!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13m3!1m2!1s0x3109519fe4616285%3A0x231ff616db6ee777!2sNational%20Technical%20Training%20Institute!5e0!3m2!1sen!2skh!4v1700000000000"; 
$branches_list = [];
while($row = mysqli_fetch_assoc($branches_query)) {
    $branches_list[] = $row;
}
if (!empty($branches_list)) {
    // បើមានទិន្នន័យក្នុង DB គឺយកលីងពី Column location របស់សាខាចុងក្រោយគេមកបង្ហាញមុនគេ
    $default_map = !empty($branches_list[0]['location']) ? $branches_list[0]['location'] : $default_map;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branches Dashboard - POS System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="logo_icon.png" rel="icon">
    <link rel="stylesheet" href="css&js/admin_dashboard.css">
    <style>
        .branch-clickable-card {
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .branch-clickable-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.12) !important;
        }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg bg-white shadow-sm mb-4 px-4 py-2 fixed-top"> 
        <div class="container-fluid">
            <div class="card" style="width: 5rem; border:none;">
                <img src="logo.png" class="card-img" alt="Logo">
            </div>
          
            <div class="collapse navbar-collapse justify-content-center">
                <ul class="navbar-nav gap-4">
                    <li class="nav-item"><a class="nav-link text-secondary" href="admin_dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-secondary" href="products.php"><i class="fa-solid fa-box"></i> Products</a></li>
                    <li class="nav-item"><a class="nav-link text-secondary" href="procurement.php"><i class="fa-solid fa-cart-arrow-down"></i> Procurement</a></li>
                    <li class="nav-item"><a class="nav-link text-secondary" href="shifts.php"><i class="fa-solid fa-user-clock"></i> Shifts</a></li>
                    <li class="nav-item border-bottom border-primary border-3">
                        <a class="nav-link text-primary fw-bold" href="branches.php"><i class="fa-solid fa-code-branch"></i> Branches</a>
                    </li>
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
                            <?php foreach($notes_query as $note): ?>
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
                            <?php endforeach; ?>
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
            <h3 class="fw-bold">Branches Location Overview</h3>
            <p class="text-muted">Welcome back, Admin <?php echo $admin_info['username']; ?>. Here's what's happening today.</p>
        </div>

        <div class="row">
            
            <div class="col-xl-5 col-lg-6 mb-4">
                
                <a href="add_branch.php" class="btn btn-success px-4 py-2 rounded-pill fw-bold d-inline-flex align-items-center gap-2" style="background-color: #2EBA7F; border: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-cloud-plus me-1" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M8 5.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 .5-.5"/>
                      <path d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383m.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z"/>
                    </svg>
                    ADD Branch
                </a>
                
                <?php if(!empty($branches_list)): ?>
                    <?php foreach($branches_list as $branch): ?>
                        <?php 
                            $status_color = ($branch['is_open'] == 1) ? 'bg-success' : 'bg-secondary';
                            $manager_img = !empty($branch['manager_profile']) ? $branch['manager_profile'] : 'default_profile.png';
                        ?>

                        <div class="card mb-3 mt-4 branch-clickable-card" 
                             data-map-url="<?php echo htmlspecialchars($branch['location']); ?>"
                             style="width: 100%; max-width: 530px; border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.06); overflow: hidden;">
                            <div class="row g-0 px-1 py-1">              
                                
                                <div class="col-md-5 position-relative d-flex align-items-center justify-content-center">
                                    <span class="position-absolute top-0 start-0 m-2 <?= $status_color; ?> rounded-circle" 
                                          style="width: 14px; height: 14px; border: 2px solid white; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                    </span>

                                    <button type="button" class="btn btn-sm text-white position-absolute top-0 end-0 m-2 rounded-pill d-flex align-items-center" 
                                            style="background-color: rgba(0, 0, 0, 0.45); border: none; font-size: 11px; padding: 3px 10px; z-index: 10; backdrop-filter: blur(2px);">
                                            Premium
                                    </button>

                                    <img src="image_shop/<?php echo $branch['img_shop']; ?>" class="img-fluid rounded" alt="Coffee Shop" 
                                         style="width: 100%; min-height: 190px; height: 100%; object-fit: cover;">

                                    <div class="position-absolute bottom-0 start-0 m-2 d-flex text-warning" 
                                         style="z-index: 10; filter: drop-shadow(0px 1px 3px rgba(0,0,0,0.9)); font-size: 13px;">
                                        <i class="fa-solid fa-star me-1"></i>
                                        <i class="fa-solid fa-star me-1"></i>
                                        <i class="fa-solid fa-star"></i>
                                    </div> 
                                </div>

                                <div class="col-md-7">
                                    <div class="card-body d-flex flex-column h-100 justify-content-between py-2">
                                        
                                        <div class="d-flex align-items-center text-dark">
                                            <i class="fa-solid fa-location-dot text-danger fs-5 me-2" style="width: 20px;"></i>
                                            <span class="fw-bold text-truncate" style="font-size: 0.95rem;"><?php echo $branch['branch_name']; ?></span>
                                        </div>

                                        <div class="d-flex align-items-center text-secondary">
                                            <i class="fa-solid fa-users text-primary me-2" style="width: 20px;"></i>
                                            <span class="fw-bold small">Staff : <?php echo $branch['staff_total']; ?></span>
                                        </div>

                                        <div class="d-flex align-items-center bg-light rounded-3 p-2 my-1 border" style="border-color: #eaedf1 !important;">
                                            <img src="uploads/<?php echo $manager_img; ?>" alt="Manager Profile" class="rounded-circle me-2" width="32" height="32" style="object-fit: cover; border: 1px solid #ddd;">
                                            <div style="line-height: 1.2; max-width: 150px;">
                                                <small class="d-block fw-bold text-dark text-truncate" style="font-size: 0.8rem;">Manager: <?php echo $branch['manager_name'] ?? 'No Manager'; ?></small>
                                                <small class="text-muted text-truncate d-block" style="font-size: 0.7rem;"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-envelope-at-fill me-1" viewBox="0 0 16 16">
                                                      <path d="M2 2A2 2 0 0 0 .05 3.555L8 8.414l7.95-4.859A2 2 0 0 0 14 2zm-2 9.8V4.698l5.803 3.546zm6.761-2.97-6.57 4.026A2 2 0 0 0 2 14h6.256A4.5 4.5 0 0 1 8 12.5a4.49 4.49 0 0 1 1.606-3.446l-.367-.225L8 9.586zM16 9.671V4.697l-5.803 3.546.338.208A4.5 4.5 0 0 1 12.5 8c1.414 0 2.675.652 3.5 1.671"/>
                                                      <path d="M15.834 12.244c0 1.168-.577 2.025-1.587 2.025-.503 0-1.002-.228-1.12-.648h-.043c-.118.416-.543.643-1.015.643-.77 0-1.259-.542-1.259-1.434v-.529c0-.844.481-1.4 1.26-1.4.585 0 .87.333.953.63h.03v-.568h.905v2.19c0 .272.18.42.411.42.315 0 .639-.415.639-1.39v-.118c0-1.277-.95-2.326-2.484-2.326h-.04c-1.582 0-2.64 1.067-2.64 2.724v.157c0 1.867 1.237 2.654 2.57 2.654h.045c.507 0 .935-.07 1.18-.18v.731c-.219.1-.643.175-1.237.175h-.044C10.438 16 9 14.82 9 12.646v-.214C9 10.36 10.421 9 12.485 9h.035c2.12 0 3.314 1.43 3.314 3.034zm-4.04.21v.227c0 .586.227.8.581.8.31 0 .564-.17.564-.743v-.367c0-.516-.275-.708-.572-.708-.346 0-.573.245-.573.791"/>
                                                    </svg><?php echo $branch['manager_email'] ?? 'N/A'; ?></small>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center text-secondary mb-2">
                                            <i class="fa-solid fa-phone text-success me-2" style="width: 20px;"></i>
                                            <span class="fw-bold small">+855: <?php echo $branch['phone']; ?></span>
                                        </div>

                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="button" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold text-white border-0 copy-btn" style="background-color: #2EBA7F; font-size: 11px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z"/>
                                            </svg>
                                            Copy Location</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted mt-4">No data in the Database!</p>
                <?php endif; ?>
            </div> 

            <div class="col-xl-7 col-lg-6 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; overflow: hidden; min-height: 550px;">
                    <iframe id="live-google-map" 
                            src="<?php echo $default_map; ?>" 
                            width="100%" 
                            height="100%" 
                            style="border:0; min-height: 550px;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="strict-origin-when-cross-origin">
                    </iframe>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const cards = document.querySelectorAll('.branch-clickable-card');
            const mapIframe = document.getElementById('live-google-map');

            cards.forEach(card => {
                card.addEventListener('click', function(e) {
        
                    if(e.target.classList.contains('copy-btn')) {
                        const url = this.getAttribute('data-map-url');
                        navigator.clipboard.writeText(url);
                        alert("Copied Location successfully.");
                        return;
                    }

                    const mapUrl = this.getAttribute('data-map-url');
                    if(mapUrl && mapUrl.trim() !== "") {
                        mapIframe.src = mapUrl;
                    }
                });
            });
        });
    </script>
</body>
</html>