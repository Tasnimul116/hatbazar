<?php
session_start();
include_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    echo "Unauthorized access.";
    exit();
}
$customerName = isset($_SESSION['username']) ? $_SESSION['username'] : "Unknown Customer";

$customerId = $_SESSION['user_id'];
$sql = "SELECT id, crop_name, quantity, location, contact, delivery_time, transaction_method FROM products WHERE quantity > 0";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['id'],
            'cropName' => $row['crop_name'],
            'quantity' => $row['quantity'],
            'location' => $row['location'],
            'contact' => $row['contact'],
            'deliveryTime' => $row['delivery_time'],
            'transactionMethod' => $row['transaction_method']
        ];
    }
} else {
    echo "No products available.";
}


?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="../public/css/customer.css">
    <script>
        function placeOrder(productId) {
            const form = document.getElementById('order-form-' + productId);
            const formData = new FormData(form);
            formData.append('action', 'orderProduct');

            fetch('../controllers/customerController.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.text()) // Read response as text first
            .then(text => {
                console.log("Raw response from server:", text);
                try {
                    const data = JSON.parse(text); // Try parsing JSON
                    if (data.success) {
                        alert(data.message || "Order placed successfully!");
                        location.reload();
                    } else {
                        alert(data.message || "Error placing order.");
                    }
                } catch (error) {
                    console.error("JSON Parse Error:", error, "Server Response:", text);
                    alert("Unexpected server response. Check the console.");
                }
            })
            .catch(error => {
                console.error("Fetch Error:", error);
                alert("There was an error placing your order. Check the console.");
            });
        }

      
    </script>
</head>
<body>
    <header>
        <h1>Hatbazar</h1>
        <nav>
            <ul>
                <li><a href="../controllers/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <h2>Welcome, <span><?php echo htmlspecialchars($customerName); ?></span></h2>

    <div id="product-list">
        <?php foreach ($products as $product): ?>
            <div class="product">
                <h3><?= htmlspecialchars($product['cropName']) ?></h3>
                <p>Quantity: <?= htmlspecialchars($product['quantity']) ?></p>
                <p>Location: <?= htmlspecialchars($product['location']) ?></p>
                <p>Farmer Contact: <?= htmlspecialchars($product['contact']) ?></p>
                <p>Delivery Time: <?= htmlspecialchars($product['deliveryTime']) ?></p>
                <p>Payment Methods: <?= htmlspecialchars($product['transactionMethod']) ?></p>

                <!-- Order Form -->
                <form id="order-form-<?= $product['id'] ?>" method="POST" onsubmit="event.preventDefault(); placeOrder(<?= $product['id'] ?>)">
                    <input type="hidden" name="productId" value="<?= $product['id'] ?>">
                    <input type="hidden" name="pickupLocation" value="<?= htmlspecialchars($product['location']) ?>">
                    <input type="text" name="dropoffLocation" placeholder="Enter Dropoff Location" required>
                    
                    <label for="transactionMethod">Select Payment Method:</label>
                    <select name="transactionMethod" required>
                        <?php
                        $methods = explode(',', $product['transactionMethod']);
                        foreach ($methods as $method) {
                            echo "<option value='".htmlspecialchars($method)."'>".htmlspecialchars($method)."</option>";
                        }
                        ?>
                    </select>

                    <button type="submit">Order</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Display agents to customer -->
   

</body>
</html>

<?php $conn->close(); ?>
