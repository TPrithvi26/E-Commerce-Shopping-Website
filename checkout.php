<?php
// Database connection settings
$servername = "localhost";
$username = "root";  // Replace with your database username
$password = "";      // Replace with your database password
$dbname = "ecommerce_db";  // Replace with your eCommerce database name

// Start session
session_start();

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch cart items
$cart_items = array();
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cart_items = $_SESSION['cart'];
}

// Fetch product details for cart items
$product_details = array();
$seller_phone = '';
if (!empty($cart_items)) {
    $placeholders = implode(',', array_fill(0, count($cart_items), '?'));
    $sql = "SELECT p.*, u.phone FROM products p JOIN users u ON p.seller_id = u.id WHERE p.id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($cart_items)), ...array_keys($cart_items));
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $product_details[] = $row;
        $seller_phone = $row['phone']; // Assuming all products in the cart are from the same seller
    }
}

$conn->close();

// Redirect to WhatsApp if seller phone number is available
if ($seller_phone) {
    $whatsapp_link = "https://wa.me/$seller_phone";
    header("Location: $whatsapp_link");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Checkout</title>
    <style>
        :root {
            --primary-color: #111317;
            --primary-color-light: #1f2125;
            --primary-color-extra-light: #35373b;
            --secondary-color: #f9ac54;
            --secondary-color-dark: #d79447;
            --text-light: #d1d5db;
            --white: #ffffff;
            --max-width: 1200px;
        }

        body {
            font-family: "Poppins", sans-serif;
            background-color: var(--primary-color);
            color: var(--text-light);
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: var(--primary-color-light);
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: var(--secondary-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid var(--secondary-color);
        }

        th, td {
            padding: 10px;
            text-align: left;
            color: var(--white);
        }

        th {
            background-color: var(--secondary-color);
        }

        tr:nth-child(even) {
            background-color: var(--primary-color-extra-light);
        }

        .view-cart-button {
            cursor: pointer;
            border: none;
            background: var(--secondary-color);
            color: var(--white);
            padding: 10px 20px;
            font-size: 15px;
            border-radius: 4px;
            transition: background-color 0.3s;
            display: block;
            text-align: center;
            text-decoration: none;
            margin-top: 20px;
        }

        .view-cart-button:hover {
            background-color: var(--secondary-color-dark);
        }

        .back-link {
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            color: var(--secondary-color);
            border: 1px solid var(--secondary-color);
            padding: 10px 20px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .back-link:hover {
            background-color: var(--secondary-color);
            color: var(--white);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Checkout</h2>
        <p>Redirecting to WhatsApp for payment...</p>
        <a href="userdashboard.php" class="back-link">Back to Shop</a>
    </div>
</body>
</html>
