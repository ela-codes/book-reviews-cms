<?php
require __DIR__ . '/../includes/session_handler.php';
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../backend/config/database.php';
require __DIR__ . '/../../debug/logger.php';


$logger = getLogger("AuthLog", __DIR__ . '/../../debug/userAuth.log');
$logger->info('USER-Edit review page loaded');

checkSession();

$headerLink = __DIR__ . "/../includes/auth_header.php";


if ($_SESSION["role"] === "ADMIN") {
    $headerLink = __DIR__ . "/../includes/admin_header.php";
}

// Handle GET request in order to display review data
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Sanitize
    $review_id = filter_input(INPUT_GET, 'review_id', FILTER_SANITIZE_NUMBER_INT);

    // Validate to ensure id is an integer
    if (filter_input(INPUT_GET, 'review_id', FILTER_VALIDATE_INT)) {

        // Build and prepare SQL String with :id placeholder parameter.
        $query = "SELECT * FROM review WHERE review_id = :review_id";
        $statement = $db->prepare($query);

        // Bind the :id parameter with binding-type of Integer.
        $statement->bindValue(':review_id', $review_id, PDO::PARAM_INT);
        $statement->execute();

        // Fetch the row selected by primary key id.
        $row = $statement->fetch();


    } else {
        header("Location: dashboard.php"); // Redirect if ID is not an integer
        exit;
    }
}
// Handle edit review form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["review_content"]) && isset($_POST["book_title"]) && isset($_POST["book_author"]) && isset($_POST["book_rating"]) && isset($_POST["review_id"])) {

        // sanitize required data
        $review_content = filter_input(INPUT_POST, 'review_content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $book_title = filter_input(INPUT_POST, 'book_title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $book_author = filter_input(INPUT_POST, 'book_author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $book_rating = filter_input(INPUT_POST, 'book_rating', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $review_id = filter_input(INPUT_POST, 'review_id', FILTER_SANITIZE_NUMBER_INT);

        // validate - ensure title and content is at least 1 character long each
        $valid_content = strlen(trim($review_content)) > 0;
        $valid_title = strlen(trim($book_title)) > 0;
        $valid_author = strlen(trim($book_author)) > 0;

        if ($valid_content && $valid_title && $valid_author) {

            // build query
            $query = "UPDATE review SET review_content = :review_content, book_title = :book_title, book_author = :book_author, book_rating = :book_rating WHERE review_id = :review_id";

            $statement = $db->prepare($query);
            $statement->bindValue(":review_content", $review_content); // proper casing
            $statement->bindValue(":book_title", ucwords($book_title)); // proper casing
            $statement->bindValue(":book_author", ucwords($book_author));
            $statement->bindValue(":book_rating", $book_rating);
            $statement->bindValue(":review_id", $review_id, PDO::PARAM_INT);

            if ($statement->execute()) {
                $logger->info("Review updated successfully for review ID: $review_id");

                // Redirect to the updated blog post page
                header("Location: https://localhost/WD2/book-reviews-cms/frontend/views/review.php?id=$review_id");
                exit;
            }
        }
    }
}

// Handle delete review form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["delete"]) && isset($_POST["review_id"])) {
    $review_id = filter_input(INPUT_POST, 'review_id', FILTER_SANITIZE_NUMBER_INT);

    try {
        $query = "DELETE FROM review WHERE review_id = :review_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':review_id', $review_id, PDO::PARAM_INT);
        $statement->execute();
        $_SESSION['success_message'] = "Review deleted successfully.";
        $logger->info("Successfully deleted review with ID: $review_id");
        header("Location: https://localhost/WD2/book-reviews-cms/frontend/views/dashboard.php");
        exit();
    } catch (Exception $e) {
        $logger->error("Error deleting review: " . $e->getMessage());
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
        <?php require $headerLink; ?>
        <main>
            <div class="container w-50">
                <h3>Edit a Review</h3>
                <form action="edit_review.php" method="post" id="postForm">
                    <ul class="list-unstyled">
                        <li>
                            <label for="book_title" class="form-label">Title</label>
                            <input type="text" name="book_title" id="book_title" class="form-control"
                                value="<?= htmlspecialchars_decode($row['book_title'], ENT_QUOTES) ?>" required />
                        </li>
                        <li>
                            <label for="book_author" class="form-label">Author</label>
                            <input type="text" name="book_author" id="book_author" class="form-control"
                                value="<?= htmlspecialchars_decode($row['book_author'], ENT_QUOTES) ?>" required />
                        </li>

                        <li>
                            <label for="book_rating" class="form-label">My Rating:</label><span class="ms-3 me-1"
                                id="ratingValue"></span><i class="bi bi-star-fill" style="color: #FDCC0D;"></i>
                            <input type="range" name="book_rating" class="form-range " min="1" max="5" step="0.5"
                                value="<?= htmlspecialchars($row['book_rating']) ?>" id="ratingRange" required />
                        </li>
                        <li>
                            <label for="review_content" class="form-label">How was the book?</label>
                            <textarea class="form-control" name="review_content" id="review_content" rows="8"
                                required><?= htmlspecialchars_decode($row['review_content'], ENT_QUOTES) ?></textarea>
                        </li>
                        <li>
                            <input type="text" name="review_id" id="review_id" class="form-control"
                                value="<?= htmlspecialchars($row['review_id']) ?>" hidden />
                        </li>

                        <div class="row">
                            <div class="col"><button type="submit" class="btn btn-primary mt-3">Apply Changes</button>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-light mt-3 float-end" data-bs-toggle="modal"
                                    data-bs-target="#deleteReview-<?= $row["review_id"] ?>">Delete Review
                                </button>
                            </div>
                        </div>
                    </ul>
                </form>

                <!-- Modal Body, hidden by default-->
                <div class="modal fade" id="deleteReview-<?= $row["review_id"] ?>" tabindex="-1" role="dialog"
                    aria-labelledby="deleteReview-<?= $row["review_id"] ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteReviewModalTitle">Delete Review</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete review your review for
                                    <strong><mark><?= $row["book_title"] ?></mark></strong> by
                                    <strong><mark><?= $row["book_author"] ?></mark></strong>?
                                </p>
                                <p class="text-danger">This action cannot be reversed.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form action="edit_review.php" method="post">
                                    <input type="hidden" name="review_id" value="<?= $row["review_id"] ?>">
                                    <input type="hidden" name="delete" value="1">
                                    <input type="submit" name="delete" value="Delete" class="btn btn-primary" />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
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