<?php
require __DIR__ . '/../includes/session_handler.php';
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../backend/config/database.php';
require __DIR__ . '/../../debug/logger.php';
require __DIR__ . '/../includes/auth_helper.php';
require __DIR__ . '/../includes/image_handler.php';

$logger = getLogger("AuthLog", __DIR__ . '/../../debug/userAuth.log');
$logger->info("Full review page loaded");

session_start();

$headerLink = __DIR__ . "/../includes/guest_header.php";
$commentFeedback = "You must be logged in to leave a comment!";

// if authenticated user session is active, show proper header and default comment feedback
if(isset($_SESSION["role"])) {
    if ($_SESSION["role"] === "USER") {
        $headerLink = __DIR__ . "/../includes/auth_header.php";
        $commentFeedback = "There are no comments on this post yet!";
    } else if ($_SESSION["role"] === "ADMIN") {
        $headerLink = __DIR__ . "/../includes/admin_header.php";
    }
}


// Handle displaying book review data
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Sanitize
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    // Validate to ensure id is an integer
    if (filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)) {

        // Build and prepare SQL String with :id placeholder parameter.
        $query = "SELECT * FROM review WHERE review_id = :id";
        $statement = $db->prepare($query);

        // Bind the :id parameter with binding-type of Integer.
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        // Fetch the row selected by primary key id.
        $row = $statement->fetch();

        if ($row) {
            $reviewer_username = getUsername($db, $row["reviewer_id"]);
            $image_url = "https://localhost/WD2/book-reviews-cms/frontend/auth_user/" . getImageUrlFromDatabase($db, $row["image_id"]);

            $logger->debug("Showing review by $reviewer_username");
            $logger->debug("Getting image url: $image_url");
        }

    } else {
        header("Location: dashboard.php"); // Redirect if ID is not an integer
        exit;
    }
}
// Handle comments
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and validate comment
    $comment_content = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_SPECIAL_CHARS);
    $review_id = filter_input(INPUT_POST, 'review_id', FILTER_VALIDATE_INT);
    $commenter_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    if ($comment_content && $review_id && $commenter_id) {
        // Insert comment into database
        $insertQuery = "INSERT INTO comment (review_id, commenter_id, comment_content) VALUES (:review_id, :commenter_id, :comment_content)";
        $insertStmt = $db->prepare($insertQuery);
        $insertStmt->bindValue(':review_id', $review_id, PDO::PARAM_INT);
        $insertStmt->bindValue(':commenter_id', $commenter_id, PDO::PARAM_INT);
        $insertStmt->bindValue(':comment_content', $comment_content);
        $insertStmt->execute();

        // Redirect to the same page to avoid form resubmission
        header("Location: review.php?id=" . $review_id);
        exit;
    } else {
        $_SESSION['comment_feedback'] = "Invalid input. Please try again.";
        header("Location: review.php?id=" . $review_id);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="d-flex h-100">
    <div class="container-fluid d-flex flex-column">
        <?php require $headerLink ?>
        <main id="mainContent">
            <div class="container w-75 my-5">
                <?php if (isset($_SESSION['image_upload_feedback'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['image_upload_feedback']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['image_upload_feedback']); ?>
                <?php endif; ?>
                <?php if ($row): ?>
                    <div class="row">
                        <div class="col border-start">
                            <h2 class="bg-dark text-white ps-2">Book review by <i><?= $reviewer_username ?></i>.</h2>
                            <br>
                            <h5 class="ps-2">
                                <strong><?= $row["book_title"] ?></strong> by
                                <strong><?= $row["book_author"] ?></strong></u>
                                (<?= $row["book_rating"] ?> <i class="bi bi-star-fill" style="color: #FDCC0D;"></i>)
                            </h5>
                            <p class="ps-2" style="font-size: 12px;">
                                <i class="text-left text-secondary">Updated on <?= $row["last_modified"] ?></i>
                            </p>
                            <p class="container pe-3">
                                <i class="bi bi-chat-right-quote-fill pe-2"></i>
                                <?= nl2br($row["review_content"]) ?>
                            </p>
                            
                        </div>
                        <?php if (getImageUrlFromDatabase($db, $row["image_id"])): ?>
                            <div class="col-5 pb-4">
                                <img src="<?= $image_url ?>" class="img-fluid rounded-top" alt="image for book review page" />
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="row mb-5 border-top border-end pt-4">
                        <div class="col">
                            <h5 class="bg-dark text-white ps-2">Comments</h5>
                            <?php
                            $commentQuery = "SELECT c.*, u.username FROM comment c JOIN user u ON c.commenter_id = u.user_id WHERE c.review_id = :review_id ORDER BY c.created_at DESC";
                            $commentStmt = $db->prepare($commentQuery);
                            $commentStmt->bindValue(':review_id', $id, PDO::PARAM_INT);
                            $commentStmt->execute();
                            $comments = $commentStmt->fetchAll();

                            if ($comments):
                                foreach ($comments as $comment):
                                    ?>
                                    <div class="mb-3">
                                        <p><strong><?= htmlspecialchars($comment['username']) ?>:</strong>
                                        <?= htmlspecialchars_decode($comment['comment_content']) ?>
                                        <small class="text-end" style="font-size: 12px;">
                                            <i class="text-secondary">(<?= $comment['created_at'] ?>)</i>
                                        </small></p>
                                    </div>
                                    <?php
                                endforeach;
                            else:
                                ?>
                                <small><?= $commentFeedback ?></small>
                            <?php endif; ?>
                        </div>
                        <?php if(isset($_SESSION["username"])): ?>
                            <div class="col-5">
                                <h5 class="bg-dark text-white ps-2">Leave a Comment</h5>
                                <form action="" method="post">
                                    <div class="mb-3">
                                        <label for="comment" class="form-label">Comment</label>
                                        <textarea class="form-control" id="comment" name="comment" rows="2" required></textarea>
                                    </div>
                                    <input type="hidden" name="review_id" value="<?= $id ?>">
                                    <button type="submit" class="btn btn-sm btn-dark">Submit</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <h3>Uh oh. This post is not available.</h3>
                <?php endif; ?>


            </div>
        </main>
        <?php require __DIR__ . "/../includes/footer.php" ?>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>