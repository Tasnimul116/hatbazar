<?php
session_start();
include_once '../config/database.php';

// Check if the user is an agent and logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header("Location: ../views/login.php");
    exit();
}

// Fetch the username from the session
$agentName = isset($_SESSION['username']) ? $_SESSION['username'] : "Unknown Agent";

// Fetch all farmers (users with 'farmer' role)
$sql_farmers = "SELECT id, username, email FROM users WHERE role = 'farmer'";
$result_farmers = $conn->query($sql_farmers);

$farmers = [];
if ($result_farmers->num_rows > 0) {
    while ($row = $result_farmers->fetch_assoc()) {
        $farmers[] = [
            'id' => $row['id'],
            'username' => $row['username'],
            'email' => $row['email']
        ];
    }
} else {
    echo "No farmers available.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard | Hatbazar</title>
    <link rel="stylesheet" href="../public/css/agent.css">
    <script>
       function openMessageForm(farmerId) {
    const messageForm = document.getElementById('message-form-' + farmerId);
    messageForm.style.display = 'block'; // Show the message form
}

function sendMessage(farmerId) {
    const form = document.getElementById('message-form-' + farmerId);
    const message = form.querySelector('textarea[name="message"]').value;

    const formData = new FormData();
    formData.append('action', 'sendMessage');
    formData.append('farmerId', farmerId);
    formData.append('message', message);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../controllers/agentController.php', true);

    // Set up the callback for when the request finishes
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            const data = JSON.parse(xhr.responseText);
            if (data.success) {
                alert('Message sent successfully!');
                form.style.display = 'none'; // Hide the form after sending the message

                // Call the fetchChatHistory immediately after the message is sent
                fetchChatHistory(farmerId); 
                location.reload();
            } else {
                alert('Error sending message: ' + (data.message || 'Unknown error'));
            }
        } else {
            alert('Request failed with status ' + xhr.status);
        }
    };

    xhr.onerror = function() {
        alert('Network error. Please try again later.');
    };

    // Send the request
    xhr.send(formData);
}

function fetchChatHistory(farmerId) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `../controllers/agentController.php?farmerId=${farmerId}`, true);

    // Set up the callback for when the request finishes
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            const data = JSON.parse(xhr.responseText);
            const chatBox = document.getElementById('chat-box-' + farmerId);
            if (data.success) {
                let messageHtml = '';
                data.messages.forEach(msg => {
                    const sender = msg.sender === 'You' ? 'You' : msg.sender;
                    messageHtml += `<div class="message"><strong>${sender}:</strong> ${msg.message}</div>`;
                });
                chatBox.innerHTML = messageHtml; // Update chat box with the new messages
                scrollToBottom(farmerId); // Optionally, scroll to the bottom of the chat
            } else {
                chatBox.innerHTML = "<p>Error loading messages.</p>";
            }
        } else {
            alert('Request failed with status ' + xhr.status);
        }
    };

    xhr.onerror = function() {
        alert('Network error. Please try again later.');
    };

    // Send the request
    xhr.send();
}

// Optional: Scroll to the bottom of the chat for new messages
function scrollToBottom(farmerId) {
    const chatBox = document.getElementById('chat-box-' + farmerId);
    chatBox.scrollTop = chatBox.scrollHeight;
}


    </script>
</head>
<body>
    <header>
        <div class="logo">
            <h1>Hatbazar</h1>
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="../controllers/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="welcome">
            <h2>Welcome, <span><?php echo htmlspecialchars($agentName); ?></span></h2>
        </section>

        <section>
            <div id="farmer-list">
                <?php foreach ($farmers as $farmer): ?>
                    <div class="farmer">
                        <h3><?= htmlspecialchars($farmer['username']) ?></h3>
                        <p>Email: <?= htmlspecialchars($farmer['email']) ?></p>

                        <div class="chat-box" id="chat-box-<?= $farmer['id'] ?>">
                            <?php
                            $sql_messages = "SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)";
                            $stmt_messages = $conn->prepare($sql_messages);
                            $stmt_messages->bind_param("iiii", $_SESSION['user_id'], $farmer['id'], $farmer['id'], $_SESSION['user_id']);
                            $stmt_messages->execute();
                            $result_messages = $stmt_messages->get_result();

                            if ($result_messages->num_rows > 0) {
                                while ($message = $result_messages->fetch_assoc()) {
                                    $sender = ($message['sender_id'] == $_SESSION['user_id']) ? 'You' : $farmer['username'];
                                    echo "<div class='message'><strong>{$sender}:</strong> {$message['message']}</div>";
                                }
                            } else {
                                echo "<p>No messages yet.</p>";
                            }
                            ?>
                        </div>

                        <button onclick="openMessageForm(<?= $farmer['id'] ?>)">Send Message</button>

                        <form id="message-form-<?= $farmer['id'] ?>" style="display:none;" method="POST" onsubmit="event.preventDefault(); sendMessage(<?= $farmer['id'] ?>)">
                            <input type="hidden" name="farmerId" value="<?= $farmer['id'] ?>">
                            <textarea name="message" placeholder="Enter your message" required></textarea>
                            <button type="submit">Send</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Hatbazar. All rights reserved. | Agricultural Marketplace</p>
    </footer>
</body>
</html>
