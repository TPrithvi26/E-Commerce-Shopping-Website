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

// Fetch cart items
$cart_items = array();
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cart_items = $_SESSION['cart'];
}

// Fetch product details for cart items
$product_details = array();
if (!empty($cart_items)) {
    $placeholders = implode(',', array_fill(0, count($cart_items), '?'));
    $sql = "SELECT * FROM products WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($cart_items)), ...array_keys($cart_items));
    $stmt->execute();
    $product_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>View Cart</title>
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

        .button, .back-link {
            cursor: pointer;
            border: none;
            background: var(--secondary-color);
            color: var(--white);
            padding: 10px 20px;
            font-size: 15px;
            border-radius: 4px;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            margin-top: 20px;
        }

        .button:hover, .back-link:hover {
            background-color: var(--secondary-color-dark);
        }

        .back-link {
            background: none;
            border: 1px solid var(--secondary-color);
            color: var(--secondary-color);
        }

        .back-link:hover {
            background-color: var(--secondary-color);
            color: var(--white);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Cart</h2>
        <?php if (empty($product_details)) : ?>
            <p>Your cart is empty.</p>
        <?php else : ?>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($product_details as $product) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></td>
                            <td><?php echo htmlspecialchars($_SESSION['cart'][$product['id']]); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($_SESSION['cart'][$product['id']] * $product['price'], 2)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="checkout.php" class="button">Checkout</a>
        <?php endif; ?>
        <a href="userdashboard.php" class="back-link">Back to Shop</a>
    </div>
</body>
</html>
