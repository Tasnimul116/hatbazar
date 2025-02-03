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
        <section class="register">
            <h2>Create an Account</h2>
            
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <form id="registrationForm" action="../controllers/registrationController.php" method="POST" onsubmit="return validateForm()">
    <div id="errorMessages" style="color: red;"></div> 

    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username"><br>

    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email"><br>

    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password"><br>

    <label for="confirmPassword">Confirm Password:</label><br>
    <input type="password" id="confirmPassword" name="confirmPassword"><br>

    <label for="role">Role:</label><br>
    <select id="role" name="role">
        <option value="farmer">Farmer</option>
        <option value="agent">Agent</option>
        <option value="customer">Customer</option>
    </select><br>

    <button type="submit">Register</button>
</form>

        </section>
    </main>


    <!-- <footer>
        <p>&copy; 2025 Hatbazar. All rights reserved. | Agricultural Marketplace</p>
    </footer> -->
</body>
</html>
