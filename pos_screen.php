<!-- ១. ប៊ូតុងសម្រាប់ចុចបង្កើត QR Code -->
<button id="btn-khqr" class="btn btn-success" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">
    <i class="fas fa-qrcode"></i> Payment by ABA KHQR
</button>

<!-- ២. ប្រអប់សម្រាប់បង្ហាញ QR Code (ដំបូងឡើយលាក់វាសិន display: none) -->
<div id="qr-modal-container" style="display: none; margin-top: 20px; padding: 15px; border: 1px solid #ddd; width: 280px; text-align: center; background: #fff;">
    <h4 style="color: #0056b3; margin-bottom: 10px;">សូមស្កែនទូទាត់ប្រាក់</h4>
    
    <!-- រូបភាព QR Code នឹងត្រូវញាត់ចូលក្នុង Tag មួយនេះ -->
    <div id="qr-image-area"></div>
    
    <p style="font-weight: bold; margin-top: 10px;">Payment: $<span id="qr-amount">0.00</span></p>
</div>

<!--  JavaScript (Fetch API) សម្រាប់ភ្ជាប់ប៊ូតុងទៅ Backend -->
<script>
document.getElementById('btn-khqr').addEventListener('click', function() {
    let btn = this;
    let qrContainer = document.getElementById('qr-modal-container');
    let qrImageArea = document.getElementById('qr-image-area');
    let qrAmountSpan = document.getElementById('qr-amount');

    // កែប្រែស្ថានភាពប៊ូតុងពេលកំពុងរង់ចាំភ្លើងពីធនាគារ
    btn.innerText = "Creating QR...";
    btn.disabled = true;

    // បាញ់ទៅហៅឯកសារ PHP Backend ដែលយើងបានរៀបចំ
    fetch('generate_qr.php')
    .then(response => response.json()) // បម្លែងលទ្ធផលជា JSON
    .then(data => {
        // បើកប៊ូតុងឱ្យដើរឡើងវិញធម្មតា
        btn.innerText = "គិតលុយតាម ABA KHQR";
        btn.disabled = false;

        if (data.status === 'success') {
            // បើកបង្ហាញប្រអប់ QR Code
            qrContainer.style.display = 'block';
            
            // ញាត់រូបភាព QR Code ដែលបានមកពី ABA ចូលទៅក្នុង HTML
            qrImageArea.innerHTML = `<img src="${data.qr_image}" alt="ABA KHQR" style="width: 100%; border: 1px solid #ccc;">`;
            
            // បង្ហាញតម្លៃលុយ
            qrAmountSpan.innerText = data.amount;
        } else {
            // បង្ហាញសារប្រាប់បើមាន Error
            alert("Can not creating QR Code: " + data.message);
        }
    })
    .catch(error => {
        btn.innerText = "Payment by ABA KHQR";
        btn.disabled = false;
        console.error('Error:', error);
        alert("មានបញ្ហាក្នុងការតភ្ជាប់ទៅកាន់ Server!");
    });
});
</script>