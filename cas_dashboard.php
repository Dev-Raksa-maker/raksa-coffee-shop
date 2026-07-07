<?php
session_start();
include 'config.php' ;

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$category_map = [
    1 => 'coffee',    // Iced Coffee
    2 => 'coffee',    // Hot Coffee
    3 => 'icecream',  // Ice Cream
    4 => 'bread',     // Bread
    5 => 'beverages',
    6 => 'snacks',
];

// ២. សរសេរ SQL Query ទាញយកតែផលិតផលណាដែលបើកលក់ (is_available = 1) 
$product_query = mysqli_query($conn, "SELECT * FROM products WHERE is_available = 1 ORDER BY product_id ASC");
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
                <a href="cas_dashboard.php"><i class="fa-solid fa-store text-white fs-5 border-bottom border-3 py-1" title="Shop"></i></a>
                <a href="order_details.php"><i class="fa-solid fa-rectangle-list text-white fs-5" title="Orders"></i></a>

                <?php
                    $current_user_id = $_SESSION['user_id'];
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
            
            <div class="col-lg-8 col-md-7 d-flex flex-column justify-content-between" style="height: calc(100vh - 120px);">
                
                <div class="bg-white p-3 rounded-4 shadow-sm mb-3">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control border-end-0 rounded-start-pill px-4 py-2" placeholder="Search products here...">
                        <button class="btn btn-success bg-custom-green rounded-end-pill px-4 border-0" type="button">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>

                <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3 flex-grow-1 overflow-y-auto px-1" id="productsGrid">
                    <?php 
                        if(mysqli_num_rows($product_query) > 0) {
                            while($p = mysqli_fetch_assoc($product_query)) {

                                // ចាប់យកអក្សរប្រភេទ (បើអត់មានក្នុង Map ទេ ឱ្យលោតទៅ 'coffee' ជាលំនាំដើម)
                                $js_category = $category_map[$p['category_id']] ?? 'coffee';

                                // ឆែកមើលរូបថត បើគ្មានរូបទេ ឱ្យលោតទៅរូប Default 
                                $image_src = (!empty($p['image'])) ? "uploads_product/" . $p['image'] : "uploads_product/default_product.png";
                    ?>
                                <div class="col product-item" data-name="<?php echo strtolower(htmlspecialchars($p['product_name'])); ?>" data-category="<?php echo $js_category; ?>">
                                    <div class="card h-30 border-0 shadow-sm rounded-4 overflow-hidden product-card" 
                                         onclick="addToCart(<?php echo $p['product_id']; ?>, '<?php echo addslashes($p['product_name']); ?>', <?php echo $p['base_price']; ?>)">

                                        <img src="<?php echo $image_src; ?>" class="card-img-top p-2 rounded-4" alt="product" style="height: 190px; object-fit: cover;">

                                        <div class="card-body text-center p-2 bg-light">
                                            <div class="fw-bold text-dark text-truncate small"><?php echo htmlspecialchars($p['product_name']); ?></div>
                                            <div class="fw-bold text-success mt-1">$<?php echo number_format($p['base_price'], 2); ?></div>
                                        </div>
                                    </div>
                                </div>
                    <?php 
                            }
                        } else {
                            echo '<div class="col-12 text-center text-muted py-5">No products available to sell!</div>';
                        }
                    ?>
                </div>

                <div class="d-flex gap-2 overflow-x-auto py-2 px-2 mt-2">
                    <div class="py-2">
                        <button class="btn category-btn rounded-4 py-2 active" onclick="filterCategory('all', this)">All Menu</button>
                    </div>

                    <div class="py-2">
                        <button class="btn category-btn rounded-4 py-2" onclick="filterCategory('coffee', this)">Coffee</button>
                    </div>
                    
                    <div class="py-2">
                        <button class="btn category-btn rounded-4 py-2" onclick="filterCategory('bread', this)">Bread</button>
                    </div>
                    
                    <div class="py-2">
                        <button class="btn category-btn rounded-4 py-2" onclick="filterCategory('icecream', this)">Ice Cream</button>
                    </div>
                    
                    <div class="py-2">
                        <button class="btn category-btn rounded-4 py-2" onclick="filterCategory('beverages', this)">Beverages</button>
                    </div>
                    
                    <div class="py-2">
                        <button class="btn category-btn rounded-4 py-2" onclick="filterCategory('snacks', this)">Snacks</button>
                    </div>

                </div>

            </div>

            <div class="col-lg-4 col-md-5">
                <div class="checkout-panel shadow-sm p-4">
                    <h3 class="text-center fw-bold text-dark mb-4">Checkout</h3>
                    
                    <div class="row text-secondary small fw-bold border-bottom pb-2 mb-2 px-1">
                        <div class="col-6">Name</div>
                        <div class="col-3 text-center">Qty</div>
                        <div class="col-3 text-end">Price</div>
                    </div>

                    <div class="cart-items-container px-1" id="cartContainer">
                        <div class="text-center text-muted py-5 mt-4">
                            <i class="fa-solid fa-basket-shopping fs-1 text-light d-block mb-2"></i>
                            Cart is empty
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-3 bg-light p-3 rounded-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small fw-bold">Discount (%)</span>
                            <input type="number" id="discountInput" class="form-control text-center fw-bold border-0 shadow-sm" value="0" min="0" max="100" style="width: 70px; height: 32px; border-radius: 8px;" oninput="calculateTotals()">
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary small fw-bold">Sub Total Products</span>
                            <span class="fw-bold text-dark" id="subtotalOutput">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                            <h5 class="fw-bold text-dark mb-0">Total</h5>
                            <h4 class="fw-bold text-success mb-0" id="totalOutput">$0.00</h4>
                        </div>
                    </div>

                    <button class="btn btn-success bg-custom-green w-100 py-3 rounded-4 border-0 fw-bold fs-5 mt-3 shadow" onclick="processPayment()">
                        PAY NOW
                    </button>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header bg-custom-green text-white rounded-top-4">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-cash-register me-2"></i> Payment Process</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <h5 class="text-secondary mb-1">Total Amount Due</h5>
                    <h2 class="text-danger fw-bold mb-4" id="modalTotal">$0.00</h2>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark d-block text-start px-2">Payment Method</label>
                        <div class="d-flex gap-3 justify-content-center">
                            <input type="radio" class="btn-check" name="payMethod" id="payCash" value="Cash" checked onchange="togglePaymentType()">
                            <label class="btn btn-outline-success rounded-pill px-4 fw-bold" for="payCash">
                                <i class="fa-solid fa-money-bill-wave me-2"></i> Cash
                            </label>

                            <input type="radio" class="btn-check" name="payMethod" id="payQR" value="QR" onchange="togglePaymentType()">
                            <label class="btn btn-outline-primary rounded-pill px-4 fw-bold" for="payQR">
                                <i class="fa-solid fa-qrcode me-2"></i> QRcode
                            </label>
                        </div>
                    </div>

                    <div class="mb-4" id="cashInputArea">
                        <label class="form-label fw-bold text-dark">Guest's money ($)</label>
                        <input type="number" id="cashReceived" class="form-control form-control-lg text-center fw-bold text-primary rounded-3" placeholder="0.00" oninput="calculateChange()">
                    </div>
                    
                    <div class="mb-4" id="abaQrArea" style="display: none;">
                        <label class="form-label fw-bold text-primary d-block">ABA PayWay KHQR</label>
                        <div id="qrContainer" class="p-3 border rounded-4 bg-white text-center d-flex justify-content-center align-items-center" style="min-height: 200px;">
                            </div>
                    </div>

                    <div class="bg-light p-3 rounded-4">
                        <h5 class="text-secondary mb-1">Money for guests.</h5>
                        <h3 class="text-success fw-bold mb-0" id="modalChange">$0.00</h3>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success bg-custom-green border-0 rounded-pill px-5" onclick="submitOrder()">Print Receipt & Done</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let cart = [];
        let finalTotalGlobal = 0;
        let subtotalGlobal = 0;    
    
        // ១. មុខងារបន្ថែមទំនិញចូល Cart
        function addToCart(id, name, price) {
            let item = cart.find(p => p.id === id);
            if (item) {
                item.qty++;
            } else {
                cart.push({ id: id, name: name, price: price, qty: 1 });
            }
            renderCart();
        }
    
        // Add/subtract items from cart
        function updateQty(id, change) {
            let item = cart.find(p => p.id === id);
            if (item) {
                item.qty += change;
                if (item.qty <= 0) {
                    cart = cart.filter(p => p.id !== id); // លុបចេញបើថយដល់ 0
                }
            }
            renderCart();
        }
    
        // UI customization feature in Checkout box
        function renderCart() {
            const container = document.getElementById('cartContainer');
            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-5 mt-4">
                        <i class="fa-solid fa-basket-shopping fs-1 text-light d-block mb-2"></i>
                        Cart is empty
                    </div>`;
                calculateTotals();
                return;
            }
    
            let html = '';
            cart.forEach(item => {
                let itemTotal = item.price * item.qty;
                html += `
                <div class="row align-items-center mb-3 px-1">
                    <div class="col-6">
                        <span class="fw-bold d-block text-dark text-truncate small">${item.name}</span>
                        <small class="text-muted">$${item.price.toFixed(2)}</small>
                    </div>
                    <div class="col-3 text-center d-flex align-items-center justify-content-center gap-2">
                        <span class="qty-btn" onclick="updateQty(${item.id}, -1)">-</span>
                        <span class="fw-bold text-dark">${item.qty}</span>
                        <span class="qty-btn" onclick="updateQty(${item.id}, 1)">+</span>
                    </div>
                    <div class="col-3 text-end fw-bold text-dark">$${itemTotal.toFixed(2)}</div>
                </div>`;
            });
            container.innerHTML = html;
            calculateTotals();
        }
    
        // Total money and discount percentage calculation function
        function calculateTotals() {
            let subtotal = 0;
            cart.forEach(item => {
                subtotal += item.price * item.qty;
            });
            
            subtotalGlobal = subtotal; 
    
            let discountPercent = parseFloat(document.getElementById('discountInput').value) || 0;
            let discountAmount = subtotal * (discountPercent / 100);
            let total = subtotal - discountAmount;
            finalTotalGlobal = total;
    
            document.getElementById('subtotalOutput').innerText = `$${subtotal.toFixed(2)}`;
            document.getElementById('totalOutput').innerText = `$${total.toFixed(2)}`;
        }
    
        // (Category Tab Buttons)
        function filterCategory(category, button) {
            document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
    
            document.querySelectorAll('.product-item').forEach(item => {
                if (category === 'all' || item.getAttribute('data-category') === category) {
                    item.classList.remove('d-none');
                } else {
                    item.classList.add('d-none');
                }
            });
        }
    
        // Search the product Real-time
        document.getElementById('searchInput').addEventListener('input', function(e) {
            let keyword = e.target.value.toLowerCase().trim();
            document.querySelectorAll('.product-item').forEach(item => {
                let name = item.getAttribute('data-name');
                if (name.includes(keyword)) {
                    item.classList.remove('d-none');
                } else {
                    item.classList.add('d-none');
                }
            });
        });
    
        // (Modal Pop-up)
        let paymentModalObj;
        function processPayment() {
            if (cart.length === 0) {
                alert("Please choose a coffee face first!");
                return;
            }
            document.getElementById('modalTotal').innerText = `$${finalTotalGlobal.toFixed(2)}`;
            document.getElementById('cashReceived').value = '';
            document.getElementById('modalChange').innerText = '$0.00';
            
            paymentModalObj = new bootstrap.Modal(document.getElementById('paymentModal'));
            paymentModalObj.show();
        }
    
        // Guest payment calculation function
        function calculateChange() {
            let cash = parseFloat(document.getElementById('cashReceived').value) || 0;
            let change = cash - finalTotalGlobal;
            if (change >= 0) {
                document.getElementById('modalChange').innerText = `$${change.toFixed(2)}`;
            } else {
                document.getElementById('modalChange').innerText = '$0.00';
            }
        }

        function togglePaymentType() {
            let isQR = document.getElementById('payQR').checked;
            let cashInputArea = document.getElementById('cashInputArea');
            let cashInput = document.getElementById('cashReceived');
            let abaQrArea = document.getElementById('abaQrArea');
            let qrContainer = document.getElementById('qrContainer');
        
            if (isQR) {
                // Open QR box and close cash box
                cashInputArea.style.display = 'none';
                abaQrArea.style.display = 'block';
                
                // Fill in the amount equal to the total amount.
                cashInput.value = finalTotalGlobal.toFixed(2);
                document.getElementById('modalChange').innerText = '$0.00';
        
                // Shoot to call QR Code from ABA and it will show automatically.
                qrContainer.innerHTML = `<div class="text-muted"><i class="fa-solid fa-spinner fa-spin me-2"></i> Creating KHQR...</div>`;
                
                let formData = new FormData();
                formData.append('total_amount', finalTotalGlobal.toFixed(2)); // Get the real money value from the common variable.
                formData.append('invoice_no', "INV-" + new Date().getTime());
        
                fetch('generate_qr.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Show real image QR Code that give from ABA Bank
                        qrContainer.innerHTML = `<img src="${data.qr_image}" alt="ABA KHQR" style="width: 100%; max-width: 220px; border-radius: 12px;">`;
                    } else {
                        qrContainer.innerHTML = `<b class="text-danger">Error: ${data.message}</b>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    qrContainer.innerHTML = `<b class="text-danger">Can not connect to server!</b>`;
                });
        
            } else {
                // If paying in cash, show the cash box and hide the QR box.
                cashInputArea.style.display = 'block';
                abaQrArea.style.display = 'none';
                
                cashInput.value = '';
                cashInput.readOnly = false;
                document.getElementById('modalChange').innerText = '$0.00';
            }
        }

        // ៩. មុខងារបញ្ចប់ការលក់ និង បាញ់ AJAX ទៅកាន់ PHP
        function submitOrder() {
            let cashReceived = parseFloat(document.getElementById('cashReceived').value) || 0;
        
            if (cashReceived < finalTotalGlobal) {
                alert("Cash received is not enough!");
                return;
            }
        
            // cash_received and cash_change insert into orderData
            let orderData = {
                subtotal: subtotalGlobal,
                discount_amount: subtotalGlobal * ((parseFloat(document.getElementById('discountInput').value) || 0) / 100), 
                grand_total: finalTotalGlobal,
                payment_method: document.querySelector('input[name="payMethod"]:checked').value,
                cash_received: cashReceived, // Cash received
                cash_change: parseFloat(document.getElementById('modalChange').innerText.replace('$', '')) || 0, // Cash change
                customer_id: null, 
                promo_id: null,    
                cart: cart         
            };
        
            // AJAX (Fetch) to Back-end
            fetch('process_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert("🎉 Saling successfully! No. invoice៖ #" + data.order_id);
                    
                    // Clear sale box and close exchange Pop-up
                    cart = [];
                    renderCart();
                    paymentModalObj.hide();
                } else {
                    alert("❌ Have a problem: " + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("❌ Can not connect to server!");
            });
        }

        document.getElementById('btn-khqr').addEventListener('click', function() {
            let btn = this;

            // Get the actual total from the Checkout screen
            // Verify that the total in the cart is the product ID name.
            let realAmount = document.getElementById('total-price').innerText; 
            let invoiceNo = "INV-" + new Date().getTime(); // Create No. invoice

            btn.innerText = "Creating QR...";
            btn.disabled = true;

            // Pack the real data in FormData
            let formData = new FormData();
            formData.append('total_amount', realAmount);
            formData.append('invoice_no', invoiceNo);

            // Shoot this real data packet to Backend
            fetch('generate_qr.php', {
                method: 'POST', 
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                btn.innerText = "Charge according to ABA KHQR";
                btn.disabled = false;

                if (data.status === 'success') {
                    document.getElementById('qr-modal-container').style.display = 'block';
                    document.getElementById('qr-image-area').innerHTML = `<img src="${data.qr_image}" style="width: 100%;">`;
                    document.getElementById('qr-amount').innerText = data.amount; // Show the real value to UI
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => {
                btn.innerText = "Charge according to ABA KHQR";
                btn.disabled = false;
                console.error('Error:', error);
            });
        });

    </script>
</body>
</html>