<?php
session_start();
include_once '../config/database.php';

// Send Message from Agent to Farmer
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'sendMessage') {
    $senderId = $_SESSION['user_id'];  // Agent's ID
    $receiverId = $_POST['farmerId'];  // Farmer's ID
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $senderId, $receiverId, $message);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}

// Fetch Messages for Agent-Farmer Chat
if (isset($_GET['farmerId'])) {
    $farmerId = $_GET['farmerId'];
    $sql = "SELECT m.id, m.message, m.sent_at, u.username AS sender
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.sent_at ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $_SESSION['user_id'], $farmerId, $farmerId, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode(['success' => true, 'messages' => $messages]);
    exit();
}
?>
