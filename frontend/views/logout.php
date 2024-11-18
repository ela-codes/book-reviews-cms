<?php
require __DIR__ . '/../../debug/logger.php';

// Create a logger instance
$logger = getLogger("AuthLog", __DIR__ . '/../../debug/userAuth.log');
$logger->info('Logout page loaded');

$indexLink = "https://localhost/WD2/book-reviews-cms/frontend/index.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_SESSION["logged_in"])) {
    $logger->debug("before destroying:" . $_SESSION["username"]);
    $logger->debug(session_status());
    session_unset();
    $gc = session_gc();

    session_destroy();
    $logger->debug("Session garbage collection: $gc");
    $logger->debug(session_status());
    header("Location: $indexLink");
    exit;
}

?>
