<?php
session_start();

// Include database connection
 include '../config/database.php';

// Check if the user is logged in and is a farmer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    echo json_encode(["success" => false, "message" => "Unauthorized access."]);
    exit();
}

// Get form data
$cropName = $_POST['cropName'];
$quantity = $_POST['quantity'];
$price = $_POST['price']; // New field: price
$location = $_POST['location'];
$contact = $_POST['contact'];
$deliveryTime = $_POST['deliveryTime'];
$transactionMethod = $_POST['transactionMethod'];
$farmerId = $_SESSION['user_id']; // Get farmer ID from session

// Validate form data
if (empty($cropName) || empty($quantity) || empty($price) || empty($location) || empty($contact) || empty($deliveryTime) || empty($transactionMethod)) {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit();
}

// Insert crop details into the database
$sql = "INSERT INTO products (farmer_id, crop_name, quantity, price, location, contact, delivery_time, transaction_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("isddssss", $farmerId, $cropName, $quantity, $price, $location, $contact, $deliveryTime, $transactionMethod);
    if ($stmt->execute()) {
        echo "<script>
        alert('Crop details submitted successfully');
        window.location.href = '../views/farmerDashboard.php';
    </script>";
    } else {
        echo "<script>
        alert('Error submitting crop details: " . $stmt->error . "');
    </script>";
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
}

$conn->close();
?>