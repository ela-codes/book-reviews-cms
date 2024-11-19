<?php
require '../frontend/includes/session_handler.php';
require '../vendor/autoload.php';
require '../backend/config/database.php';
require '../debug/logger.php';


$logger = getLogger("AuthLog", '../debug/userAuth.log');
$logger->info("Dashboard page loaded.");

session_start();
$loggedIn = false;
$headerLink = __DIR__ . "/includes/guest_header.php";


// if authenticated user session is active, show auth_header
if (isset($_SESSION["username"])) {
    $headerLink = __DIR__ . "/includes/auth_header.php";
    $loggedIn = true;
}

?>

<!DOCTYPE html>
<html class="h-100" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Reviews - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="d-flex h-100 text-center">
    <div class="container-fluid d-flex flex-column">
        <?php require $headerLink ?>
        <main class="row">
            <div class="container w-50  d-flex align-items-center flex-column">
                <h1 class="bg-dark text-white ps-1" style="width:max-content;"> Welcome to BookReviews.</h1>
                <h6>Discover, review, and share your love for books.</h6>
                <?php if ($loggedIn): ?>
                    <a class="btn btn-dark btn-lg mt-4" href="./views/dashboard.php">Return to Dashboard</a>
                <?php else: ?>
                    <a class="btn btn-dark btn-lg mt-4" href="./views/register.php">Register for FREE</a>
                <?php endif; ?>

            </div>
        </main>
        <?php require __DIR__ . "/includes/footer.php" ?>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>