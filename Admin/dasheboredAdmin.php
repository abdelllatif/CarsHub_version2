
<?php 
session_start();
require_once 'getstatistique.php';
require_once '../classes/reservationclass.php';
require_once '../classes/categorieclass.php';
require_once '../classes/reviewclass.php';
require_once '../classes/classvihcule.php';
require_once '../classes/reviewclass.php';
require_once '../classes/clientclasse.php';
$review = new Review();
$clients = $review->getClientsInfo();

$review = new Review();
$allReviews = $review->getAllReviews(); // Get all reviews for the admin
$vehicle = new Vehicle();
$vehicles = $vehicle->getAllVehicles();

// First check authentication
if (!isset($_SESSION['user_email']) || $_SESSION['user_id'] != 1) {
    header("Location: ../connexion/singin.php");
    exit();
}

// Then handle the status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id']) && isset($_POST['status'])) {
    $reservationId = $_POST['reservation_id'];
    $status = $_POST['status'];
    $reservation = new Reservation(); // Create instance of Reservation class
    $result = $reservation->updateStatus($reservationId, $status);
    // Optionally redirect or show a success message
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <div class="w-64 bg-gray-800 text-white">
            <div class="p-4">
                <h2 class="text-2xl font-bold">Admin Dashboard</h2>
            </div>
            <nav class="mt-4" id="sidebar-nav">
    <a href="#" data-section="dashboard" class="block py-3 px-4 text-gray-300 hover:bg-gray-700 hover:text-white active">
        Dashboard Overview
    </a>
 
    <a href="#" data-section="vehicles" class="block py-3 px-4 text-gray-300 hover:bg-gray-700 hover:text-white">
        Manage Vehicles
    </a>
    <a href="#" data-section="manage-reservations" class="block py-3 px-4 text-gray-300 hover:bg-gray-700 hover:text-white">
        Manage Reservations
    </a>
    <a href="#" data-section="reviews" class="block py-3 px-4 text-gray-300 hover:bg-gray-700 hover:text-white">
        Manage Reviews
    </a>
   
</nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <header class="bg-white shadow">
                <div class="flex items-center justify-between px-6 py-4">
                    <h1 class="text-2xl font-semibold text-gray-900" id="section-title">Dashboard Overview</h1>
                    <div class="flex items-center">
                        <span class="text-gray-700 mr-4">Welcome to your dashboard!</span>
                        <a href="../connexion/logout.php"><button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Logout </button></a>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <section id="dashboard" class="section-content">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-gray-500 text-sm font-medium">Total Vehicles</h3>
                            <p class="text-3xl font-bold text-gray-900"><?php
                           echo $total2;
                            ?></p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-gray-500 text-sm font-medium">Total Reservations</h3>
                            <p class="text-3xl font-bold text-gray-900"><?php
                           echo $total3;
                            ?></p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-gray-500 text-sm font-medium">Total Reviews</h3>
                            <p class="text-3xl font-bold text-gray-900"><?php
                           echo $total4;
                            ?></p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-gray-500 text-sm font-medium">Total Users</h3>
                            <p class="text-3xl font-bold text-gray-900"><?php
                           echo $total1;
                            ?></p>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">All Clients</h1>
        
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-semibold">Manage Clients</h2>
                </div>
                <div class="p-6">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="text-left p-3">Client Name</th>
                                <th class="text-left p-3">Email</th>
                                <th class="text-left p-3">Account Created</th>
                                <th class="text-left p-3">Number of Reservations</th>
                                <th class="text-left p-3">Number of Reviews</th>
                                <th class="text-left p-3">Archive</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client): ?>
                                <tr>
                                    <td class="p-3"><?php echo htmlspecialchars($client['firstName'] . ' ' . $client['lastName']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($client['email']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($client['createdAt']); ?></td>
                                    <td class="p-3 text-center"><?php echo htmlspecialchars($client['num_reservations']); ?></td>
                                    <td class="p-3 text-center"><?php echo htmlspecialchars($client['num_reviews']); ?></td>
                                    <td class="p-3 ">
                                        <form action="Archiveruser.php" method="POST">
                                            <input type="hidden" name="clientId" value="<?php echo $client['id']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-800">Archive</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
    </div>
                </section>

                <!-- Manage Vehicles Section -->
<section id="vehicles" class="section-content hidden">
<div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Manage Vehicles</h1>
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b flex justify-between items-center">
                <h2 class="text-xl font-semibold">Vehicle Management</h2>
                <button onclick="showAddVehicleForm()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add New Vehicle</button>
                <button onclick="showAddCategoryForm()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Add New Category</button>
            </div>
            <div class="p-6">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="text-left p-3">Image</th>
                            <th class="text-left p-3">Model</th>
                            <th class="text-left p-3">Brand</th>
                            <th class="text-left p-3">Category</th>
                            <th class="text-left p-3">Price Per Day (DH)</th>
                            <th class="text-left p-3">Status</th>
                            <th class="text-left p-3">Reserved By</th>
                            <th class="text-left p-3">Description</th>
                            <th class="text-left p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <tr>
                                <td class="p-3"><img src="<?php echo htmlspecialchars($vehicle['image']); ?>" alt="Vehicle Image" class="w-16 h-16 object-cover"></td>
                                <td class="p-3"><?php echo htmlspecialchars($vehicle['model']); ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($vehicle['brand']); ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($vehicle['category_name']); ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($vehicle['pricePerDay']); ?> DH</td>
                                <td class="p-3"><?php echo htmlspecialchars($vehicle['status']); ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($vehicle['firstName'] . ' ' . $vehicle['lastName']); ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($vehicle['description']); ?></td>
                                <td class="p-3">
                                <form >
                                <button onclick="showUpdateForm(<?php echo htmlspecialchars(json_encode($vehicle)); ?>)" class="text-blue-600 hover:text-blue-900">Edit</button>
                                    <form action="Adminedellit.php" method="POST" class="inline">
                                        <input type="hidden" name="vehicle_id" value="<?php echo $vehicle['id']; ?>">
                                        <button class="text-red-600 hover:text-red-900 ml-2" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>







<!-- Update Vehicle Form -->
<div id="updateVehicleForm" class="fixed inset-0 flex items-center justify-center hidden z-50  bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Update Vehicle</h3>
            <form id="editVehicleForm" class="space-y-4">
                <input type="hidden" name="vehicle_id" id="vehicle_id">
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Model</label>
                    <input type="text" name="model" id="model" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Brand</label>
                    <input type="text" name="brand" id="brand" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Price Per Day (DH)</label>
                    <input type="number" name="pricePerDay" id="pricePerDay" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" required rows="3" class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                </div>
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="categoryId" id="categoryId" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="available">Available</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-2 bg-green-600 text-white rounded hover:bg-green-700">Update Vehicle</button>
            </form>
            <button onclick="hideUpdateForm()" class="w-full py-2 mt-4 bg-red-600 text-white rounded hover:bg-red-700">Cancel</button>
        </div>
    </div>

           











    <!-- Add New Vehicle Form -->
    <div id="addVehicleForm" class="fixed inset-0 flex items-center  justify-center z-50 hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Add New Vehicle</h3>
            <form id="newVehicleForm" action="setvihucle.php" method="POST" class="space-y-4" enctype="multipart/form-data">
                <div id="vehicleFields">
                    <!-- Dynamic vehicle form fields will appear here -->
                    <div class="vehicle-field flex space-x-4 mb-4">
                        <div class="flex flex-col w-full">
                            <label class="text-sm font-medium text-gray-700 mb-1">Model</label>
                            <input type="text" name="model" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div class="flex flex-col w-full">
                            <label class="text-sm font-medium text-gray-700 mb-1">Brand</label>
                            <input type="text" name="brand" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="vehicle-field flex space-x-4 mb-4">
                        <div class="flex flex-col w-full">
                            <label class="text-sm font-medium text-gray-700 mb-1">Price Per Day (DH)</label>
                            <input type="number" name="pricePerDay" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="vehicle-field flex space-x-4 mb-4">
                        <div class="flex flex-col w-full">
                            <label class="text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" required rows="3" class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                        </div>
                    </div>
                    <div class="flex flex-col w-full">
                        <label for="image" class="text-sm font-medium text-gray-700 mb-1">Image</label>
                        <input type="file" name="image" id="image" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div class="vehicle-field flex space-x-4 mb-4">
                        <div class="flex flex-col w-full">
                            <label class="text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="categoryId" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex flex-col w-full">
                            <label class="text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="hideAddVehicleForm()" class="px-4 py-2 bg-gray-400 text-white rounded-md shadow-sm hover:bg-gray-500 transition">
                        Cancel
                    </button>
                    <button type="button" onclick="addNewVehicleField()" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700 transition">
                        Add More Vehicles
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md shadow-sm hover:bg-green-700 transition">
                        Add Vehicle
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add New Category Form -->
    <div id="addCategoryForm" class="fixed inset-0 flex items-center justify-center z-50 hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Add New Category</h3>
            <form id="newCategoryForm" action="setcategory.php" method="POST" class="space-y-4">
                <div id="categoryFields">
                    <div class="category-field flex flex-col w-full mb-4">
                        <label class="text-sm font-medium text-gray-700 mb-1">Category Name</label>
                        <input type="text" name="name" id="categoryName" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div class="category-field flex flex-col w-full mb-4">
                        <label class="text-sm font-medium text-gray-700 mb-1">Category Description</label>
                        <input type="text" name="description" id="categoryDescription" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="hideAddCategoryForm()" class="px-4 py-2 bg-gray-400 text-white rounded-md shadow-sm hover:bg-gray-500 transition">
                        Cancel
                    </button>
                    <button type="button" onclick="addNewCategoryField()" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700 transition">
                        Add More Categories
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md shadow-sm hover:bg-green-700 transition">
                        Add Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>




                <!-- Manage Reservations Section -->
                <section id="manage-reservations" class="section-content hidden">
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
                                        <th class="text-left p-3">Dropoff Location</th>
                                        <th class="text-left p-3">Total Price</th>
                                        <th class="text-left p-3">Status</th>
                                        <th class="text-left p-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                     $reservation = new Reservation();

                                     $reservations = $reservation->getAllReservations();
                                   
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
                                            echo "<span class='px-2 py-1 " . ($reservation['status'] == 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') . " rounded-full text-sm'>" . htmlspecialchars($reservation['status']) . "</span>";
                                            echo "</td>";

                                            echo "<td class='p-3'>";
                                                // Show confirm/refuse buttons
                                echo "<form method='POST' class='inline'>";
                                echo "<input type='hidden' name='reservation_id' value='" . htmlspecialchars($reservation['id']) . "'>";
                                echo "<input type='hidden' name='status' value='confirmed'>";
                                echo "<button type='submit' class='text-blue-600 hover:text-blue-900'>Confirm</button>";
                                echo "</form>";

                                            echo "<form method='POST' class='inline'>";
                                            echo "<input type='hidden' name='reservation_id' value='" . htmlspecialchars($reservation['id']) . "'>";
                                            echo "<input type='hidden' name='status' value='refused'>";
                                            echo "<button type='submit' class='bg-red-300 rounded-lg p-1 text-blue-600 hover:text-blue-900'>refused</button>";
                                            echo "</form>";
                                        
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div> 
                  </section>




                <!-- Manage Reviews Section -->
                <section id="reviews" class="section-content hidden">
                    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">All Reviews</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if ($allReviews && count($allReviews) > 0): ?>
                <?php foreach ($allReviews as $review): ?>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-2">
                            <?php echo "User: "." ". htmlspecialchars($review['firstName'] . ' ' . $review['lastName']); ?> 
                            <?php echo "<br> "?>
                            <?php echo "car: "." ". htmlspecialchars($review['brand'] . ' ' . $review['model']); ?>
                        </h2>
                        <div class="flex items-center mb-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="text-yellow-400">
                                    <?php echo $i <= $review['rating'] ? '★' : '☆'; ?>
                                </span>
                            <?php endfor; ?>
                        </div>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($review['comment']); ?></p>
                        <div class="flex justify-between items-center">
                         
                            <form action="delete_review.php" method="POST">
                                <input type="hidden" name="reviewId" value="<?php echo $review['id']; ?>">
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
                </section>

                
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('#sidebar-nav a');
        const sections = document.querySelectorAll('.section-content');
        const sectionTitle = document.getElementById('section-title');

        function showSection(sectionId) {
            sections.forEach(section => {
                section.classList.add('hidden');
            });
            document.getElementById(sectionId).classList.remove('hidden');
            
            navLinks.forEach(link => {
                link.classList.remove('bg-blue-600');
                if (link.dataset.section === sectionId) {
                    link.classList.add('bg-blue-600');
                    sectionTitle.textContent = link.textContent.trim();
                }
            });
        }

        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const sectionId = link.dataset.section;
                showSection(sectionId);
            });
        });
        function showUpdateForm(vehicle) {
            document.getElementById('updateVehicleForm').classList.remove('hidden');

            populateForm(vehicle);
        }

        function hideUpdateForm() {
            document.getElementById('updateVehicleForm').classList.add('hidden');
        }

        function populateForm(vehicle) {
            document.getElementById('vehicle_id').value = vehicle.id;
            document.getElementById('model').value = vehicle.model;
            document.getElementById('brand').value = vehicle.brand;
            document.getElementById('description').value = vehicle.description;
            document.getElementById('pricePerDay').value = vehicle.pricePerDay;
            document.getElementById('status').value = vehicle.status;
            document.getElementById('categoryId').value = vehicle.categoryId;
            document.getElementById('image').value = vehicle.image;
        }

    });

    function showAddVehicleForm() {
        document.getElementById('addVehicleForm').classList.remove('hidden');
    }

    function hideAddVehicleForm() {
        document.getElementById('addVehicleForm').classList.add('hidden');
    }

    function showAddCategoryForm() {
        document.getElementById('addCategoryForm').classList.remove('hidden');
    }

    function hideAddCategoryForm() {
        document.getElementById('addCategoryForm').classList.add('hidden');
    }

    document.getElementById('newVehicleForm').addEventListener('submit', function(event) {
        hideAddVehicleForm();
    });

    document.getElementById('newCategoryForm').addEventListener('submit', function(event) {
        hideAddCategoryForm();
    });
 

    </script>
</body>
</html>