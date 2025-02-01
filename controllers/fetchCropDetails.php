<?php
session_start();
include_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    echo json_encode([]);
    exit();
}

$farmerId = $_SESSION['user_id'];
$sql = "SELECT crop_name, quantity, price, location, contact, delivery_time, transaction_method FROM products WHERE farmer_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $farmerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $crops = [];

    while ($row = $result->fetch_assoc()) {
        $crops[] = [
            "cropName" => $row['crop_name'],
            "quantity" => $row['quantity'],
            "price" => $row['price'],
            "location" => $row['location'],
            "contact" => $row['contact'],
            "deliveryTime" => $row['delivery_time'],
            "transactionMethod" => $row['transaction_method'],
        ];
    }

    echo json_encode($crops); 
    $stmt->close();
} else {
    echo json_encode([]);
}

$conn->close();
?>