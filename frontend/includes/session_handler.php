<?php 

define("SESSION_TIMEOUT", 60 * 5);

function checkSession() {
    session_start();

    // Check if user is not logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header("Location: login.php");
        exit();
    }

    // Check if session is expired
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        // Session has expired
        session_unset(); // Unset all session variables
        session_destroy(); // Destroy session
        header("Location: login.php?timeout=1"); // Redirect with timeout message
        exit();
    }

    // Update last activity timestamp if session is not expired
    
    $_SESSION['last_activity'] = time();
}

?>