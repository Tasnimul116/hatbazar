<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    header("Location: ../views/login.php");
    exit();
}

// Fetch the username from the session
$farmerName = isset($_SESSION['username']) ? $_SESSION['username'] : "Unknown Farmer";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard | Hatbazar</title>
    <link rel="stylesheet" href="../public/css/farmer.css">

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
            <h2>Welcome, <span><?php echo htmlspecialchars($farmerName); ?></span></h2>
        </section>

       
        <section class="crop-form">
            <h3>Submit Crop Details</h3>
    <form id="cropForm" method="POST" action="../controllers/submitCrop.php">

    <div class="form-group">
        <label for="cropName">Crop Name</label>
        <input type="text" id="cropName" name="cropName" >
    </div>


    <div class="form-group">
        <label for="quantity">Quantity (in kg)</label>
        <input type="number" id="quantity" name="quantity" >
    </div>

    <div class="form-group">
        <label for="price">Price</label>
        <input type="number" id="price" name="price" step="0.01" >
    </div>

 
    <div class="form-group">
        <label for="location">Location</label>
        <select id="location" name="location" >
            <option value="">Select Location</option>
            <option value="Dhaka">Dhaka</option>
            <option value="Khulna">Khulna</option>
            <option value="Sylhet">Sylhet</option>
            <option value="Mymensingh">Mymensingh</option>
            <option value="Rajshahi">Rajshahi</option>
            <option value="Barishal">Barishal</option>
        </select>
    </div>

    <div class="form-group">
        <label for="contact">Contact</label>
        <input type="text" id="contact" name="contact" >
    </div>

  
    <div class="form-group">
        <label for="deliveryTime">Delivery Time</label>
        <input type="datetime-local" id="deliveryTime" name="deliveryTime" required>
    </div>

   
    <div class="form-group">
        <label for="transactionMethod">Transaction Method</label>
        <select id="transactionMethod" name="transactionMethod" >
            <option value="">Select Transaction Method</option>
            <option value="Cash on Delivery">Cash on Delivery</option>
            <option value="Bkash">Bkash</option>
            <option value="Bank Transfer">Bank Transfer</option>
        </select>
    </div>

    <button type="submit">Submit</button>
</form>
        </section>

        <section class="submitted-data">
            <h3>Submitted Crop Details</h3>
            <div id="dataTable">
              
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Hatbazar. All rights reserved. | Agricultural Marketplace</p>
    </footer>

    <script src="../public/js/cropDetails.js"></script>
</body>
</html>