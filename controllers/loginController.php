<?php
 include '../config/database.php';
session_start();

header("Content-Type: application/json"); // Set JSON response type

$response = ["success" => false, "message" => "Invalid request."];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $response["message"] = "Both fields are required.";
    } else {
        $sql = "SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
        
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $dbUsername, $dbPassword, $role);
                $stmt->fetch();
        
                if (password_verify($password, $dbPassword)) {
                    $_SESSION['user_id'] = $id;
                    $_SESSION['username'] = $dbUsername;
                    $_SESSION['role'] = $role;
        
                    // Redirect URL based on user role
                    $redirectURL = null;
                    switch ($role) {
                        case 'farmer':
                            $redirectURL = "../views/farmerDashboard.php";
                            break;
                        case 'agent':
                            $redirectURL = "../views/agentDashboard.php";
                            break;
                        case 'admin':
                            $redirectURL = "../views/adminDashboard.php";
                            break;
                        case 'customer':
                            $redirectURL = "../views/customerDashboard.php";
                            break;
                        default:
                            $redirectURL = null;
                    }
        
                    if ($redirectURL) {
                        $response["success"] = true;
                        $response["redirect"] = $redirectURL;
                    } else {
                        $response["message"] = "Invalid role.";
                    }
                } else {
                    $response["message"] = "Incorrect password.";
                }
            } else {
                $response["message"] = "User not found.";
            }
        
            $stmt->close();
        } else {
            $response["message"] = "Something went wrong. Please try again later.";
        }

        
    }
}

$conn->close();
echo json_encode($response);
?>
