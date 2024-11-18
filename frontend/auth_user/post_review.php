<?php
require __DIR__ . '/../includes/session_handler.php';
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../backend/config/database.php';
require __DIR__ . '/../../debug/logger.php';

// Create a logger instance
$logger = getLogger("AuthLog", __DIR__ . '/../../debug/userAuth.log');
$logger->info('USER-Post page loaded');

checkSession();


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["review_content"]) && isset($_POST["book_title"]) && isset($_POST["book_author"]) && isset($_POST["book_rating"])) {
        // sanitize required data
        $review_content = filter_input(INPUT_POST, 'review_content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $book_title = filter_input(INPUT_POST, 'book_title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $book_author = filter_input(INPUT_POST, 'book_author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $book_rating = filter_input(INPUT_POST, 'book_rating', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $logger->debug($review_content);

        // validate - ensure title and content is at least 1 character long each
        $valid_content = strlen(trim($review_content)) > 0;
        $valid_title = strlen(trim($book_title)) > 0;
        $valid_author = strlen(trim($book_author)) > 0;

        if ($valid_content && $valid_title && $valid_author) {
            $reviewer_id = $_SESSION["user_id"];

            // build query
            $query = "INSERT INTO review(review_content, book_title, book_author, book_rating, reviewer_id) VALUES (:review_content, :book_title, :book_author, :book_rating, :reviewer_id)";

            $statement = $db->prepare($query);
            $statement->bindValue(":review_content", ucwords($review_content)); // proper casing
            $statement->bindValue(":book_title", ucwords($book_title)); // proper casing
            $statement->bindValue(":book_author", $book_author);
            $statement->bindValue(":book_rating", $book_rating);
            $statement->bindValue(":reviewer_id", $reviewer_id);

            if ($statement->execute()) {
                $logger->debug("Submitted new post query.");
                // Retrieve the ID for new review post
                $last_id = $db->lastInsertId();

                // Redirect to the new blog post page
                header("Location: ../../views/review.php?id={$last_id}");
                exit;
            }

        }
    }
}

?>


<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Reviews - Write A Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="d-flex h-100">
    <div class="container-fluid d-flex flex-column">
        <header class="mb-auto">
            <nav class="navbar navbar-expand-sm">
                <div class="container h-100">
                    <a href="../index.php" class="navbar-brand">BookReviews</a>
                    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#homeNav"
                        aria-controls="homeNav" aria-label="Expand Navigation Bar">
                        <div class="navbar-toggler-icon"></div>
                    </button>
                    <div class="collapse navbar-collapse" id="homeNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a href="../views/browse.php" class="nav-link">Browse</a>
                            </li>
                            <li class="nav-item">
                                <a href="../views/logout.php" class="nav-link">Log Out</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main>
            <div class="container w-50">
                <h3>Write a Review</h3>
                <form action="post_review.php" method="post" id="postForm">
                    <ul class="list-unstyled">
                        <li>
                            <label for="book_title" class="form-label">Title</label>
                            <input type="text" name="book_title" id="book_title" class="form-control" required />
                        </li>
                        <li>
                            <label for="book_author" class="form-label">Author</label>
                            <input type="text" name="book_author" id="book_author" class="form-control" required />
                        </li>

                        <li>
                            <label for="book_rating" class="form-label">My Rating:</label><span class="ms-3 me-1"
                                id="ratingValue"></span><i class="bi bi-star-fill" style="color: #FDCC0D;"></i>
                            <input type="range" name="book_rating" class="form-range " min="1" max="5" step="0.5"
                                id="ratingRange" required />
                        </li>
                        <li>
                            <label for="review_content" class="form-label">How was the book?</label>
                            <textarea class="form-control" name="review_content" id="review_content" rows="8"
                                required></textarea>
                        </li>
                        <li>
                            <label for="review_image" class="form-label">Upload a cover picture</label>
                            <input type="file" class="form-control" name="review_image" id="review_image"
                                aria-describedby="fileFormatHelpId" />
                            <div id="fileFormatHelpId" class="form-text">Formats accepted: .jpeg, .png</div>
                        </li>
                        <button type="submit" class="btn btn-primary mt-3">Post</button>
                    </ul>
                </form>
            </div>
        </main>
        <?php require __DIR__ . "/../includes/footer.php" ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script>
        let range = document.getElementById("ratingRange");
        let output = document.getElementById("ratingValue");
        output.innerHTML = range.value;

        range.oninput = function () {
            output.innerHTML = this.value;
        }

    </script>
</body>

</html>