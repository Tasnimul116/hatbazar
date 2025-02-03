<?php
session_start();
 include '../config/database.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    header("Location: ../views/login.php");
    exit();
}

// Fetch the username from the session
$farmerName = isset($_SESSION['username']) ? $_SESSION['username'] : "Unknown Farmer";

// Fetch all agents (users with 'agent' role)
$sql_agents = "SELECT id, username, email FROM users WHERE role = 'agent'";
$result_agents = $conn->query($sql_agents);

$agents = [];
if ($result_agents->num_rows > 0) {
    while ($row = $result_agents->fetch_assoc()) {
        $agents[] = [
            'id' => $row['id'],
            'username' => $row['username'],
            'email' => $row['email']
        ];
    }
} else {
    echo "No agents available.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard | Hatbazar</title>
    <link rel="stylesheet" href="../public/css/farmer.css">
    <script>
        function openMessageForm(agentId) {
            const messageForm = document.getElementById('message-form-' + agentId);
            messageForm.style.display = 'block'; // Show the message form
        }

        function sendMessage(agentId) {
            const form = document.getElementById('message-form-' + agentId);
            const formData = new FormData(form);
            formData.append('action', 'sendMessage');

            fetch('../controllers/messageController.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Message sent successfully!');
                    form.style.display = 'none'; // Hide the form after sending the message

                    // Refresh the chat history for the specific agent
                    fetchChatHistory(agentId);
                    location.reload();

                } else {
                    alert('Error sending message.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to send message.');
            });
        }

        function fetchChatHistory(agentId) {
            fetch(`../controllers/messageController.php?agentId=${agentId}`)
                .then(response => response.json())
                .then(data => {
                    const chatBox = document.getElementById('chat-box-' + agentId);
                    if (data.success) {
                        let messageHtml = '';
                        data.messages.forEach(msg => {
                            messageHtml += `<div class="message"><strong>${msg.sender}:</strong> ${msg.message}</div>`;
                        });
                        chatBox.innerHTML = messageHtml;
                    } else {
                        chatBox.innerHTML = "<p>Error loading messages.</p>";
                    }
                });
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
            <h2>Welcome, <span><?php echo htmlspecialchars($farmerName); ?></span></h2>
        </section>

        <section class="crop-form">
            <h3>Submit Crop Details</h3>
            <form id="cropForm" method="POST" action="../controllers/submitCrop.php">
                <div class="form-group">
                    <label for="cropName">Crop Name</label>
                    <input type="text" id="cropName" name="cropName" required>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity (in kg)</label>
                    <input type="number" id="quantity" name="quantity" required>
                </div>

                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="location">Location</label>
                    <select id="location" name="location" required>
                        <option value="">Select Location</option>
                        <option value="Dhaka">Dhaka</option>
                        <option value="Khulna">Khulna</option>
                        <option value="Sylhet">Sylhet</option>
                        <option value="Mymensingh">Mymensingh</option>
                        <option value="Rajshahi">Rajshahi</option>
                        <option value="Barishal">Barishal</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="contact">Contact</label>
                    <input type="text" id="contact" name="contact" required>
                </div>

                <div class="form-group">
                    <label for="deliveryTime">Delivery Time</label>
                    <input type="datetime-local" id="deliveryTime" name="deliveryTime" required>
                </div>

                <div class="form-group">
                    <label for="transactionMethod">Transaction Method</label>
                    <select id="transactionMethod" name="transactionMethod" required>
                        <option value="">Select Transaction Method</option>
                        <option value="Cash on Delivery">Cash on Delivery</option>
                        <option value="Bkash">Bkash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>
                </div>

                <button type="submit">Submit</button>
            </form>
        </section>

        <section>
            <div id="agent-list">
                <?php foreach ($agents as $agent): ?>
                    <div class="agent">
                        <h3><?= htmlspecialchars($agent['username']) ?></h3>
                        <p>Email: <?= htmlspecialchars($agent['email']) ?></p>

                        <div class="chat-box" id="chat-box-<?= $agent['id'] ?>">
                            <?php
                            $sql_messages = "SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)";
                            $stmt_messages = $conn->prepare($sql_messages);
                            $stmt_messages->bind_param("iiii", $_SESSION['user_id'], $agent['id'], $agent['id'], $_SESSION['user_id']);
                            $stmt_messages->execute();
                            $result_messages = $stmt_messages->get_result();

                            if ($result_messages->num_rows > 0) {
                                while ($message = $result_messages->fetch_assoc()) {
                                    $sender = ($message['sender_id'] == $_SESSION['user_id']) ? 'You' : $agent['username'];
                                    echo "<div class='message'><strong>{$sender}:</strong> {$message['message']}</div>";
                                }
                            } else {
                                echo "<p>No messages yet.</p>";
                            }
                            ?>
                        </div>

                        <button onclick="openMessageForm(<?= $agent['id'] ?>)">Send Message</button>

                        <form id="message-form-<?= $agent['id'] ?>" style="display:none;" method="POST" onsubmit="event.preventDefault(); sendMessage(<?= $agent['id'] ?>)">
                            <input type="hidden" name="agentId" value="<?= $agent['id'] ?>">
                            <textarea name="message" placeholder="Enter your message" required></textarea>
                            <button type="submit">Send</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="submitted-data">
            <h3>Submitted Crop Details</h3>
            <div id="dataTable"></div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Hatbazar. All rights reserved. | Agricultural Marketplace</p>
    </footer>

    <script src="../public/js/cropDetails.js"></script>
</body>
</html>
