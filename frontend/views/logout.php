<?php
require __DIR__ . '/../../debug/logger.php';

// Create a logger instance
$logger = getLogger("AuthLog", __DIR__ . '/../../debug/userAuth.log');
$logger->info('Logout page loaded');

/**
* when logout button is clicked,
* ask the user to confirm ("are you sure you want to log out?")
* if yes, then unset and destroy their session 
* then bring them to a "successfully logged out page"
* redirect to home page
*/
session_start();

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_SESSION["logged_in"])) {
    $logger->debug("before destroying:" . $_SESSION["username"]);
    $logger->debug(session_status());
    session_unset();
    $gc = session_gc();

    session_destroy();
    $logger->debug("Session garbage collection: $gc");
    $logger->debug(session_status());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    YOU'VE BEEN LOGGED OUT MF
</body>
</html>