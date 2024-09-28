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

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: sellerdashboard.php"); // Redirect if no valid ID is provided
    exit();
}

$product_id = $_GET['id'];

// Check if product belongs to the seller
$stmt = $conn->prepare("SELECT id FROM products WHERE id = ? AND seller_id = ?");
$stmt->bind_param("ii", $product_id, $_SESSION['user_id']);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: sellerdashboard.php"); // Redirect if product not found
    exit();
}

// Delete the product
$stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND seller_id = ?");
$stmt->bind_param("ii", $product_id, $_SESSION['user_id']);

if ($stmt->execute()) {
    header("Location: sellerdashboard.php"); // Redirect back to dashboard after deletion
    exit();
} else {
    echo "Failed to delete product. Please try again.";
}

$stmt->close();
$conn->close();
?>
