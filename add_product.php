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

// Initialize error message
$error_message = "";
$success_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $seller_id = $_SESSION['user_id'];

    // Validate input
    if (empty($name) || empty($description) || empty($price) || empty($category)) {
        $error_message = "All fields are required.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error_message = "Price must be a positive number.";
    } else {
        // Insert new product into database
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, category, seller_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsi", $name, $description, $price, $category, $seller_id);

        if ($stmt->execute()) {
            $success_message = "Product added successfully.";
            // Clear form inputs
            $name = $description = $price = $category = "";
        } else {
            $error_message = "Failed to add product. Please try again.";
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
    <title>Add Product</title>
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

        input, select {
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

        .error-message {
            color: red;
            margin-top: 10px;
        }

        .success-message {
            color: green;
            margin-top: 10px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--secondary-color);
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Product</h2>
        <?php if (!empty($error_message)) : ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message)) : ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="name">Product Name:</label>
            <input type="text" id="name" name="name" placeholder="Product Name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>

            <label for="description">Description:</label>
            <input type="text" id="description" name="description" placeholder="Description" value="<?php echo htmlspecialchars($description ?? ''); ?>" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" placeholder="Price" value="<?php echo htmlspecialchars($price ?? ''); ?>" required>

            <label for="category">Category:</label>
            <input type="text" id="category" name="category" placeholder="Category" value="<?php echo htmlspecialchars($category ?? ''); ?>" required>

            <button type="submit">Add Product</button>
        </form>
        <a href="sellerdashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>
