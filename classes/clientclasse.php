<?php 
require_once 'userclasse.php';  
require_once 'reservationclass.php';  

class Client extends User {
    public $address;
    public $phone;
    
    public function __construct($lastName, $firstName, $email, $password, $phone, $address = null) {
        parent::__construct($lastName, $firstName, $email, $password, $phone);
        $this->address = $address;
        $this->phone = $phone;
    }

    public function makeReservation($vehicleId, $startDate, $endDate, $location) {
        $reservation = new Reservation();
        return $reservation->createReservation($vehicleId, $startDate, $endDate, $location, $this->id);
    }

    public function filterVehiclesByCategory($categoryId) {
        $query = "SELECT * FROM vehicles WHERE categoryId = :categoryId";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":categoryId", $categoryId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchVehicles($criteria) {
        $query = "SELECT * FROM vehicles WHERE make LIKE :criteria OR model LIKE :criteria OR location LIKE :criteria";
        $stmt = $this->pdo->prepare($query);
        $searchTerm = "%" . $criteria . "%"; 
        $stmt->bindParam(":criteria", $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function paginateResults($page, $limit) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT * FROM vehicles LIMIT :offset, :limit";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addReview($vehicleId, $rating, $comment) {
        $query = "INSERT INTO reviews (vehicleId, clientId, rating, comment, createdAt)
        VALUES (:vehicleId, :clientId, :rating, :comment, :createdAt)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":vehicleId", $vehicleId);
        $stmt->bindParam(":clientId", $_SESSION['user_id']);
        $stmt->bindParam(":rating", $rating);
        $stmt->bindParam(":comment", $comment);
        $stmt->bindParam(":createdAt", date("Y-m-d H:i:s"));
        if ($stmt->execute()) {
         echo "Review added successfully!";
        } else {
        echo "Error adding review.";
        }
    }

    public function getMyReviews() {
        $query = "SELECT * FROM reviews WHERE clientId = :clientId";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":clientId", $_SESSION['user_id']);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMyReservations() {
        $query = "SELECT * FROM reservations WHERE clientId = :clientId";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":clientId", $_SESSION['user_id']);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>
