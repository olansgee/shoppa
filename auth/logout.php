<?php
// auth/logout.php
session_start();

// Store username for potential future use
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Clear cart data
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}

// Redirect to homepage with success message
header("Location: /shoppa-main/index.php?logout=success");
exit();
?>