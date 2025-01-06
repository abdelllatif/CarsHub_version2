<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <!-- Button to open the "All Avis" popup -->
    <button id="openAllAvisPopup" class="bg-blue-500 text-white px-4 py-2 rounded">All Avis</button>

    <!-- Button to open the "Add Votre Avis" popup -->
    <button id="openAddAvisPopup" class="bg-green-500 text-white px-4 py-2 rounded">Add Votre Avis</button>

    <!-- All Avis Popup (Initially Hidden) -->
    <div id="allAvisPopup" class="popup fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="popup-content bg-white p-6 rounded-lg max-w-lg w-full shadow-lg">
            <h2 class="text-xl font-bold mb-4">All Avis</h2>
            <div id="reviewsList" class="mb-4">
                <!-- Reviews will be dynamically inserted here -->
            </div>
            <button id="closeAllAvisPopup" class="bg-red-500 text-white px-4 py-2 rounded mt-4">Close</button>
        </div>
    </div>

    <!-- Add Votre Avis Popup (Initially Hidden) -->
    <div id="addAvisPopup" class="popup fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="popup-content bg-white p-6 rounded-lg max-w-lg w-full shadow-lg">
            <h2 class="text-xl font-bold mb-4">Add Votre Avis</h2>
            <div class="stars flex mb-4">
                <!-- 5 stars for rating -->
                <span class="star text-gray-400 text-3xl cursor-pointer" data-value="1">&#9733;</span>
                <span class="star text-gray-400 text-3xl cursor-pointer" data-value="2">&#9733;</span>
                <span class="star text-gray-400 text-3xl cursor-pointer" data-value="3">&#9733;</span>
                <span class="star text-gray-400 text-3xl cursor-pointer" data-value="4">&#9733;</span>
                <span class="star text-gray-400 text-3xl cursor-pointer" data-value="5">&#9733;</span>
            </div>
            <textarea id="reviewText" placeholder="Your review..." class="w-full p-2 border rounded mb-4"></textarea>
            <button id="submitReview" class="bg-blue-500 text-white px-4 py-2 rounded">Submit</button>
            <button id="closeAddAvisPopup" class="bg-red-500 text-white px-4 py-2 rounded mt-4">Close</button>
        </div>
    </div>

    <!-- Include the JavaScript files -->
    <script >document.addEventListener('DOMContentLoaded', function() {
    const openAllAvisPopupBtn = document.getElementById('openAllAvisPopup');
    const closeAllAvisPopupBtn = document.getElementById('closeAllAvisPopup');
    const allAvisPopup = document.getElementById('allAvisPopup');
    const reviewsList = document.getElementById('reviewsList');

    // Example data for previous reviews
    const reviews = [
        { rating: 5, text: "Excellent! I loved it!" },
        { rating: 4, text: "Very good, but could be improved." },
        { rating: 3, text: "It was fine, not bad." }
    ];

    // Function to display reviews with their ratings
    function displayReviews() {
        reviewsList.innerHTML = '';
        reviews.forEach(review => {
            const reviewElement = document.createElement('div');
            reviewElement.classList.add('review', 'mb-2');
            reviewElement.innerHTML = `
                <div class="stars text-yellow-400 mb-2">
                    ${getStarsHTML(review.rating)}
                </div>
                <p>${review.text}</p>
            `;
            reviewsList.appendChild(reviewElement);
        });
    }

    // Function to generate star rating HTML based on the rating
    function getStarsHTML(rating) {
        let starsHTML = '';
        for (let i = 1; i <= 5; i++) {
            starsHTML += i <= rating ? '★' : '☆';
        }
        return starsHTML;
    }

    // Function to open the "All Avis" popup
    openAllAvisPopupBtn.addEventListener('click', function() {
        allAvisPopup.classList.remove('hidden'); // Make the popup visible
        displayReviews(); // Display all reviews
    });

    // Function to close the "All Avis" popup
    closeAllAvisPopupBtn.addEventListener('click', function() {
        allAvisPopup.classList.add('hidden'); // Hide the popup
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const openAddAvisPopupBtn = document.getElementById('openAddAvisPopup');
    const closeAddAvisPopupBtn = document.getElementById('closeAddAvisPopup');
    const addAvisPopup = document.getElementById('addAvisPopup');
    const submitReviewBtn = document.getElementById('submitReview');
    const reviewText = document.getElementById('reviewText');
    let currentRating = 0;

    // Function to open the "Add Votre Avis" popup
    openAddAvisPopupBtn.addEventListener('click', function() {
        addAvisPopup.classList.remove('hidden'); // Make the popup visible
    });

    // Function to close the "Add Votre Avis" popup
    closeAddAvisPopupBtn.addEventListener('click', function() {
        addAvisPopup.classList.add('hidden'); // Hide the popup
    });

    // Rating system (stars)
    const starElements = document.querySelectorAll('.star');
    starElements.forEach(star => {
        star.addEventListener('click', function() {
            currentRating = parseInt(this.getAttribute('data-value'));
            updateStars(currentRating);
        });
    });

    // Function to update the stars based on rating
    function updateStars(rating) {
        starElements.forEach(star => {
            if (parseInt(star.getAttribute('data-value')) <= rating) {
                star.classList.add('text-yellow-400');
                star.classList.remove('text-gray-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-400');
            }
        });
    }

    // Function to handle review submission
    submitReviewBtn.addEventListener('click', function() {
        const review = reviewText.value;

        if (currentRating === 0) {
            alert('Please provide a rating!');
        } else if (review === '') {
            alert('Please enter your review text!');
        } else {
            // Submit the review to the server (or process it here)
            alert(`Review submitted! Rating: ${currentRating} stars\nReview: ${review}`);
            addAvisPopup.classList.add('hidden');  // Close the popup after submission
        }
    });
});

</script>
  

</body>
</html>






























<?php foreach ($userReviews as $userReview): ?>
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="modal-overlay-<?php echo $userReview['id']; ?>">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Update Your Review</h3>
                    <form action="submit_review.php" method="POST" class="mt-2 text-left">
                        <input type="hidden" name="reviewId" value="<?php echo $userReview['id']; ?>">
                        <input type="hidden" name="clientId" value="<?php echo $_SESSION['user_id']; ?>">

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="rating">
                                Your Rating
                            </label>
                            <div class="stars flex justify-center space-x-1">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star text-gray-400 text-3xl cursor-pointer transition duration-150 ease-in-out hover:text-yellow-400" data-value="<?php echo $i; ?>" 
                                          onclick="setRating(<?php echo $i; ?>, <?php echo $userReview['id']; ?>)">
                                        &#9733;
                                    </span>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" name="rating" id="ratingInput-<?php echo $userReview['id']; ?>" value="<?php echo $userReview['rating']; ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="comment">
                                Your Review
                            </label>
                            <textarea 
                                name="comment" 
                                id="comment-<?php echo $userReview['id']; ?>"
                                rows="4"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="Share your experience..."
                                required
                            ><?php echo htmlspecialchars($userReview['comment']); ?></textarea>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <button 
                                type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
                            >
                                Update Review
                            </button>
                            <button 
                                type="button"
                                class="close-modal bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
                                data-review-id="<?php echo $userReview['id']; ?>"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
