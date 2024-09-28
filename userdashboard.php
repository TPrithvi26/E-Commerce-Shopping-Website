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

// Fetch all products
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Handle add to cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = 1; // Add product with quantity 1
    } else {
        $_SESSION['cart'][$product_id]++; // Increase quantity if already in cart
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>User Dashboard</title>
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
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .view-cart-button {
            background: var(--secondary-color);
            color: var(--white);
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            display: block;
            margin-bottom: 20px;
            text-decoration: none;
        }

        .view-cart-button:hover {
            background-color: var(--secondary-color-dark);
        }

        .product-box {
            background: var(--primary-color-light);
            border: 1px solid var(--secondary-color);
            border-radius: 8px;
            padding: 20px;
            margin: 10px;
            display: inline-block;
            width: calc(33.333% - 20px);
            vertical-align: top;
            box-sizing: border-box;
        }

        .product-box img {
            max-width: 100%;
            border-radius: 8px;
        }

        .product-box h3 {
            margin: 10px 0;
            color: var(--secondary-color);
        }

        .product-box p {
            margin: 10px 0;
        }

        .product-box button {
            background: var(--secondary-color);
            color: var(--white);
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .product-box button:hover {
            background-color: var(--secondary-color-dark);
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }

        .exit {
            background: var(--secondary-color);
            color: var(--white);
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            display: block;
            margin-bottom: 20px;
            text-decoration: none;
        }

         {
            background-color: var(--secondary-color-dark);
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="view_cart.php" class="view-cart-button">View Cart</a>
        <a href="dashboard.html" class="exit">exit</a>
        <div class="products">
            <?php while ($product = $result->fetch_assoc()) : ?>
                <div class="product-box">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <p>Price: $<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit" name="add_to_cart">Add to Cart</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
