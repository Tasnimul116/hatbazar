<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hatbazar</title>
    <link rel="stylesheet" href="../public/css/login.css"> 
</head>
<body>
<header>
    <div class="logo">
        <h1>Hatbazar</h1>
    </div>
    <nav class="navbar">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="./contact.php">Contact</a></li>
            <li><a href="./login.php">Login</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="login">
        <h2>Login</h2>
        <p id="error-message" class="error" style="color: red;"></p>
        
        <form id="login-form">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username"><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password"><br>

            <button type="submit">Login</button>
        </form>
        <p class="register-link">Don't have an account? <a href="./registration.php">Create new account</a></p>
    </section>
</main>

<script>
document.getElementById("login-form").addEventListener("submit", async function(event) {
    event.preventDefault(); 

    let username = document.getElementById("username").value.trim();
    let password = document.getElementById("password").value.trim();
    let errorMessage = document.getElementById("error-message");

    if (username === "" || password === "") {
        errorMessage.textContent = "Both fields are required!";
        return;
    }

    // Send an AJAX request to the PHP script for authentication
    try {
        let response = await fetch("../controllers/loginController.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`,
        });

        let result = await response.json(); 

        if (result.success) {
            window.location.href = result.redirect; // Redirect user based on role
        } else {
            errorMessage.textContent = result.message; // Show error message
        }
    } catch (error) {
        errorMessage.textContent = "Something went wrong. Please try again later.";
    }
});
</script>

<footer>
    <p>&copy; 2025 Hatbazar. All rights reserved. | Agricultural Marketplace</p>
</footer>
</body>
</html>
