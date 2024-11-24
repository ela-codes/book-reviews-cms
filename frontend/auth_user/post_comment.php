<?php

require __DIR__ . '/../includes/session_handler.php';
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../backend/config/database.php';

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