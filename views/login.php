<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hatbazar</title>
    <link rel="stylesheet" href="../public/css/login.css"> <!-- External CSS -->
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
            </ul>
        </nav>
        </div>
    </header>

    <main>
        <section class="login">
            <h2>Login</h2>
            <?php if (!empty($error)): ?>
                <p class="error"> <?php echo $error; ?> </p>
            <?php endif; ?>
            <form action="../controllers/loginController.php" method="POST">
                <label for="username">Username:</label><br>
                <input type="text" id="username" name="username" required><br>

                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required><br>

                <button type="submit">Login</button>
            </form>
            <p class="register-link">Don't have an account? <a href="./registration.php">Create new account</a></p>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Hatbazar. All rights reserved. | Agricultural Marketplace</p>
    </footer>
</body>
</html>
