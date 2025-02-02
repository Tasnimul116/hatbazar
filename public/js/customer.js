function placeOrder(productId, pickupLocation) {
    document.getElementById('product_id').value = productId;
    document.getElementById('pickup_location').value = pickupLocation;
    document.getElementById('place-order-modal').style.display = 'block';
}

function closeModal() {
    document.getElementById('place-order-modal').style.display = 'none';
}

document.getElementById('place-order-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('place_order', '1');

    fetch('../../controllers/customerController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        alert('Failed to place order. Please try again.');
    });
});