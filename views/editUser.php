<?php
session_start();
 include '../config/database.php';

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../views/login.php");
    exit();
}

// Fetch user details based on the ID passed in the URL
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Fetch user details
    $stmt = $conn->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "User not found.";
        exit();
    }
} else {
    echo "User ID not provided.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Validate inputs
    if (empty($username) || empty($email) || empty($role)) {
        $error = "All fields are required.";
    } else {
       
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
        if ($stmt === false) {
            echo "Error preparing statement: " . $conn->error;
            exit();
        }

        $stmt->bind_param("sssi", $username, $email, $role, $userId);

        // Debugging: Check if bind_param is successful
        if ($stmt->errno) {
            echo "Binding parameters failed: " . $stmt->error;
            exit();
        }

        if ($stmt->execute()) {
            $success = "User updated successfully!";
        } else {
            $error = "Error updating user: " . $stmt->error;
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | Hatbazar</title>
    <link rel="stylesheet" href="../public/css/editUser.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>Hatbazar</h1>
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="adminDashboard.php">Back to Dashboard</a></li>
                <li><a href="../controllers/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="edit-user">
            <h2>Edit User</h2>

            <?php if (isset($success)): ?>
                <div class="alert success">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="farmer" <?= $user['role'] === 'farmer' ? 'selected' : '' ?>>Farmer</option>
                        <option value="agent" <?= $user['role'] === 'agent' ? 'selected' : '' ?>>Agent</option>
                        <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <button type="submit">Update User</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Hatbazar. All rights reserved. | Agricultural Marketplace</p>
    </footer>
</body>
</html>