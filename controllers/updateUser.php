<?php
session_start();
include_once '../config/database.php';

// Ensure the user is logged in as an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Unauthorized access.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userId'])) {
    $userId = $_POST['userId'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Validate input data
    if (empty($username) || empty($email) || empty($role)) {
        echo "All fields are required.";
        exit();
    }

    // Update user in the database
    $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $username, $email, $role, $userId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "User updated successfully.";
    } else {
        echo "No changes made or error occurred.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

header("Location: ../views/adminDashboard.php");
exit();
?>
