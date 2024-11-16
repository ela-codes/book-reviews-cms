<?php 
require __DIR__ . '/../includes/session_timeout.php';


session_start();

if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) {
    header("Location: login.php");
    exit();
}

// if session is expired
if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"]) > SESSION_TIMEOUT) {
    session_unset(); // revert all session variables
    session_destroy(); // destroy session
    header("Location: login.php?timeout=1");
    exit();
}
// if not expired, update session timestamp
$_SESSION["last_activity"] = time();

?>