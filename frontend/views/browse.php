<?php
require __DIR__ . '/../includes/session_handler.php';
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../backend/config/database.php';
require __DIR__ . '/../../debug/logger.php';
require __DIR__ . '/../includes/auth_helper.php';
require __DIR__ . '/../includes/image_handler.php';

$logger = getLogger("AuthLog", __DIR__ . '/../../debug/userAuth.log');
$logger->info("Browse page loaded");

session_start();

$headerLink = __DIR__ . "/../includes/guest_header.php";


// if authenticated user session is active, show auth_header
if (isset($_SESSION["username"])) {
    $headerLink = __DIR__ . "/../includes/auth_header.php";
}

function display_content_preview($content)
{
    $limit = 150;
    $max_characters = 0;
    $result = "";
    $contentLength = strlen($content);

    if ($contentLength < $limit) {
        $max_characters = $contentLength;
    } else {
        $max_characters = $limit;
    }

    for ($i = 0; $i < $max_characters; $i++) {
        $result .= $content[$i];
    }
    return $result . "...";
}


// sortable by book title, user, last_updated
$query = "SELECT * FROM review ORDER BY last_modified DESC LIMIT 40;";
$statement = $db->prepare($query);

$statement->execute();



?>

<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Reviews - Browse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../style.css">
</head>

<body class="d-flex h-100">
    <div class="container-fluid d-flex flex-column">
        <?php require $headerLink ?>
        <main id="mainContent" class="container my-4">
            <h2 class="bg-dark text-white ps-2 mb-3">our community readers</h2>
            <div class="card-columns mx-3">
                <?php while ($row = $statement->fetch()): ?>
                    <div class="card d-flex flex-column border-dark">
                        <a href="review.php?id=<?= $row["review_id"] ?>" style="text-decoration: none;" class="text-dark">
                            <?php if (getImageUrlFromDatabase($db, $row["image_id"])): ?>
                                <img class="card-img-top"
                                    src="<?= "https://localhost/WD2/book-reviews-cms/frontend/auth_user/" . getImageUrlFromDatabase($db, $row["image_id"]) ?>"
                                    alt="card-image" />
                            <?php endif; ?>
                            <div class="card-body">
                                <div class="card-title">
                                    <p class="fs-6"><strong><?= $row["book_title"] ?></strong></p>
                                    (<?= $row["book_rating"] ?> <i class="bi bi-star-fill" style="color: #FDCC0D;"></i>
                                    by <?= getUsername($db, $row["reviewer_id"]); ?>)
                                </div>
                                <p class="card-text"><?= display_content_preview($row['review_content']) ?></p>
                            </div>
                            <div class="card-footer">
                                <small class="text-muted">Last updated on <?= $row["last_modified"] ?> </small>
                            </div>
                        </a>
                    </div>


                <?php endwhile; ?>
            </div>
        </main>
        <?php require __DIR__ . "/../includes/footer.php" ?>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>