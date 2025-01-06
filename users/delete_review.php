<?php
session_start();
require_once '../classes/reviewclass.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reviewId'])) {
    $reviewId = $_POST['reviewId'];

    // Ensure the user is logged in
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        
        $review = new Review();
        $result = $review->deleteReview($reviewId, $userId);
        
        // After processing, redirect the user back to their reviews page or show a success message
        if ($result) {
            // Redirect after successful deletion
            header('Location:myreview.php'); // Change this to your reviews page
            exit();
        } else {
            // Handle failure
            echo "Failed to delete review.";
        }
    } else {
        echo "You must be logged in to delete a review.";
    }
} else {
    echo "Invalid request.";
}
?>
