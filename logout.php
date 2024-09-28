<?php
// Start session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Finally, destroy the session
session_destroy();

// Redirect to the login page
header("Location: dashboard.html");
exit();
?>
