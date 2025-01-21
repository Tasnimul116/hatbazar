<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration - Hatbazar</title>
    <link rel="stylesheet" href="../public/css/registration.css"> 
    <script src="../public/js/register.js" defer></script>
</head>
<body>
    <header>
        <div class="logo">
            <h1>Hatbazar</h1>
            <nav class="navbar">
            <ul>
            <li><a href="../index.php">Home</a></li>
                <li><a href="./about.php">About Us</a></li>
                <li><a href="./contact.php">Contact</a></li>
                <li><a href="./login.php">Log in</a></li>
            </ul>
        </nav>
        </div>
    </header>

    <main>
        <section class="register">
            <h2>Create an Account</h2>
            
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <form id="registrationForm" action="../controllers/authcontroller.php" method="POST" onsubmit="return validateForm()">
                <label for="username">Username:</label><br>
                <input type="text" id="username" name="username" required><br>

                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" required><br>

                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required><br>

                <label for="confirmPassword">Confirm Password:</label><br>
                <input type="password" id="confirmPassword" name="confirmPassword" required><br>

                <label for="role">Role:</label><br>
                <select id="role" name="role" required>
                    <option value="farmer">Farmer</option>
                    <option value="agent">Agent</option>
                    <option value="customer">Customer</option>
                </select><br>

                <button type="submit">Register</button>
            </form>
        </section>
    </main>


    <footer>
        <p>&copy; 2025 Hatbazar. All rights reserved. | Agricultural Marketplace</p>
    </footer>
</body>
</html>
