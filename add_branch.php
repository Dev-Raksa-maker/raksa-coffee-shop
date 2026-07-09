<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$error_msg = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $branch_name = mysqli_real_escape_string($conn, trim($_POST['branch_name']));
    $location    = mysqli_real_escape_string($conn, trim($_POST['location']));
    $phone       = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $manager_id  = trim($_POST['manager_id']);

    if (empty($branch_name) || empty($location) || empty($phone) || empty($manager_id)) {
        $error_msg = "Please input the branch detail!";
    } else {
        
        // Check whether the entered Manager ID is actually stored in the users (User ID) table.
        $manager_id_clean = mysqli_real_escape_string($conn, $manager_id);
        $check_manager = mysqli_query($conn, "SELECT user_id FROM users WHERE user_id = '$manager_id_clean' AND is_active = 1");
        
        if (mysqli_num_rows($check_manager) == 0) {
            //If this ID is not found in the users table, it is rejected immediately.
            $error_msg = "Manager ID (User ID: $manager_id) This is incorrect or there is no account in the system.!";
        } else {
            
            //  Upload the image shop Folder: image_shop/
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                
                $file_name = $_FILES['image']['name'];
                $file_tmp  = $_FILES['image']['tmp_name'];
                
                // Add time() before the file name to avoid overlapping names when uploading.
                $new_image_name = time() . '_' . $file_name;
                $target_folder  = "image_shop/" . $new_image_name;

                // Check if there is no folder image_shop, it will be created automatically.
                if (!is_dir('image_shop')) {
                    mkdir('image_shop', 0777, true);
                }

                if (move_uploaded_file($file_tmp, $target_folder)) {
                    
                    // Pass all the conditions ➔ INSERT into the Database!
                    $sql_insert = "INSERT INTO branches (branch_name, location, phone, manager_id, img_shop, is_open) 
                                   VALUES ('$branch_name', '$location', '$phone', '$manager_id_clean', '$new_image_name', 1)";
                    
                    if (mysqli_query($conn, $sql_insert)) {
                        $success_msg = "Created a new branch was successfully! 🎉";
                        // Push back to branch list page after successful save for 1.5 seconds
                        header("refresh:1.5; url=branches.php");
                    } else {
                        $error_msg = "មានបញ្ហាខាងកូដ SQL បុកចូល Database: " . mysqli_error($conn);
                    }
                } else {
                    $error_msg = "There is a crash, unable to download images to save in the image_shop/ folder!";
                }
            } else {
                $error_msg = "Please select a shop image!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADD Branches - POS System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="logo_icon.png" rel="icon">
    <link href="css&js/procurement.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="bg-custom-green text-white p-3 d-flex align-items-center mb-4 fixed-top shadow-sm">
        <a href="branches.php" class="text-white text-decoration-none me-3 fs-4">&larr;</a>
        <h4 class="m-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-cloud-plus me-3" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M8 5.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 .5-.5"/>
              <path d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383m.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z"/>
            </svg>Live Add Branches
        </h4>
    </div>

    <div class="container" style="margin-top: 120px;">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8 mb-4">
                
                <?php if($error_msg != ""): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $error_msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if($success_msg != ""): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i> <?php echo $success_msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow rounded-4">
                    <div class="card-body p-4">
                        <h2 class="text-center text-success fw-bold mb-4">Create a new branch</h2>
                        <p class="text-center text-muted small mb-4">Please input the branch detail.</p>
                        
                        <form id="changeForm" class="needs-validation" novalidate action="add_branch.php" method="POST" enctype="multipart/form-data">

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Branch Name</label>
                                <input type="text" class="form-control" name="branch_name" required placeholder="Aa">
                                <div class="invalid-feedback">Please enter your branch name!</div>
                            </div>

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Link Location (Google Map URL)</label>
                                <input type="text" class="form-control" name="location" required placeholder="Link here...">
                                <div class="invalid-feedback">Please copy and paste your link location!</div>
                            </div>

                            <div class="mb-4">
                                <label class="text-secondary small fw-bold">Phone Number</label>
                                <input type="text" class="form-control" name="phone" required >
                                <div class="invalid-feedback">Invalid input the phone number!</div>
                            </div>

                            <div class="mb-4">
                                <label class="text-secondary small fw-bold">Manager ID (Must match User ID in system)</label>
                                <input type="text" class="form-control" name="manager_id" required placeholder="User ID of Admin">
                                <div class="invalid-feedback">Please assign a valid Manager User ID!</div> 
                            </div>

                            <div class="mb-4">
                                <label class="text-secondary small fw-bold">Branch Picture <span class="text-danger">*</span></label>
                                <input type="file" name="image" accept="image/*" class="form-control" id="imageInput" required>
                                <div class="invalid-feedback">Please select a shop image!</div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="branches.php" class="btn btn-outline-danger px-4 rounded-pill text-decoration-none">CANCEL</a>
                                <button type="submit" class="btn btn-success px-5 rounded-pill bg-custom-green border-0">SAVE</button>
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