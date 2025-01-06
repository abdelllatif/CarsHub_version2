<?php
session_start(); 
require_once 'classes/classvihcule.php';
require_once 'classes/categorieclass.php';
require_once 'classes/reviewclass.php';

$categoris = new categorie();
$categoris->getAllCategories();
$userIsRegistered = isset($_SESSION['user_email']) && isset($_SESSION['user_id']);
$review = new review();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Location de véhicules simple et rapide pour tous vos besoins.">
    <title>Location de Véhicules - AutoLoc</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .loading {
            display: none;
        }
        .loading.active {
            display: block;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100 flex flex-col">
    <!-- Navigation -->
    <nav class="bg-white shadow-md fixed w-full z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-2">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span class="text-2xl font-bold text-blue-600">AutoLoc</span>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-gray-600 hover:text-blue-600">Accueil</a>
                    <a href="#vehicles" class="text-gray-600 hover:text-blue-600">Véhicules</a>
                    <a href="users/myreview.php" class="block text-gray-600 hover:text-blue-600">myreviesws</a>
                    <a href="users/myreservation.php" class="block text-gray-600 hover:text-blue-600">Myreservations</a>

                    <div class="flex items-center space-x-4">
                        <a href="connexion/singin.php">
                        <button id="loginBtn" class="text-gray-600 hover:text-blue-600" aria-label="Connexion">
                            <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Connexion
                        </button>
                        </a>
                    </div>
                </div>

                <button id="mobileMenuBtn" class="md:hidden" aria-label="Menu mobile">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden md:hidden fixed w-full bg-white shadow-md z-40 top-20">
        <div class="p-4 space-y-4">
            <a href="#" class="block text-gray-600 hover:text-blue-600">Accueil</a>
            <a href="#vehicles" class="block text-gray-600 hover:text-blue-600">Véhicules</a>
            <a href="users/myreview.php" class="block text-gray-600 hover:text-blue-600">myreviesws</a>
            <a href="users/myreservation.php" class="block text-gray-600 hover:text-blue-600">Myreservations</a>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow pt-20">
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-4xl md:text-5xl font-bold">Bienvenue chez AutoLoc</h1>
                <p class="mt-4 text-lg">Votre solution de location de véhicules rapide et fiable.</p>
                <a href="#vehicles" class="mt-8 inline-block bg-white text-blue-600 px-6 py-3 rounded-lg hover:bg-gray-200">Explorer nos véhicules</a>
            </div>
        </section>

 <!-- Vehicles Section -->
 <section id="vehicles" class="py-16 bg-gray-100">
        <div class="flex gap-4 mb-8">
            <div class="relative flex-grow">
                <input type="text" id="searchInput" placeholder="Rechercher un véhicule (ex: BMW 2002)" class="border border-gray-300 rounded-lg py-2 px-4 w-full" />
            </div>
            <select id="categoryFilter" class="border border-gray-300 rounded-lg py-2 px-4">
                <option value="">Toutes les catégories</option>
                <?php 
                $categories = $categoris->getAllCategories();
                foreach ($categories as $category) {
                    echo '<option value="' . htmlspecialchars($category['id']) . '">' . htmlspecialchars($category['name']) . '</option>';
                }
                ?>
            </select>
            <button id="filterBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Filtrer
            </button>
        </div>
    </select>
 
                <!-- Best Offers Section -->
   <!-- Best Offers Section -->
   <section class="py-16 bg-gray-100">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold">Meilleures Offres</h2>
            <p class="text-gray-600">Découvrez nos voitures les mieux notées</p>
        </div>
        <div class="mx-auto px-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php
        $vehicle = new Vehicle();
        $cars = $vehicle->getCars(); 

        $limit = 8; 

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max($page, 1); 

        $totalCars = $vehicle->getTotalCars();
        $totalPages = ceil($totalCars / $limit); 

        $cars = $vehicle->paginateVehicles($page, $limit);

        foreach ($cars as $car) {
            $brand = htmlspecialchars($car['brand']);
            $model = htmlspecialchars($car['model']);
            $pricePerDay = htmlspecialchars($car['pricePerDay']);
            $image = htmlspecialchars($car['image']);
            $status = htmlspecialchars($car['status']);
            $isAvailable = $vehicle->checkAvailability($car['id']);

            // Get the recent rating for this car
            $recentRating = $review->getRecentRating($car['id']);
            $ratingValue = $recentRating ? $recentRating['rating'] : 0;
            $totalReviews = $review->getTotalReviews($car['id']);

            echo "
                <div class=\"bg-white shadow-md rounded-lg overflow-hidden transform hover:scale-105 transition\">
                    <img src=\"Admin/$image\" alt=\"$brand $model\" class=\"carcontainer h-44 w-full\"> 
                    <div class=\"p-6\">
                        <h3 class=\"text-xl font-semibold\">$brand $model</h3>
                        <p class=\"text-gray-600\">À partir de $pricePerDay €/jour</p>
                        <div class=\"mt-4 flex justify-between items-center\">
                            <div class=\"stars\" data-rating=\"$ratingValue\">
                                <span class=\"star cursor-pointer\" data-value=\"1\">★</span>
                                <span class=\"star cursor-pointer\" data-value=\"2\">★</span>
                                <span class=\"star cursor-pointer\" data-value=\"3\">★</span>
                                <span class=\"star cursor-pointer\" data-value=\"4\">★</span>
                                <span class=\"star cursor-pointer\" data-value=\"5\">★</span>
                            </div>
                            <span class=\"text-gray-600 ml-2\">($totalReviews avis)</span>
                            <button onclick=\"openAllAvisPopup({$car['id']})\" class=\"viewReviewsBtn text-sm text-gray-800 py-2 cursor-pointer\">Voir les avis</button>
                        </div>";

            if ($userIsRegistered) { 
                if ($isAvailable) {
                    echo "<button type=\"button\" onclick=\"showreservationform(" . htmlspecialchars(json_encode($car)) . ")\" class=\"reserveBtns mt-6 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700\">Réserver</button>";
                } else {
                    echo "<span class=\"mt-6 w-full bg-gray-400 text-white py-2 rounded-lg\">Not Available</span>";
                }
            } else {
                echo "<button type=\"button\" onclick=\"showLoginAlert()\" class=\"mt-6 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700\">Réserver</button>";
            }

            echo "
                    </div>
                </div>
            ";
        }
        ?>
        </div>
 
    </section>
                
<!-- All Avis Popup -->
<div id="allAvisPopup" class="popup fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="popup-content bg-white p-6 rounded-lg max-w-lg w-full shadow-lg">
        <h2 class="text-xl font-bold mb-4"> All reviews</h2>
        <div id="reviewsList" class="mb-4">
            <?php
            $pdo = new Review();

            $pdos=$pdo->getReviewsByVehicle($car['id']) ;  
            var_dump($pdos);
                echo  "   <p>Total reviews : $totalReviews</p>";       
    if ($pdos) {
        foreach ($pdos as $review) {
            echo "<div class='bg-gray-100 p-2 mb-2 rounded'>";
            echo "<p><strong>" . htmlspecialchars($review['firstName'] . ' ' . $review['lastName']) . "</strong> - Rating: " . str_repeat('★', $review['rating']) . "</p>";
            echo "<p>" . htmlspecialchars($review['comment']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No reviews yet.</p>";
    }
     ?>
        </div>
        <button onclick="closeAllAvisPopup() "  id="closeAllAvisPopup" class="bg-red-500 text-white px-4 py-2 rounded mt-4">Close</button>
    </div>
</div>
                <!-- Car Details Popup -->
                <div id="carDetailsPopup" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                    <div class="flex justify-between">
                        <div class="bg-white rounded-lg p-6 max-w-2xl flex">
                            <div class="w-1/2">
                                <img src="https://www.mercedes-benz.ca/content/dam/mb-nafta/ca/myco/my22/eqb-suv/all-vehicles/MBCAN-2022-EQB350W4-SUV-AVP-DR.png" alt="Car Model" class="car-image animate-shadow">
                            </div>
                            <div class="w-1/2 pl-6">
                                <h3 class="text-2xl font-bold mb-2">Modèle: Voiture Modèle 1</h3>
                                <p class="text-lg mb-2"><strong>Prix:</strong> À partir de 49€/jour</p>
                                <p class="text-lg mb-2"><strong>Disponibilité:</strong> Disponible</p>
                                <p class="text-lg mb-4"><strong>Description:</strong> Une voiture confortable et spacieuse, idéale pour les trajets en famille ou entre amis.</p>
                                <div class="flex justify-end">
                                    <button id="closeCarDetails" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 mr-4">Fermer</button>
                                    <button id="" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Réserver</button>
                                </div>
                            </div>
                        </div>

                        
                    </div>
                </div>        

                <script>
                    function submitComment() {
                        alert('Comment submitted!');
                    }

                    function showLoginAlert() {
                        Swal.fire({
                            title: 'Connexion requise',
                            text: 'Veuillez vous connecter pour réserver un véhicule',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Se connecter',
                            cancelButtonText: 'Annuler'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'connexion/singin.php';
                            }
                        });
                    }

                    document.getElementById('closeCarDetails').addEventListener('click', () => {
                        document.getElementById('carDetailsPopup').classList.add('hidden');
                    });

                    document.getElementById('reservation-form').addEventListener('submit', function(event) {
                        event.preventDefault();
                        Swal.fire({
                            title: 'Réservation confirmée!',
                            text: 'Votre réservation a été enregistrée avec succès. Vous pouvez maintenant laisser un commentaire.',
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Laisser un commentaire',
                            cancelButtonText: 'Fermer'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById('comment-section').classList.remove('hidden');
                            }
                            document.getElementById('reservation-popup').classList.add('hidden');
                        });
                    });
                </script>

                <!-- Reservation Popup -->
                <div id="reservation-popup"  class="hidden fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-96">
        <h2 class="text-xl font-bold mb-4">Réservation de Voiture</h2>
        <form id="reservation-form"  action="users/setreservation.php" method="POST" class="space-y-4">
            <input type="hidden" id="id_car" name="id_car" required
            class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300 focus:outline-none" />
            <div>
                <label for="debueDate" class="block text-sm font-medium text-gray-700">Date de Debut</label>
                <input type="datetime-local" id="debueDate" name="debueDate" required
                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300 focus:outline-none" />
            </div>
            <div>
                <label for="returnDate" class="block text-sm font-medium text-gray-700">Date de Retour</label>
                <input type="datetime-local" id="returnDate" name="returnDate" required
                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300 focus:outline-none" />
            </div>
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700">Adresse</label>
                <textarea id="address" name="address" required rows="3"
                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300 focus:outline-none" placeholder="Entrez votre adresse"></textarea>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" id="cancel-button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Confirmer la Réservation</button>
            </div>
        </form>
    </div>
</div>

                <!-- Pagination -->
                <div class="flex justify-center mt-8">
                    <form method="GET" action="">
                        <input type="hidden" name="page" value="<?php echo max($page - 1, 1); ?>">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700" <?php if ($page <= 1) echo 'disabled'; ?>>Précédent</button>
                    </form>
                    
                    <form method="GET" action="">
                        <input type="hidden" name="page" value="<?php echo min($page + 1, $totalPages); ?>">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 ml-4" <?php if ($page >= $totalPages) echo 'disabled'; ?>>Suivant</button>
                    </form>
                </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-8">
            <div class="container mx-auto px-4 text-center">
                <p>&copy; 2023 AutoLoc. Tous droits réservés.</p>
            </div>
        </footer>
    </main>
    <script>

  


document.querySelectorAll('.carcontainer').forEach(car => {
    car.addEventListener('click', () => {
        document.getElementById('carDetailsPopup').classList.remove('hidden');
    });
});

document.getElementById('closeCarDetails').addEventListener('click', () => {
    document.getElementById('carDetailsPopup').classList.add('hidden');
});



document.getElementById('cancelRating').addEventListener('click', () => {
    document.getElementById('reviewPopup').classList.add('hidden');
    ratingInput.value = 0;
    updateStarColors(0);
});

document.getElementById('submitRating').addEventListener('click', () => {
    const rating = parseInt(ratingInput.value);
    if (rating > 0) {
        alert(`Merci pour votre évaluation de ${rating} étoiles!`);
        document.getElementById('reviewPopup').classList.add('hidden');
    } else {
        alert('Veuillez sélectionner une note.');
    }
});

document.getElementById('nextPage').addEventListener('click', function() {
    alert('Next page functionality to be implemented.');
});

document.getElementById('prevPage').addEventListener('click', function() {
    alert('Previous page functionality to be implemented.');
});

document.querySelectorAll('.reserveBtns').forEach(button => {
    button.addEventListener('click', () => {
        document.getElementById('reservation-popup').classList.remove('hidden');
    });
});

document.getElementById('cancel-button').addEventListener('click', () => {
    document.getElementById('reservation-popup').classList.add('hidden');
});

document.getElementById('reservation-form').addEventListener('submit', function(event) {
    event.preventDefault();
    alert('Réservation confirmée!');
    document.getElementById('reservation-popup').classList.add('hidden');
});




/**********************************************/ 
function showreservationform(car) {
    document.getElementById('id_car').value = car.id; 
    const reservationPopup = document.getElementById('reservation-popup');
    const cancelButton = document.getElementById('cancel-button');
    
    reservationPopup.classList.remove('hidden');

    cancelButton.addEventListener('click', function() {
        reservationPopup.classList.add('hidden');
    });
}








function showLoginAlert() {
    Swal.fire({
        title: 'Connexion requise',
        text: 'Veuillez vous connecter pour réserver un véhicule',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Se connecter',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'connexion/singin.php';
        }
    });
}

