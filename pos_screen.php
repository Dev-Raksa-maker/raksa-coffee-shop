
<button id="btn-khqr" class="btn btn-success" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">
    <i class="fas fa-qrcode"></i> Payment by ABA KHQR
</button>

<!-- QR Code (display: none) -->
<div id="qr-modal-container" style="display: none; margin-top: 20px; padding: 15px; border: 1px solid #ddd; width: 280px; text-align: center; background: #fff;">
    <h4 style="color: #0056b3; margin-bottom: 10px;">Please scan the payment.</h4>
    
    <!-- imahe QR Code -->
    <div id="qr-image-area"></div>
    
    <p style="font-weight: bold; margin-top: 10px;">Payment: $<span id="qr-amount">0.00</span></p>
</div>

<!--  JavaScript (Fetch API) connect to Backend -->
<script>
document.getElementById('btn-khqr').addEventListener('click', function() {
    let btn = this;
    let qrContainer = document.getElementById('qr-modal-container');
    let qrImageArea = document.getElementById('qr-image-area');
    let qrAmountSpan = document.getElementById('qr-amount');

    btn.innerText = "Creating QR...";
    btn.disabled = true;

    fetch('generate_qr.php')
    .then(response => response.json()) // JSON
    .then(data => {
        
        btn.innerText = "Charge according to ABA KHQR";
        btn.disabled = false;

        if (data.status === 'success') {
            // Show box QR Code
            qrContainer.style.display = 'block';
            
            // Paste the QR Code image from ABA into HTML
            qrImageArea.innerHTML = `<img src="${data.qr_image}" alt="ABA KHQR" style="width: 100%; border: 1px solid #ccc;">`;
            
            // Show money value
            qrAmountSpan.innerText = data.amount;
        } else {
            // Show message if there is an error.
            alert("Can not creating QR Code: " + data.message);
        }
    })
    .catch(error => {
        btn.innerText = "Payment by ABA KHQR";
        btn.disabled = false;
        console.error('Error:', error);
        alert("There is a problem connecting to Server!");
    });
});
</script>