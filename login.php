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

// Initialize error message
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT id, password, user_type FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    // Check if email exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $user_type);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['user_id'] = $id;
            $_SESSION['user_type'] = $user_type;

            // Redirect based on user type
            if ($user_type === 'seller') {
                header("Location: sellerdashboard.php");
            } else {
                header("Location: userdashboard.php"); // Adjust the location as needed
            }
            exit();
        } else {
            $error_message = "Incorrect password. Please try again.";
        }
    } else {
        $error_message = "No account found with that email.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <style>:root {
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
    max-width: 600px;
    margin: 50px auto;
    padding: 20px;
    background-color: var(--primary-color-light);
    border-radius: 8px;
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

.error-message {
    color: red;
    margin-top: 10px;
}

.login-link {
    display: block;
    text-align: center;
    margin-top: 10px;
    color: var(--secondary-color);
    text-decoration: none;
}

.login-link:hover {
    text-decoration: underline;
}
</style>
</head>
<body>
    <div class="container">
        <h2>Login Form</h2>
        <?php if (!empty($error_message)) : ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Password" required>

            <button type="submit">Login</button>
        </form>
        <a href="register.php" class="login-link">Don't have an account? Register here</a>
    </div>
</body>
</html>
