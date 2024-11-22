<?php
require __DIR__ . '/../includes/session_handler.php';
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../backend/config/database.php';
require __DIR__ . '/../../debug/logger.php';

$logger = getLogger("AuthLog", __DIR__ . '/../../debug/userAuth.log');
checkSession();
header('Content-Type: application/json');

// Allow script to read raw input and then decode the expected JSON payload
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Sanitize column and direction inputs
$sortColumn = isset($data['sortColumn']) ? $data['sortColumn'] : 'book_title';
$sortDirection = isset($data["sortDirection"]) ? $data["sortDirection"] : 'ASC';

$logger->debug("Sort column: $sortColumn, Sort direction: $sortDirection");

// Ensure that the sort column is one of the allowed columns
$allowedColumns = ['book_title', 'book_author', 'book_rating'];
if (!in_array($sortColumn, $allowedColumns)) {
    $sortColumn = 'book_title';
}

$reviewer_id = $_SESSION["user_id"];

$logger->info("Fetching reviews for dashboard - {$_SESSION["username"]}, Reviewer ID: $reviewer_id, Sort Column: $sortColumn, Sort Direction: $sortDirection");


try {
    $logger->debug("running query...");

    $query = "SELECT * FROM review WHERE reviewer_id = :reviewer_id ORDER BY $sortColumn $sortDirection";
    $statement = $db->prepare($query);
    $statement->bindValue(':reviewer_id', $reviewer_id, PDO::PARAM_INT);
    $statement->execute();

    $reviews = $statement->fetchAll(PDO::FETCH_ASSOC);
    $count = count($reviews);

    $logger->debug("Fetched $count reviews");

    // Add preview and format data for JSON response
    $responseReviews = array_map(function ($review) {
        return [
            'review_id' => $review['review_id'],
            'book_title' => $review['book_title'],
            'book_author' => $review['book_author'],
            'book_rating' => $review['book_rating'],
            'review_content_preview' => substr($review['review_content'], 0, 80) . '...',
            'last_modified_preview' => substr($review['last_modified'], 0, 10),
        ];
    }, $reviews);

    // $logger->debug("formatted json: " . json_encode($responseReviews));

    echo json_encode([
        'success' => true,
        'reviews' => $responseReviews,
    ]);
} catch (PDOException $e) {
    error_log("Error fetching sorted reviews: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching reviews.',
    ]);
}
?>
