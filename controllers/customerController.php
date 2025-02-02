<?php
session_start();
include_once '../config/database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['action']) || $_POST['action'] !== 'orderProduct') {
        error_log("Invalid action received.");
        echo json_encode(["success" => false, "message" => "Invalid request."]);
        exit();
    }

    if (!isset($_POST['productId'], $_POST['pickupLocation'], $_POST['dropoffLocation'], $_POST['transactionMethod'])) {
        error_log("Missing required fields.");
        echo json_encode(["success" => false, "message" => "Missing required fields."]);
        exit();
    }

    $productId = intval($_POST['productId']);
    $pickupLocation = $_POST['pickupLocation'];
    $dropoffLocation = $_POST['dropoffLocation'];
    $transactionMethod = $_POST['transactionMethod'];

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "message" => "User not authenticated."]);
        exit();
    }
    $customerId = $_SESSION['user_id'];

    // Check if product exists and has enough quantity
    $checkQuery = "SELECT quantity, transaction_method FROM products WHERE id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Product not found."]);
        exit();
    }

    $product = $result->fetch_assoc();
    if ($product['quantity'] <= 0) {
        echo json_encode(["success" => false, "message" => "Product out of stock."]);
        exit();
    }

    // Validate transaction method
    $availableMethods = explode(',', $product['transaction_method']);
    if (!in_array($transactionMethod, $availableMethods)) {
        echo json_encode(["success" => false, "message" => "Invalid transaction method selected."]);
        exit();
    }

    // Insert order
    $insertQuery = "INSERT INTO orders (customer_id, product_id, pickup_location, dropoff_location, transaction_method) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    if ($stmt) {
        $stmt->bind_param("iisss", $customerId, $productId, $pickupLocation, $dropoffLocation, $transactionMethod);
        if ($stmt->execute()) {
            // Reduce product quantity
            $updateQuery = "UPDATE products SET quantity = quantity - 1 WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("i", $productId);
            $updateStmt->execute();

            echo json_encode(["success" => true, "message" => "Order placed successfully!"]);
        } else {
            error_log("Database Insert Error: " . $stmt->error);
            echo json_encode(["success" => false, "message" => "Database error."]);
        }
        $stmt->close();
    } else {
        error_log("Database Query Preparation Error: " . $conn->error);
        echo json_encode(["success" => false, "message" => "Failed to process request."]);
    }

    $conn->close();
} else {
    error_log("Invalid request method.");
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>
