<?php
require __DIR__ . '/../includes/session_handler.php';
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../backend/config/database.php';
require __DIR__ . '/../../debug/logger.php';

// Create a logger instance
$logger = getLogger("AuthLog", __DIR__ . '/../../debug/userAuth.log');
checkSession();

$logger->info("Dashboard page loaded for username - {$_SESSION["username"]}, id - {$_SESSION["user_id"]}");

function display_content_preview($content, $limit = 50, $ellipsis = "...") 
{
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
    return $result . $ellipsis;
}
function getAllReviews($db, $reviewer_id)
{
    try {
        $query = "SELECT * FROM review WHERE reviewer_id = :reviewer_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':reviewer_id', $reviewer_id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    } catch (PDOException $e) {
        $logger = getLogger("AuthLog", __DIR__ . '/../../debug/userAuth.log');
        $logger->error("Error fetching reviews: " . $e->getMessage());
        return [];
    }
}

$reviewer_id = $_SESSION["user_id"];
$reviews = getAllReviews($db, $reviewer_id);


?>

<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Reviews - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="d-flex h-100">
    <div class="container-fluid d-flex flex-column">
        <?php require __DIR__ . "/../includes/auth_header.php" ?>

        <main class="container my-4">
            <h2 class="bg-dark text-white ps-2 my-5">welcome to your dashboard!</h2>
            <div class="row">
                <div class="col">
                    <h5>Number of posts: <?= count($reviews) ?></h5>
                </div>
                <div class="col"><a class="btn btn-primary btn-md btn-dark text-white float-end"
                        href="../auth_user/post_review.php">Write a Review</a>
                </div>
            </div>
            <div class="table-responsive my-4">
                <table class="table table-sm table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="col-3">Title</th>
                            <th scope="col" class="col-1">Author</th>
                            <th scope="col" class="col-1 text-center">Rating</th>
                            <th scope="col">Review</th>
                            <th scope="col" class="text-center">Last Updated</th>
                            <th scope="col" class="col-1 text-center" colspan="3"></th>
                        </tr>
                    </thead>
                    <tbody class="table-hover">
                        <?php if (count($reviews) === 0): ?>
                            <tr>
                                <td colspan="7">No reviews found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reviews as $review): ?>
                                <tr>
                                    <td><?= $review["book_title"] ?></td>
                                    <td><?= $review["book_author"] ?></td>
                                    <td class="text-center"><?= $review["book_rating"] ?></td>
                                    <td><?= display_content_preview($review["review_content"], 70) ?></td>
                                    <td class="text-center"><?= display_content_preview($review["last_modified"], 10, "") ?></td>
                                    <td>
                                    <a href="review.php?id=<?= $review["review_id"] ?>" class="btn btn-sm"><i class="bi bi-box-arrow-up-right"></i></a>
                                    </td>
                                    <td>
                                        <a href="edit_review.php?id=<?= $review["review_id"] ?>" class="btn btn-sm">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#deleteReview-<?= $review["review_id"] ?>"><i class="bi bi-trash3"></i>
                                        </button>
                                        <!-- Modal Body, hidden by default-->
                                        <!-- <div class="modal fade" id="deleteReview-<?= $review["review_id"] ?>" tabindex="-1"
                                            role="dialog" aria-labelledby="#deleteUser-<?= $user["user_id"] ?>"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-md"
                                                role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteUserModalTitle">Delete User</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want delete user
                                                            <strong><mark><?= $user["username"] ?></mark></strong> from the
                                                            system?
                                                        </p>
                                                        <p class="text-danger">All of the user's posts, comments, and images
                                                            will be removed.</p>
                                                        <p class="text-danger">This action cannot be reversed.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <form action="admin_dashboard.php" method="post">
                                                            <input type="hidden" name="id" value="<?= $user["user_id"] ?>">
                                                            <input type="hidden" name="delete" value="1">
                                                            <input type="submit" name="delete" value="Delete"
                                                                class="btn btn-primary" />
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
        <?php require __DIR__ . "/../includes/footer.php" ?>


    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>