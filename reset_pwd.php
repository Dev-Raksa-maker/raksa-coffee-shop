<?php
    
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOS - Password Reset Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="logo_icon.png" rel="icon">
    <link href="css&js/procurement.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="bg-custom-green text-white p-3 d-flex align-items-center mb-4 fixed-top shadow-sm">
        <a href="index.php" class="text-white text-decoration-none me-3 fs-4">&larr;</a>
        <h4 class="m-0"><i class="fa-solid fa-bell px-2"></i> Notification</h4>
    </div>

    <div class="container" style="margin-top: 120px;">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8 mb-4">
                <div class="card border-0 shadow rounded-4">
                    <div class="card-body p-4">
                        <h2 class="text-center text-success fw-bold mb-4">SOS Request</h2>
                        <p class="text-center text-muted small mb-4">Please submit your details. Your manager will contact you shortly to reset your password.</p>
                        
                        <form id="sosForm" class="needs-validation" novalidate action="process_sos.php" method="POST">
                            
                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Email</label>
                                <div class="input-group">
                                    <input type="email" class="form-control" name="email" id="emailInput" required>
                                    <div class="invalid-feedback">Please enter a valid email address!</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Purpose</label>
                                <select class="form-select" name="type" required>
                                    <option value="Password Reset">Password Reset</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="text-secondary small fw-bold">Phone Number</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="phone" id="phoneInput" required>
                                    <div class="invalid-feedback">Please enter your phone number!</div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="reset" id="cancelBtn" class="btn btn-outline-danger px-4 rounded-pill">CANCEL</button>
                                <button type="submit" class="btn btn-success px-5 rounded-pill bg-custom-green border-0">SEND</button>
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
                    Your request has been sent to the Manager.
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const form = document.getElementById('sosForm');
        const toastLiveExample = document.getElementById('successToast');
        const cancelBtn = document.getElementById('cancelBtn');
    
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                // បើវាយអត់ទាន់គ្រប់ មិនឱ្យបញ្ជូនទិន្នន័យទេ
                event.preventDefault(); 
                event.stopPropagation();
            } else {
                // បើវាយត្រូវអស់ហើយ
                event.preventDefault(); // ទប់សិន កុំឱ្យ Page លោតទៅបាត់
                
                // លោត Toast បង្ហាញភាពជោគជ័យ
                const toast = new bootstrap.Toast(toastLiveExample);
                toast.show();

                // ពន្យារពេល ១.៥ វិនាទី ទើបបញ្ជូនទិន្នន័យទៅ PHP ដើម្បីឱ្យ User មើល Toast ទាន់
                setTimeout(function() {
                    form.submit(); 
                }, 1500);
            }
            form.classList.add('was-validated'); 
        }, false);

        // ពេលចុច Cancel លុបពណ៌ក្រហមចាប់កំហុសចេញ
        cancelBtn.addEventListener('click', function () {
            form.classList.remove('was-validated');
        });
    </script>
</body>
</html>