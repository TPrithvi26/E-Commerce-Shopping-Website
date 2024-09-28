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

// Check if user is logged in and is a seller
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header("Location: login.php"); // Redirect to login if not logged in or not a seller
    exit();
}

// Fetch seller's products
$seller_id = $_SESSION['user_id'];
$sql = "SELECT * FROM products WHERE seller_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$products = $stmt->get_result();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Seller Dashboard</title>
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
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: var(--primary-color-light);
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: var(--secondary-color);
        }

        .card {
            background-color: var(--primary-color-extra-light);
            border: 1px solid var(--secondary-color);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }

        .card img {
            max-width: 100%;
            border-radius: 8px;
        }

        .card-content {
            padding: 10px;
        }

        .card-title {
            font-size: 1.2em;
            margin: 0;
            color: var(--secondary-color);
        }

        .card-description {
            margin: 10px 0;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-price {
            font-size: 1.1em;
            color: var(--secondary-color);
        }

        .button {
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
        }

        .button:hover {
            background-color: var(--secondary-color-dark);
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        a {
            color: var(--secondary-color);
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        a:hover {
            text-decoration: underline;
        }

        .logout-button {
            text-align: center;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .card {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Seller Dashboard</h2>
        <a href="add_product.php" class="button">Add New Product</a>
        <div class="products">
            <?php while ($product = $products->fetch_assoc()) : ?>
                <div class="card">
                    <div class="card-content">
                        <h3 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="card-description"><?php echo htmlspecialchars($product['description']); ?></p>
                        <p class="card-price">$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                        <div class="actions">
                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="button">Edit</a>
                            <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="button">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="logout-button">
            <a href="logout.php" class="button">Logout</a>
        </div>
    </div>
</body>
</html>
