<?php
require __DIR__ . '/../includes/session_handler.php';
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../backend/config/database.php';
require __DIR__ . '/../../debug/logger.php';
require __DIR__ . '/../includes/getUsername.php';

$logger = getLogger("AuthLog", __DIR__ . '/../../debug/userAuth.log');
$logger->info("Browse page loaded");

$headerLink = __DIR__ . "/../includes/guest_header.php";

function display_content_preview($content)
{
    $limit = 200;
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
$query = "SELECT * FROM review ORDER BY book_title ASC LIMIT 20;";
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
        <main id="mainContent" class="container w-75">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3 my-4">
                <?php while ($row = $statement->fetch()): ?>
                    <div class="col">
                        <a href="review.php?id=<?= $row["review_id"] ?>" style="text-decoration: none;">
                            <div class="card h-100 d-flex flex-column border-dark">
                                <img class="card-img-top"
                                    src="https://freerangestock.com/sample/156228/a-child-reading-a-book.jpg"
                                    alt="card-image" />
                                <div class="card-body">
                                    <div class="card-title">
                                        <strong><?= $row["book_title"] ?></strong>
                                        (<?= $row["book_rating"] ?> <i class="bi bi-star-fill" style="color: #FDCC0D;"></i> by <?php getUsername($db, $row["reviewer_id"]); ?>)
                                    </div>
                                    <p class="card-text"><?= display_content_preview($row['review_content']) ?></p>
                                </div>
                                <div class="card-footer">
                                    <small class="text-muted">Last updated on <?= $row["last_modified"] ?> </small>
                                </div>
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