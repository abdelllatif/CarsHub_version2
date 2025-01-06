<?php
session_start();
require_once '../classes/reviewclass.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reviewId'])) {
    $reviewId = $_POST['reviewId'];

    $review = new Review();
    $result = $review->deleteReviews($reviewId);
    
    if ($result) {
        header('Location: review.php'); 
        exit();
    } else {
        // Handle failure
        echo "Failed to delete review.";
    }
} else {
    echo "Invalid request.";
}
?>