<?php
require __DIR__ . '/../includes/session_handler.php';
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../backend/config/database.php';
require __DIR__ . '/../../debug/logger.php';

// Create a logger instance
$logger = getLogger("AuthLog", __DIR__ . '/../../debug/userAuth.log');
checkSession();

$logger->info("Dashboard page loaded for username - {$_SESSION["username"]}, id - {$_SESSION["user_id"]}");





?>

<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Reviews - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="d-flex h-100">
    <div class="container-fluid d-flex flex-column">
        <?php require __DIR__ . "/../includes/auth_header.php" ?>
        
        <main>
                <div class="container ">
                    <h1>welcome to your dashboard!</h1>
                    <a class="btn btn-dark" href="../auth_user/post_review.php">Write a Review</a>
                </div>

        </main>
        <?php require __DIR__ . "/../includes/footer.php" ?>

    
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>