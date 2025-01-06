<?php 
session_start();
require_once '../classes/reservationclass.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: ../connexion/singin.php");
    exit();
}

$reservation = new Reservation();
$userId = $_SESSION['user_id'];
$reservations = $reservation->getmyReservations($userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id']) && isset($_POST['status'])) {
    $reservationId = $_POST['reservation_id'];
    $status = $_POST['status'];
    $result = $reservation->updateStatus($reservationId, $status);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Reservations</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Manage Reservations Section -->
    <section id="manage-reservations" class="section-content">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold">Reservation Management</h2>
            </div>
            <div class="p-6">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="text-left p-3">User Name</th>
                            <th class="text-left p-3">Vehicle</th>
                            <th class="text-left p-3">Start Date</th>
                            <th class="text-left p-3">End Date</th>
                            <th class="text-left p-3">Pickup Location</th>
                            <th class="text-left p-3">Total Price</th>
                            <th class="text-left p-3">Status</th>
                            <th class="text-left p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (!$reservations) {
                            echo "<tr><td colspan='9' class='p-3 text-center'>No reservations found.</td></tr>";
                        } else {
                            foreach ($reservations as $reservation) {
                                echo "<tr>";
                                echo "<td class='p-3'>" . htmlspecialchars($reservation['user_name']) . " " . htmlspecialchars($reservation['user_lastname']) . "</td>";
                                echo "<td class='p-3'>" . htmlspecialchars($reservation['vehicle']) . " " . htmlspecialchars($reservation['v_brand']) . "</td>";
                                echo "<td class='p-3'>" . htmlspecialchars($reservation['startDate']) . "</td>";
                                echo "<td class='p-3'>" . htmlspecialchars($reservation['endDate']) . "</td>";
                                echo "<td class='p-3'>" . htmlspecialchars($reservation['pickupLocation']) . "</td>";
                                echo "<td class='p-3'>" . htmlspecialchars($reservation['dropoffLocation']) . "</td>";
                                echo "<td class='p-3'>" . htmlspecialchars($reservation['totalPrice']) . " DH</td>";
                                echo "<td class='p-3'>";
                                echo "<span class='px-2 py-1 " . ($reservation['status'] == 'confirmed' ? 'bg-green-100 text-green-800' : ($reservation['status'] == 'refused' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) . " rounded-full text-sm'>" . htmlspecialchars($reservation['status']) . "</span>";
                                echo "</td>";
                                echo "<td class='p-3'>";
                                if ($reservation['status'] == 'canceled' || $reservation['status'] == 'refused' || $reservation['status'] == 'en Attend') {
                                    echo "<form method='POST' class='inline'>";
                                    echo "<input type='hidden' name='action' value='delete'>";
                                    echo "<input type='hidden' name='reservation_id' value='" . htmlspecialchars($reservation['id']) . "'>";
                                    echo "<button type='submit' class='text-red-600 hover:text-red-900' onclick='return confirm(\"Are you sure you want to delete this reservation?\")'>";
                                    echo "<i class='fas fa-trash'></i>";
                                    echo "</button>";
                                    echo "</form>";
                                } else {
                                    echo "<form method='POST' class='inline ml-2'>";
                                    echo "<input type='hidden' name='reservation_id' value='" . htmlspecialchars($reservation['id']) . "'>";
                                    echo "<input type='hidden' name='status' value='canceled'>";
                                    echo "<button type='submit' class='text-red-600 hover:text-red-900'>Cancel</button>";
                                    echo "</form>";
                                    echo "<button onclick=\"showReviewForm(" . htmlspecialchars($reservation['id']) . ")\" class=\"add-review-btn text-sm text-gray-800 py-2 cursor-pointer\" data-reservation-id=\"" . htmlspecialchars($reservation['id']) . "\">Add votre avis</button>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Add Votre Avis +--  -->
    <?php foreach ($reservations as $reservation): ?>
    <div class="review-form hidden" id="reviewForm-<?php echo $reservation['id']; ?>">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="modal-overlay-<?php echo $reservation['id']; ?>">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Submit Your Review</h3>
                    <form action="submit_review.php" method="POST" class="mt-2 text-left">
                        <input type="hidden" name="vehicleId" value="<?php echo $reservation['vehicleId']; ?>">
                        <input type="hidden" name="clientId" value="<?php echo $_SESSION['user_id']; ?>">
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="rating">
                                Your Rating
                            </label>
                            <div class="stars flex justify-center space-x-1">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star text-gray-400 text-3xl cursor-pointer transition duration-150 ease-in-out hover:text-yellow-400" data-value="<?php echo $i; ?>">&#9733;</span>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" name="rating" id="ratingInput-<?php echo $reservation['id']; ?>" value="0">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="comment">
                                Your Review
                            </label>
                            <textarea 
                                name="comment" 
                                id="comment-<?php echo $reservation['id']; ?>"
                                rows="4"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="Share your experience..."
                                required
                            ></textarea>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <button 
                                type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
                            >
                                Submit Review
                            </button>
                            <button 
                                type="button"
                                class="close-modal bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
                                data-reservation-id="<?php echo $reservation['id']; ?>"
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const reviewForms = document.querySelectorAll('.review-form');
        
        reviewForms.forEach(form => {
            const stars = form.querySelectorAll('.star');
            const ratingInput = form.querySelector('input[name="rating"]');
            
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    ratingInput.value = value;
                    updateStars(stars, value);
                });
            });
        });

        function updateStars(stars, rating) {
            stars.forEach(star => {
                if (parseInt(star.getAttribute('data-value')) <= rating) {
                    star.classList.remove('text-gray-400');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-400');
                }
            });
        }

        // "Add Review"
        document.querySelectorAll('.add-review-btn').forEach(button => {
            button.addEventListener('click', function() {
                const reservationId = this.getAttribute('data-reservation-id');
                const reviewForm = document.getElementById(`reviewForm-${reservationId}`);
                if (reviewForm) {
                    reviewForm.classList.toggle('hidden');
                } else {
                    console.error(`Review form not found for reservation ID: ${reservationId}`);
                }
            });
        });

        // Add Cancel
        document.querySelectorAll('.close-modal').forEach(button => {
            button.addEventListener('click', function() {
                const reservationId = this.getAttribute('data-reservation-id');
                const reviewForm = document.getElementById(`reviewForm-${reservationId}`);
                if (reviewForm) {
                    reviewForm.classList.add('hidden');
                } else {
                    console.error(`Review form not found for reservation ID: ${reservationId}`);
                }
            });
        });
    });
    </script>
</body>
</html>