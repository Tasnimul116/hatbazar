<?php
// Include database connection and session start
include_once '../config/database.php';
session_start();

// Initialize error variable
$error = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Check if username or password is empty
    if (empty($username) || empty($password)) {
        $error = "Please fill in both fields.";
    } else {
        // Prepare SQL to fetch user data from the database
        $sql = "SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1";

        // Prepare statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters
            $stmt->bind_param("s", $username);

            // Execute the statement
            $stmt->execute();
            $stmt->store_result();

            // Check if a user exists with the provided username
            if ($stmt->num_rows > 0) {
                // Bind result variables
                $stmt->bind_result($id, $dbUsername, $dbPassword, $role);

                // Fetch the user data
                if ($stmt->fetch()) {
                    // Verify password
                    if (password_verify($password, $dbPassword)) {
                        // Start a session and set session variables
                        $_SESSION['user_id'] = $id;
                        $_SESSION['username'] = $dbUsername;
                        $_SESSION['role'] = $role;

                        // Redirect based on user role
                        if ($role == 'farmer') {
                            header("Location: ../views/farmerDashboard.php");
                        } elseif ($role == 'agent') {
                            header("Location: ../views/agentDashboard.php");
                        } elseif ($role == 'admin') {
                            header("Location: ../views/adminDashboard.php");
                        } elseif ($role == 'customer') {
                            header("Location: ../views/customerDashboard.php");
                        }
                        
                        exit();
                    } else {
                        // Incorrect password
                        $error = "Invalid username or password.";
                    }
                }
            } else {
                // User not found
                $error = "Invalid username or password.";
            }

            $stmt->close();
        } else {
            // Database error
            $error = "Something went wrong. Please try again later.";
        }
    }
}

$conn->close();
?>
