<?php
session_start();
 include '../config/database.php';

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../views/login.php");
    exit();
}

// Fetch all users
$sql_users = "SELECT id, username, email, role, created_at FROM users";
$result_users = $conn->query($sql_users);

$users = [];
if ($result_users->num_rows > 0) {
    while ($row = $result_users->fetch_assoc()) {
        $users[] = $row;
    }
}

// Fetch all products
$sql_products = "SELECT p.id, p.crop_name, p.quantity, p.location, p.contact, p.delivery_time, p.transaction_method, u.username AS farmer_name 
                 FROM products p 
                 JOIN users u ON p.farmer_id = u.id";
$result_products = $conn->query($sql_products);

$products = [];
if ($result_products->num_rows > 0) {
    while ($row = $result_products->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Hatbazar</title>
    <link rel="stylesheet" href="../public/css/admin.css">
    <script>


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
            <h2>Welcome, Admin</h2>
        </section>

        <section class="user-management">
            <h3>User Management</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td><?= htmlspecialchars($user['created_at']) ?></td>
                            <td>
                                <a href="editUser.php?id=<?= $user['id'] ?>">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="product-management">
            <h3>Product Management</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Crop Name</th>
                        <th>Quantity</th>
                        <th>Location</th>
                        <th>Contact</th>
                        <th>Delivery Time</th>
                        <th>Transaction Method</th>
                        <th>Farmer</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['id']) ?></td>
                            <td><?= htmlspecialchars($product['crop_name']) ?></td>
                            <td><?= htmlspecialchars($product['quantity']) ?></td>
                            <td><?= htmlspecialchars($product['location']) ?></td>
                            <td><?= htmlspecialchars($product['contact']) ?></td>
                            <td><?= htmlspecialchars($product['delivery_time']) ?></td>
                            <td><?= htmlspecialchars($product['transaction_method']) ?></td>
                            <td><?= htmlspecialchars($product['farmer_name']) ?></td>
                            <td>
                            <a href="editProduct.php?id=<?= $product['id'] ?>">Edit</a>
                            </td>
                            
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Hatbazar. All rights reserved. | Agricultural Marketplace</p>
    </footer>
</body>
</html>