<?php
session_start();
 include '../config/database.php';

// Ensure admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../views/login.php");
    exit();
}

// Check if a product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ./adminDashboard.php");
    exit();
}

$product_id = $_GET['id'];

// Fetch product details
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Product not found!";
    exit();
}

$product = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $crop_name = $_POST['crop_name'];
    $quantity = $_POST['quantity'];
    $location = $_POST['location'];
    $contact = $_POST['contact'];
    $delivery_time = $_POST['delivery_time'];
    $transaction_method = $_POST['transaction_method'];

    $update_sql = "UPDATE products SET crop_name = ?, quantity = ?, location = ?, contact = ?, delivery_time = ?, transaction_method = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sissssi", $crop_name, $quantity, $location, $contact, $delivery_time, $transaction_method, $product_id);

    if ($update_stmt->execute()) {
        header("Location: ./adminDashboard.php");
        exit();
    } else {
        echo "Error updating product.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | Hatbazar</title>
    <link rel="stylesheet" href="../public/css/editProduct.css">
</head>
<body>
    <header>
        <h1>Edit Product</h1>
        <a href="./adminDashboard.php">Back to Dashboard</a>
    </header>

    <form method="POST">
    <label>Crop Name:</label>
    <input type="text" name="crop_name" value="<?= htmlspecialchars($product['crop_name']) ?>" required>

    <label>Quantity:</label>
    <input type="number" name="quantity" value="<?= htmlspecialchars($product['quantity']) ?>" required>

    <label>Location:</label>
    <select name="location" required>
        <option value="Dhaka" <?= $product['location'] === 'Dhaka' ? 'selected' : '' ?>>Dhaka</option>
        <option value="Chittagong" <?= $product['location'] === 'Chittagong' ? 'selected' : '' ?>>Chittagong</option>
        <option value="Rajshahi" <?= $product['location'] === 'Rajshahi' ? 'selected' : '' ?>>Rajshahi</option>
        <option value="Khulna" <?= $product['location'] === 'Khulna' ? 'selected' : '' ?>>Khulna</option>
        <option value="Barisal" <?= $product['location'] === 'Barisal' ? 'selected' : '' ?>>Barisal</option>
        <option value="Sylhet" <?= $product['location'] === 'Sylhet' ? 'selected' : '' ?>>Sylhet</option>
    </select>

    <label>Contact:</label>
    <input type="text" name="contact" value="<?= htmlspecialchars($product['contact']) ?>" required>

    <label>Delivery Time:</label>
    <input type="text" name="delivery_time" value="<?= htmlspecialchars($product['delivery_time']) ?>" required>

    <label>Transaction Method:</label>
    <select name="transaction_method" required>
        <option value="Cash" <?= $product['transaction_method'] === 'Cash' ? 'selected' : '' ?>>Cash</option>
        <option value="Bank Transfer" <?= $product['transaction_method'] === 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
        <option value="Mobile Payment" <?= $product['transaction_method'] === 'Mobile Payment' ? 'selected' : '' ?>>Mobile Payment</option>
        <option value="Cheque" <?= $product['transaction_method'] === 'Cheque' ? 'selected' : '' ?>>Cheque</option>
    </select>

    <button type="submit">Update Product</button>
</form>

</body>
</html>
