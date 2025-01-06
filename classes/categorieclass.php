<?php
require_once 'connectiondatabase.php';  

class Categorie extends Data {
    public $name;
    public $description;
    public $pdo;

    public function __construct() {
        $this->pdo = $this->connextion();  
    }
    public function categories($name,$description){
       
        try {
            $query = "INSERT INTO categories (name, description)
            VALUES (:name, :description)";
            $stmt = $this->pdo->prepare($query);  
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":description", $description);
            if ($stmt->execute()) {
            echo "Data sent successfully";


                //header('Location: ../connexion/signin.php');
                exit();
            } else {
                $errormessage = $stmt->errorInfo();
                echo "Error: " . $errormessage[2];
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    

    }
    public function getAllCategories() {
        try {
            $query = "SELECT * FROM categories";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
            return [];
        }
    }
    public function getAvailableVehicles() {
        $query = "SELECT * FROM vehicles WHERE categoryId = :categoryId AND status = 'available'";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":categoryId", $this->id);  
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);  
    }

    public function getVehicleCount() {
        $query = "SELECT COUNT(*) AS vehicle_count FROM vehicles WHERE categoryId = :categoryId";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":categoryId", $this->id);  
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['vehicle_count'];  
    }

    public function filterVehicles($criteria) {
        $query = "SELECT * FROM vehicles WHERE categoryname = :categoryname";
        
      
        if (isset($criteria['model'])) {
            $query .= " AND model LIKE :model";
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":categoryId", $this->id);

        if (isset($criteria['priceMin'])) {
            $stmt->bindParam(":priceMin", $criteria['priceMin']);
        }
        if (isset($criteria['priceMax'])) {
            $stmt->bindParam(":priceMax", $criteria['priceMax']);
        }
        if (isset($criteria['model'])) {
            $stmt->bindParam(":model", "%{$criteria['model']}%");
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }
}
?>
