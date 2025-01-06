<?php
require_once 'connectiondatabase.php'; 

class Reservation extends data { 
    public $pdo; 
  protected $createdAt;
    public function __construct() {
        $this->pdo = $this->connextion(); 
    }
    
    public function createReservation($vehicleId, $startDate, $endDate, $location, $clientId) {
        $totalPrice = $this->calculatePrice($vehicleId, $startDate, $endDate); 
        $this->createdAt = date("Y-m-d H:i:s"); 

        $query = "INSERT INTO reservations (vehicleId, customerId, startDate, endDate, pickupLocation, totalPrice, status, createdAt)
        VALUES (:vehicleId, :clientId, :startDate, :endDate, :location, :totalPrice, 'en Attend', :createdAt)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":vehicleId", $vehicleId);
        $stmt->bindParam(":clientId", $clientId);
        $stmt->bindParam(":startDate", $startDate);
        $stmt->bindParam(":endDate", $endDate);
        $stmt->bindParam(":location", $location);
        $stmt->bindParam(":totalPrice", $totalPrice);
        $stmt->bindParam(":createdAt", $this -> createdAt);


        if ($stmt->execute()) { 
            return "Reservation successfully created!";
        } else {
            return "Error creating reservation.";
        }
    }

    public function calculatePrice($vehicleId, $startDate, $endDate) {
        $query = "SELECT pricePerDay FROM vehicles WHERE id = :vehicleId"; 
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":vehicleId", $vehicleId);
        $stmt->execute();
        $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($vehicle) {
            $pricePerDay = $vehicle['pricePerDay'];
            $start = new DateTime($startDate);
            $end = new DateTime($endDate); 
            $interval = $start->diff($end); 
            $days = $interval->days;

            return $pricePerDay * $days; 
        }
      else{
        return 0; 
    }
        
    }

    public function confirmReservation($reservationId) {
        $query = "UPDATE reservations SET status = 'confirmed' WHERE id = :reservationId";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":reservationId", $reservationId);

        if ($stmt->execute()) {
            return "Reservation confirmed!";
        } else {
            return "Error confirming reservation.";
        }
    }

    public function cancelReservation($reservationId) {
        $query = "UPDATE reservations SET status = 'cancelled' WHERE id = :reservationId";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":reservationId", $reservationId);

        if ($stmt->execute()) {
            return "Reservation cancelled!";
        } else {
            return "Error cancelling reservation.";
        }
    }

    public function updateStatus($reservationId, $status) {
        try {
            // Log the incoming values
            error_log("Attempting to update - ID: $reservationId, Status: $status");
            
            $query = "UPDATE reservations SET status = :status WHERE id = :reservationId"; 
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":reservationId", $reservationId, PDO::PARAM_INT);
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        
            $result = $stmt->execute();
            if ($result) {
                error_log("Update successful");
                return "Reservation status updated!";
            } else {
                error_log("Update failed: " . print_r($stmt->errorInfo(), true));
                return "Error updating reservation status.";
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return "Database error: " . $e->getMessage();
        }
    }
    public function getAllReservations() {
        $stmt = $this->pdo->prepare("SELECT r.*, u.firstName AS user_name, u.lastName AS user_lastname, v.model AS vehicle, v.brand AS v_brand 
                                     FROM reservations r
                                     JOIN clients u ON r.customerId = u.id
                                     JOIN vehicles v ON r.vehicleId = v.id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getmyReservations($userId) {
        $stmt = $this->pdo->prepare("SELECT r.*, u.firstName AS user_name, u.lastName AS user_lastname, v.model AS vehicle, v.brand AS v_brand 
                                     FROM reservations r
                                     JOIN clients u ON r.customerId = u.id
                                     JOIN vehicles v ON r.vehicleId = v.id
                                     WHERE r.customerId = :userId");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function deleteReservation($reservationId) {
        $sql = "DELETE FROM reservations WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$reservationId]);
    }
    public function getReservationById($reservationId) {
        $query = "SELECT * FROM reservations WHERE id = :reservationId";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":reservationId", $reservationId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }
}
?>
