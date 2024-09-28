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

// Initialize error and success messages
$error_message = "";
$success_message = "";

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: sellerdashboard.php"); // Redirect if no valid ID is provided
    exit();
}

$product_id = (int) $_GET['id']; // Ensure ID is treated as an integer

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND seller_id = ?");
$stmt->bind_param("ii", $product_id, $_SESSION['user_id']);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: sellerdashboard.php"); // Redirect if product not found
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    // Validate input
    if (empty($name) || empty($description) || empty($price) || empty($category)) {
        $error_message = "All fields are required.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error_message = "Price must be a positive number.";
    } else {
        // Update product in database
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, category = ? WHERE id = ? AND seller_id = ?");
        $stmt->bind_param("ssdiii", $name, $description, $price, $category, $product_id, $_SESSION['user_id']);

        if ($stmt->execute()) {
            $success_message = "Product updated successfully.";
        } else {
            $error_message = "Failed to update product. Please try again.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Product</title>
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

        form {
            width: 100%;
        }

        label {
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
            color: var(--white);
        }

        input {
            width: calc(100% - 22px);
            border: 2px solid var(--secondary-color);
            background: var(--primary-color-extra-light);
            color: var(--white);
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 4px;
        }

        button {
            cursor: pointer;
            width: 100%;
            border: none;
            background: var(--secondary-color);
            color: var(--white);
            margin: 10px 0 0;
            padding: 10px;
            font-size: 15px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: var(--secondary-color-dark);
        }

        .error-message, .success-message {
            margin-top: 10px;
        }

        .error-message {
            color: red;
        }

        .success-message {
            color: green;
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
        <h2>Edit Product</h2>
        <?php if (!empty($error_message)) : ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message)) : ?>
            <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . urlencode($product_id); ?>" method="post">
            <label for="name">Product Name:</label>
            <input type="text" id="name" name="name" placeholder="Product Name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

            <label for="description">Description:</label>
            <input type="text" id="description" name="description" placeholder="Description" value="<?php echo htmlspecialchars($product['description']); ?>" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" placeholder="Price" value="<?php echo htmlspecialchars($product['price']); ?>" required>

            <label for="category">Category:</label>
            <input type="text" id="category" name="category" placeholder="Category" value="<?php echo htmlspecialchars($product['category']); ?>" required>

            <button type="submit">Update Product</button>
        </form>
        <a href="sellerdashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>
