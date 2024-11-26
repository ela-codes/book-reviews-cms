<?php
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../backend/config/database.php';
require __DIR__ . '/../../debug/logger.php';
require __DIR__ . '/../includes/image_handler.php';
require __DIR__ . '/../includes/review_content_helper.php';
require __DIR__ . '/../includes/auth_helper.php';

$logger = getLogger("AuthLog", __DIR__ . '/../../debug/userAuth.log');
$logger->info("Search page loaded");

session_start();

$headerLink = __DIR__ . "/../includes/guest_header.php";


// if authenticated user session is active, show auth_header
if (isset($_SESSION["role"])) {
    if ($_SESSION["role"] === "USER") {
        $headerLink = __DIR__ . "/../includes/auth_header.php";
    } else if ($_SESSION["role"] === "ADMIN") {
        $headerLink = __DIR__ . "/../includes/admin_header.php";
    }
}

// Pagination settings
$itemsPerPage = 5;
$currentPage = isset($_GET['page']) ? filter_var($_GET['page'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) : 1;
if (!$currentPage)
    $currentPage = 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Search input validation and sanitization
$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = trim(filter_input(INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    // Ensure search query is at least 1 characters
    if (strlen($searchQuery) < 1) {
        $searchQuery = '';
    }
}
$search = '%' . $searchQuery . '%';

// Build search query using prepared statements
$totalStmt = $db->prepare("SELECT COUNT(*) FROM review WHERE 
    review_content LIKE :search OR 
    book_title LIKE :search OR 
    book_author LIKE :search");
$totalStmt->bindValue(':search', $search, PDO::PARAM_STR);
$totalStmt->execute();
$totalItems = $totalStmt->fetchColumn();
$totalPages = ceil($totalItems / $itemsPerPage);

// Get paginated results
$stmt = $db->prepare("SELECT * FROM review WHERE 
    review_content LIKE :search OR 
    book_title LIKE :search OR 
    book_author LIKE :search  
    ORDER BY book_title ASC 
    LIMIT :limit OFFSET :offset");
$stmt->bindValue(':search', $search, PDO::PARAM_STR);
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$searchResults = $stmt->fetchAll();



?>
<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="d-flex h-100">
    <div class="container-fluid d-flex flex-column">
        <?php require $headerLink ?>
        <main class="container my-4 h-100">
            <h2 class="bg-dark text-white ps-2 mb-3">search for something</h2>
            <div class="container">
                <div class="row justify-content-center">
                    <!-- Search Form -->
                    <form action="search.php" method="GET" class="col-8 my-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Enter keywords to search..."
                                value="<?php echo htmlspecialchars_decode($searchQuery); ?>">
                            <button type="submit" class="btn btn-dark">Search</button>
                        </div>
                    </form>
                </div>
                <div class="row justify-content-center">
                    <!-- Search Results -->
                    <?php if (!empty($searchQuery)): ?>
                        <h5 class="text-center">Book reviews containing "<?= htmlspecialchars_decode($_GET['search']) ?>"</h5>
                        <div class="col-8 g-3">
                            <?php foreach ($searchResults as $review): ?>
                                <div class="card m-3" style="font-size:small;">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="review.php?id=<?= $review['review_id'] ?>"
                                                class="text-dark text-decoration-none">
                                                <?= htmlspecialchars_decode($review['book_title']) ?>
                                            </a>
                                        </h5>
                                        <p class="card-text">
                                            <strong>Rating:</strong> <?= htmlspecialchars($review['book_rating']) ?> ⭐
                                            <br>
                                            <strong>Author:</strong> <?= htmlspecialchars_decode($review['book_author']) ?>
                                        </p>
                                        <p class="card-text text-truncate">
                                            <?= htmlspecialchars_decode($review['review_content']) ?>
                                        </p>
                                    </div>
                                    <div class="card-footer text-muted">
                                        Last updated: <?= htmlspecialchars($review['last_modified']) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <!-- for previous page -->
                                    <li class="page-item <?= ($currentPage == 1) ? 'disabled' : ''; ?>">
                                        <a class="page-link"
                                            href="?page=<?= $currentPage - 1; ?>&search=<?= urlencode($searchQuery); ?>">Previous</a>
                                    </li>
                                    <!-- for page numbers -->
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= ($currentPage == $i) ? 'active' : ''; ?>">
                                            <a class="page-link"
                                                href="?page=<?= $i; ?>&search=<?= urlencode($searchQuery); ?>"><?= $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <!-- for next page -->
                                    <li class="page-item <?= ($currentPage == $totalPages) ? 'disabled' : ''; ?>">
                                        <a class="page-link"
                                            href="?page=<?= $currentPage + 1; ?>&search=<?= urlencode($searchQuery); ?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-center text-muted">Use a keyword to search for reviews.</p>
                    <?php endif; ?>
                </div>

        </main>


        <?php require __DIR__ . '/../includes/footer.php'; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>