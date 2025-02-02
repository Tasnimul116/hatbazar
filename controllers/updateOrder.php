<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['agent_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$order_id = $_GET['order_id'];
$status = $_GET['status'];

try {
    $query = "UPDATE orders SET status = :status WHERE id = :order_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>