document.querySelectorAll('.reserveBtns').forEach(button => {
    button.addEventListener('click', () => {
        Swal.fire({
            title: 'Confirmer la réservation',
            text: 'Êtes-vous sûr de vouloir réserver ce véhicule ?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Oui, réserver',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('reservation-popup').classList.remove('hidden');
            }
        });
    });
});

document.getElementById('reservation-form').addEventListener('submit', function(event) {
    event.preventDefault();
    Swal.fire({
        title: 'Réservation confirmée!',
        text: 'Votre réservation a été enregistrée avec succès. Vous pouvez maintenant laisser un commentaire.',
        icon: 'success',
        showCancelButton: true,
        confirmButtonText: 'Laisser un commentaire',
        cancelButtonText: 'Fermer'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show comment form
            document.getElementById('comment-section').classList.remove('hidden');
        }
        document.getElementById('reservation-popup').classList.add('hidden');
    });
});

// Add this to the car details popup HTML
const commentSection = `
    <div id="comment-section" class="hidden mt-4">
        <h4 class="text-lg font-semibold mb-2">Ajouter un commentaire</h4>
        <textarea 
            class="w-full p-2 border rounded-lg mb-2" 
            placeholder="Partagez votre expérience..."></textarea>
        <button 
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
            onclick="submitComment()">
            Envoyer
        </button>
    </div>
`;

