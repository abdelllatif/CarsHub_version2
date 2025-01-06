<?php
session_start();
require_once '../classes/reviewclass.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reviewId'])) {
    $reviewId = $_POST['reviewId'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Ensure the user is logged in
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $review = new Review();

        // Update the review
        $result = $review->updateReview($reviewId, $userId, $rating, $comment);

        if ($result) {
            header('Location: myreview.php');  // Redirect to the reviews page
            exit();
        } else {
            echo "Failed to update review.";
        }
    } else {
        echo "You must be logged in to update a review.";
    }
} else {
    echo "Invalid request.";
}
?>
