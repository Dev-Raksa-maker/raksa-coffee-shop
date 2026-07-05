<?php
    session_start();
    include 'config.php';

    if(!isset($_SESSION['user_id'])){
        header("Location: index.php");
        exit();
    }

    $items_query = mysqli_query($conn, "SELECT item_id, item_name, unit FROM inventory_items ORDER BY item_id ASC");

    $history_query = mysqli_query($conn, "SELECT pod.*, ii.item_name, po.order_date 
                                          FROM purchase_order_details pod
                                          JOIN inventory_items ii ON pod.item_id = ii.item_id
                                          JOIN purchase_orders po ON pod.po_id = po.po_id
                                          ORDER BY po.order_date DESC LIMIT 10");
                                          
    $suppliers_query = mysqli_query($conn, "SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurement - Restock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="logo_icon.png" rel="icon">
    <link href="css&js/procurement.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="bg-custom-green text-white p-3 d-flex align-items-center mb-4 fixed-top shadow-sm">
        <a href="admin_dashboard.php" class="text-white text-decoration-none me-3 fs-4">&larr;</a>
        <h4 class="m-0"><i class="fa-solid fa-cart-arrow-down px-2"></i> Procurement</h4>
    </div>

    <div class="container-fluid px-4" style="margin-top: 100px;">
        <div class="row">
            
            <div class="col-lg-4 col-md-5 mb-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="text-center text-success fw-bold mb-4">Restock Stock</h4>
                        
                        <form id="restockForm" class="needs-validation" novalidate action="process_restock.php" method="POST">

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Item Name</label>
                                <select class="form-select" name="item_id" required>
                                    <option value="" selected disabled>-- Select Item --</option>
                                    <?php while($item = mysqli_fetch_assoc($items_query)): ?>
                                        <option value="<?php echo $item['item_id']; ?>">
                                            <?php echo htmlspecialchars($item['item_name']); ?> (<?php echo $item['unit']; ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <div class="invalid-feedback">Please select an item!</div>
                            </div>

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Select Supplier</label>
                                <select class="form-select" name="supplier_id" required>
                                    <option value="" selected disabled>-- Select Supplier --</option>
                                    <?php while($sup = mysqli_fetch_assoc($suppliers_query)): ?>
                                        <option value="<?php echo $sup['supplier_id']; ?>">
                                            <?php echo htmlspecialchars($sup['supplier_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <div class="invalid-feedback">Please select a supplier!</div>
                            </div>

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Quantity <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="qty" id="qtyInput" required placeholder="0.00">
                                <div class="invalid-feedback">Invalid Quantity!</div>
                            </div>

                            <div class="mb-3">
                                <label class="text-secondary small fw-bold">Unit Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">$</span>
                                    <input type="number" step="0.01" class="form-control" name="unit_price" id="priceInput" required placeholder="0.00">
                                    <div class="invalid-feedback">Invalid Unit Price!</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="text-secondary small fw-bold">Total Cost</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">$</span>
                                    <input type="text" class="form-control bg-light border-start-0" value="0.00" readonly id="totalOutput">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="reset" id="cancelBtn" class="btn btn-outline-danger px-4 rounded-pill">Cancel</button>
                                <button type="submit" class="btn btn-success px-5 rounded-pill bg-custom-green border-0">SAVE</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 col-md-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold mb-4 text-secondary"><i class="fa-solid fa-history me-2"></i>Recent Purchase Orders</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Item Name</th>
                                    <th>Qty Bought</th>
                                    <th>Unit Price</th>
                                    <th>Total Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($history_query) > 0): ?>
                                    <?php while($row = mysqli_fetch_assoc($history_query)): ?>
                                        <tr>
                                            <td><small class="text-muted"><?php echo date('d-M-Y H:i', strtotime($row['order_date'])); ?></small></td>
                                            <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['item_name']); ?></td>
                                            <td><?php echo number_format($row['qty'], 2); ?></td>
                                            <td class="text-primary">$<?php echo number_format($row['unit_price'], 2); ?></td>
                                            <td class="text-success fw-bold">$<?php echo number_format($row['line_total'], 2); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No restock history found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 start-0 p-3">
        <div id="successToast" class="toast align-items-center bg-white border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body text-success">
                    <strong class="text-dark"><i class="fa-solid fa-circle-check text-success me-2"></i>Notification</strong><br>
                    Restock recorded and stock updated successfully!
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const form = document.getElementById('restockForm');
        const toastLiveExample = document.getElementById('successToast');
        const cancelBtn = document.getElementById('cancelBtn');
        const qtyInput = document.getElementById('qtyInput');
        const priceInput = document.getElementById('priceInput');
        const totalOutput = document.getElementById('totalOutput');
    
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault(); 
                event.stopPropagation();
            } else {
                event.preventDefault(); 
                const toast = new bootstrap.Toast(toastLiveExample);
                toast.show();
                
                setTimeout(function() {
                    form.submit();
                }, 1500);
            }
            form.classList.add('was-validated'); 
        }, false);

        cancelBtn.addEventListener('click', function () {
            form.classList.remove('was-validated');
            totalOutput.value = "0.00";
        });

        function calculateTotal() {
            const qty = parseFloat(qtyInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = qty * price;
            totalOutput.value = total.toFixed(2);
        }

        qtyInput.addEventListener('input', calculateTotal);
        priceInput.addEventListener('input', calculateTotal);
    </script>
</body>
</html>