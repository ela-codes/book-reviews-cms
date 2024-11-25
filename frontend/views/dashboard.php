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
        $query = "SELECT * FROM review WHERE reviewer_id = :reviewer_id ORDER BY book_title ASC";
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
$headerLink = __DIR__ . "/../includes/auth_header.php";


if ($_SESSION["role"] === "ADMIN") {
    $headerLink = __DIR__ . "/../includes/admin_header.php";
}


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
        <?php require $headerLink ?>

        <main class="container my-4 h-100">
            <h2 class="bg-dark text-white ps-2 my-5">welcome to your dashboard!</h2>
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); // Clear the message after displaying ?>
            <?php endif; ?>
            <div class="row">
                <div class="col">
                    <h5>Number of posts: <?= count($reviews) ?></h5>
                </div>
                <div class="col"><a class="btn btn-primary btn-md btn-dark text-white float-end"
                        href="../auth_user/post_review.php">Write a Review</a>
                </div>
            </div>
            <div class="table-responsive my-5">
                <table class="table table-sm table-hover align-middle">
                    <thead class="text-center text-nowrap">
                        <tr>
                            <th scope="col" class="col-2 text-start">
                                <button type="button" class="btn sort-btn" id="titleBtn" onClick="updateSort('book_title', 'titleBtn')"><strong>Title</strong>
                                    <i class="bi bi-sort-alpha-down"></i>
                                </button>
                            </th>
                            <th scope="col" class="col-1 text-start">
                                <button type="button" class="btn sort-btn ASC" id="authorBtn" onClick="updateSort('book_author', 'authorBtn')"><strong>Author</strong>
                                    <i class="bi bi-sort-alpha-down"></i>
                                </button>
                            </th>
                            <th scope="col" class="col-1 text-nowrap">
                                <button type="button" class="btn sort-btn ASC" id="ratingBtn" onClick="updateSort('book_rating', 'ratingBtn')"><strong>Rating</strong>
                                    <i class="bi bi-sort-alpha-down"></i>
                                </button>
                            </th>
                            <th scope="col" class="col-6" style="padding-bottom: 0.7rem">Review</th>
                            <th scope="col" class="col-1" style="padding-bottom: 0.7rem">Last Updated</th>
                            <th scope="col" colspan="2"></th>
                        </tr>
                    </thead>
                    <tbody class="table-hover">
                        <!-- dynamically populated -->
                    </tbody>
                </table>
            </div>
        </main>
        <?php require __DIR__ . "/../includes/footer.php" ?>


    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>

    <!-- SORTING HANDLER -->
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            updateSort('book_title', 'titleBtn', true); // Default sort column
            
        });

        function updateSort(column, btnId = 'titleBtn', firstLoad = false) {
            console.log("running updateSort function")
            console.log(`Column: ${column}, btnId: ${btnId}, firstLoad: ${firstLoad}`)
            let currentSortColumn = column; // Default sort column
            let currentSortDirection = ''
            let btn = document.querySelector(`#${btnId}`);


            if (firstLoad) {
                currentSortDirection = 'ASC'; // default
                btn.classList.add('ASC');
                console.log(btn.classList[2])
            } else {
                if (btn.classList[2] === 'ASC') {
                    currentSortDirection = 'DESC';
                    btn.innerHTML = `<strong>${btn.firstChild.textContent}</strong> <i class="bi bi-sort-alpha-up"></i>`;
                    btn.classList.replace('ASC', 'DESC');
                } else {
                    currentSortDirection = 'ASC';
                    btn.innerHTML = `<strong>${btn.firstChild.textContent}</strong> <i class="bi bi-sort-alpha-down"></i>`;
                    btn.classList.replace('DESC', 'ASC');
                }
            }
            
            fetchSortedData(currentSortColumn, currentSortDirection);// Fetch sorted data
        }

        function fetchSortedData(sortColumn, sortDirection) {
            console.log(`running fetchSortedData function - ${sortColumn}, ${sortDirection}`)
            fetch("https://localhost/WD2/book-reviews-cms/frontend/includes/fetch_reviews.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    sortColumn: sortColumn,
                    sortDirection: sortDirection,
                }),
            })
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector("tbody");
                    tbody.innerHTML = ""; // Clear existing rows
                    console.log(data)
                    if (data.success) {
                        data.reviews.forEach(review => {
                            const row = `
                        <tr>
                            <td>${review.book_title}</td>
                            <td>${review.book_author}</td>
                            <td class="text-center">${review.book_rating}</td>
                            <td>${review.review_content_preview}</td>
                            <td class="text-center">${review.last_modified_preview}</td>
                            <td><a href="review.php?id=${review.review_id}" class="btn btn-sm"><i class="bi bi-box-arrow-up-right"></i></a></td>
                            <td><a href="../auth_user/edit_review.php?review_id=${review.review_id}" class="btn btn-sm"><i class="bi bi-pencil-square"></i></a></td>
                        </tr>
                    `;
                            tbody.insertAdjacentHTML("beforeend", row);
                        });
                    } else {
                        tbody.innerHTML = "<tr><td colspan='7'>No reviews found.</td></tr>";
                    }
                })
                .catch(error => {
                    console.error("Error fetching sorted reviews:", error);
                });
        }
    </script>
</body>

</html>