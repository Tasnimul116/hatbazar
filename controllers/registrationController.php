<?php
include_once '../config/database.php'; 

$error = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $role = $_POST['role'];

    // Validate form data
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = "Please fill all the fields.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";

        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);  
            if ($stmt->execute()) {
                header("Location: ../views/login.php"); 
                exit();
            } else {
                
                echo "<script>
        alert('Error submitting crop details: " . $stmt->error . "');
    </script>";
            }

            $stmt->close();
        } else {
            $error = "Failed to prepare the SQL query.";
        }
    }
}

$conn->close();
?>