<?php 
session_start();
require_once '../classes/reviewclass.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion/singin.php');
    exit;
}

$review = new Review();
$userReviews = $review->getUserReviews($_SESSION['user_id']);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reviews - AutoLoc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">My Reviews</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if ($userReviews): ?>
                <?php foreach ($userReviews as $userReview): ?>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-2">
                            <?php echo htmlspecialchars($userReview['brand'] . ' ' . $userReview['model']); ?>
                        </h2>
                        <div class="flex items-center mb-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="text-yellow-400">
                                    <?php echo $i <= $userReview['rating'] ? '★' : '☆'; ?>
                                </span>
                            <?php endfor; ?>
                        </div>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($userReview['comment']); ?></p>
                        <div class="flex justify-between items-center">
                            <button onclick="editReview(<?php echo htmlspecialchars(json_encode($userReview)); ?>)" 
                                    class="text-blue-600 hover:text-blue-800">
                                Edit
                            </button>
                            <form action="delete_review.php" method="POST">
                                <input type="hidden" name="reviewId" value="<?php echo $userReview['id']; ?>">
                                <button name="deleteReview" type="submit" 
                                        class="text-red-600 hover:text-red-800">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No reviews found.</p>
            <?php endif; ?>
        </div>
    </div>
<!-- Update Review Modal (hidden by default) -->
<div id="updateReviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Update Your Review</h3>
            <form id="updateReviewForm" action="update_review.php" method="POST" class="mt-2 text-left">
                <!-- Hidden fields for review id and client id -->
                <input type="hidden" name="reviewId" id="reviewIdInput">
                <input type="hidden" name="clientId" value="<?php echo $_SESSION['user_id']; ?>">

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="rating">
                        Your Rating
                    </label>
                    <div class="stars flex justify-center space-x-1">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star text-gray-400 text-3xl cursor-pointer transition duration-150 ease-in-out hover:text-yellow-400" 
                                  data-value="<?php echo $i; ?>" 
                                  onclick="setRating(<?php echo $i; ?>)">
                                &#9733;
                            </span>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="rating" id="ratingInput" value="0">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="comment">
                        Your Review
                    </label>
                    <textarea 
                        name="comment" 
                        id="commentInput"
                        rows="4"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        placeholder="Share your experience..."
                        required
                    ></textarea>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                        Update Review
                    </button>
                    <button type="button" class="close-modal bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Function to open the review edit modal and populate it with the review data
    function editReview(userReview) {
        // Fill the modal fields with the current review data
        document.getElementById('reviewIdInput').value = userReview.id;
        document.getElementById('ratingInput').value = userReview.rating;
        document.getElementById('commentInput').value = userReview.comment;

        // Open the modal
        document.getElementById('updateReviewModal').classList.remove('hidden');
    }

    // Function to close the modal
    document.querySelector('.close-modal').addEventListener('click', function() {
        document.getElementById('updateReviewModal').classList.add('hidden');
    });

    // Function to handle the rating input
    function setRating(rating) {
        document.getElementById('ratingInput').value = rating;

        // Highlight the stars
        const stars = document.querySelectorAll('.star');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
            }
        });
    }
</script>

</body>
</html>