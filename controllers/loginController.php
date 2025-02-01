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

    // Validate form data
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
                        $_SESSION['user_id'] = $id;
                        $_SESSION['username'] = $dbUsername;
                        $_SESSION['role'] = $role;

                        if ($role == 'farmer') {
                            header("Location: ../views/farmerDashboard.php");
                          
                        }
                        if ($role == 'agent') {
                            header("Location: ../views/agentDashboard.php");
                        } 
                        if ($role == 'admin') {
                            header("Location: ../views/adminDashboard.php");
                        } 
                        
                        exit();
                    } else {
                        $error = "Invalid username or password.";
                    }
                }
            } else {
                $error = "Invalid username or password.";
            }

            $stmt->close();
        } else {
            $error = "Something went wrong. Please try again later.";
        }
    }
}

$conn->close();
?>
