<?php
require_once '../classes/reviewclass.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicleId = $_POST['vehicleId'];
    $clientId = $_POST['clientId'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $review = new Review();
    $result = $review->addReview($vehicleId, $clientId, $rating, $comment);

    if ($result) {
        $_SESSION['message'] = "Review submitted successfully!";
    } else {
        $_SESSION['message'] = "Failed to submit review. Please try again.";
    }

    header("Location: myreservation.php");
    exit();
}
?>