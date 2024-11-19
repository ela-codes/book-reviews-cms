<?php
require __DIR__ . '/../includes/session_handler.php';
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../backend/config/database.php';
require __DIR__ . '/../../debug/logger.php';
require __DIR__ . '/../includes/getUsername.php';

$logger = getLogger("AuthLog", __DIR__ . '/../../debug/userAuth.log');
$logger->info("Full review page loaded");

session_start();

$headerLink = __DIR__ . "/../includes/guest_header.php";

// if authenticated user session is active, show auth_header
if (isset($_SESSION["username"])) {
    $headerLink = __DIR__ . "/../includes/auth_header.php";
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Sanitize
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    // Validate to ensure id is an integer
    if(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)) {

        // Build and prepare SQL String with :id placeholder parameter.
        $query = "SELECT * FROM review WHERE review_id = :id";
        $statement = $db->prepare($query);

        // Bind the :id parameter with binding-type of Integer.
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        // Fetch the row selected by primary key id.
        $row = $statement->fetch();

        $reviewer_username = getUsername($db, $row["reviewer_id"]);
        $logger->debug("Showing review by $reviewer_username");

    } else {
        header("Location: dashboard.php"); // Redirect if ID is not an integer
        exit;
    }
}
?>

<!DOCTYPE html>
<html class="h-100" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Reviews - Full Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="d-flex h-100">
    <div class="container-fluid d-flex flex-column">
        <?php require $headerLink ?>
        <main id="mainContent">
            <div class="container w-75 my-4 border-start">
                <?php if($row): ?>
                    <h2 class="bg-dark text-white ps-2">Book review by <i><?= $reviewer_username ?></i>.</h2>
                    <br>
                    <h5 class="ps-2">
                        <strong><?= $row["book_title"] ?></strong> by <strong><?= $row["book_author"] ?></strong></u>
                        (<?= $row["book_rating"] ?> <i class="bi bi-star-fill" style="color: #FDCC0D;"></i>)
                    </h5>
                    <p class="container ps-4 pe-3">
                        <i class="bi bi-chat-right-quote-fill pe-2"></i>
                        <?= nl2br($row["review_content"]) ?>
                    </p>
                    <p class="text-end pt-3 pe-3" style="font-size: 12px;">
                        <i class="text-right text-secondary">Last updated on <?= $row["last_modified"] ?>.</i>
                    </p>
                <?php else: ?>
                    <h3>This post is not available.</h3>
                <?php endif; ?>


            </div>
        </main>
        <?php require __DIR__ . "/../includes/footer.php" ?>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>