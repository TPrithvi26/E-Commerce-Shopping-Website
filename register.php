<?php
// Database connection settings
$servername = "localhost";
$username = "root";  // Replace with your database username
$password = "";      // Replace with your database password
$dbname = "ecommerce_db";  // Replace with your eCommerce database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error message
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $user_type = $_POST['user_type']; // New field for user type

    // Check if email already exists
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        $error_message = "Email already exists. Please use another email.";
    } else {
        // Insert user into database
        $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, phone, email, password, user_type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first_name, $last_name, $phone, $email, $password, $user_type);

        if ($stmt->execute()) {
            header("Location: login.php"); // Redirect to login page after successful registration
            exit();
        } else {
            $error_message = "Registration failed. Please try again.";
        }

        $stmt->close();
    }

    $check_email->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Register</title>
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

        input, select {
            width: calc(100% - 22px);
            border: 2px solid var(--secondary-color);
            background: var(--primary-color-extra-light);
            color: var(--white);
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 4px;
        }

        .phone-container {
            display: flex;
            align-items: center;
        }

        .phone-container select, .phone-container input {
            width: 48%;
        }

        .phone-container select {
            margin-right: 4%;
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
        <h2>Registration Form</h2>
        <?php if (!empty($error_message)) : ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" placeholder="First Name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" placeholder="Last Name" required>

            
           

            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" placeholder="Phone Number" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Password" required>

            <label for="user_type">Register as:</label>
            <select id="user_type" name="user_type" required>
                <option value="user">User</option>
                <option value="seller">Seller</option>
            </select>

            <button type="submit">Register</button>
        </form>
        <a href="login.php" class="login-link">Already have an account? Login here</a>
    </div>
</body>
</html>