// Add this function for comment submission
function submitComment() {
    const commentText = document.querySelector('#comment-section textarea').value;
    if (commentText.trim()) {
        Swal.fire({
            title: 'Merci!',
            text: 'Votre commentaire a été ajouté avec succès',
            icon: 'success'
        });
        document.querySelector('#comment-section textarea').value = '';
        document.getElementById('comment-section').classList.add('hidden');
    } else {
        Swal.fire({
            title: 'Erreur',
            text: 'Veuillez entrer un commentaire',
            icon: 'error'
        });
    }
}



    // Add click event to "Voir les avis" buttons
    const viewReviewsButtons = document.querySelectorAll('.viewReviewsBtn');
    viewReviewsButtons.forEach(button => {
        button.addEventListener('click', function() {
            const carId = this.getAttribute('data-car-id');
            const reviewsContainer = document.getElementById(`allAvisPopup-${carId}`);
            
            if (reviewsContainer.classList.contains('hidden')) {
                // Fetch and display reviews
                fetch(`get_reviews.php?carId=${carId}`)
                    .then(response => response.text())
                    .then(data => {
                        reviewsContainer.innerHTML = data;
                        reviewsContainer.classList.remove('hidden');
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                // Hide reviews if they're already visible
                reviewsContainer.classList.add('hidden');
            }
        });



});

function openAllAvisPopup(car) {
    document.getElementById('allAvisPopup').classList.remove('hidden');
    
}
function closeAllAvisPopup() {
    const allAvisPopup = document.getElementById('allAvisPopup');
    allAvisPopup.classList.add('hidden'); // Hide the popup
}



</script>

</body>
</html>