<?php
require_once 'connectiondatabase.php';  
class Vehicle extends Data{
    public $pdo;
    public $image;
    private $uploadDir = 'uploads/';  

    public function __construct() {
            $this->pdo = $this->connextion();  

      

        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function uploadImage($imageData) {
        if ($imageData['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Upload failed with error code " . $imageData['error']);
        }
    
        $imageInfo = getimagesize($imageData['tmp_name']);
        if ($imageInfo === false) {
            throw new Exception("Uploaded file is not a valid image.");
        }
    
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($imageInfo['mime'], $allowedTypes)) {
            throw new Exception("Invalid file type. Only JPG, PNG, and GIF are allowed.");
        }
    
        $extension = pathinfo($imageData['name'], PATHINFO_EXTENSION);
        $newFilename = uniqid() . '.' . $extension;
        $targetPath = $this->uploadDir . $newFilename;
    
        echo "Target path: $targetPath<br>"; 
    
        if (!move_uploaded_file($imageData['tmp_name'], $targetPath)) {
            throw new Exception("Failed to move uploaded file.");
        }
    
        $this->image = $targetPath;
    
        return $targetPath;
    }
    
    // public function getCategoryIdByName($categoryName) {
    //     if (empty($categoryName)) {
    //         throw new Exception("Category name cannot be empty");
    //     }

    //     $stmt = $this->pdo->prepare("SELECT id FROM categories WHERE name = ?");
    //     $stmt->execute([$categoryName]);
    //     $result = $stmt->fetch(PDO::FETCH_COLUMN);
    //     var_dump($result);

    //     if (!$result) {
    //         throw new Exception("Category not found: " . $categoryName);
    //     }
    //     return $result;
    // }

    public function save($model, $brand, $description, $pricePerDay, $status, $categoryId, $characteristics, $image, $createdAt) {
        // Validate required fields
        if (empty($model) || empty($brand) || empty($categoryId)) {
            throw new Exception("Model, brand, and category are required fields");
        }

        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO vehicles (
                    model, brand, description, pricePerDay, 
                    status, categoryId, characteristics, image, createdAt
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            return $stmt->execute([
                $model,
                $brand,
                $description,
                $pricePerDay,
                $status,
                $categoryId,
                $characteristics,
                $image,
                $createdAt
            ]);
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
    
    public function getAllVehicles() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT v.id, v.model, v.brand, v.pricePerDay, v.status, v.image, v.description, 
                       c.name AS category_name, cl.firstName, cl.lastName
                FROM vehicles v
                LEFT JOIN categories c ON v.categoryId = c.id
                LEFT JOIN reservations r ON v.id = r.vehicleId
                LEFT JOIN clients cl ON r.customerId = cl.id
                ORDER BY v.createdAt DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching vehicles: " . $e->getMessage());
            return false;
        }
    }

    public function validateData($data) {
        $errors = [];

        if (empty($data['model'])) $errors[] = "Model is required";
        if (empty($data['brand'])) $errors[] = "Brand is required";
        if (!is_numeric($data['pricePerDay'])) $errors[] = "Price must be a number";
        if (empty($data['category'])) $errors[] = "Category is required";

        return $errors;
    }

    public function getCars() {
        $query = "SELECT * FROM vehicles";
        $stmt = $this->pdo->prepare($query); 
        $stmt->execute();
    
        $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $cars; 
    }

        // ... (other methods)
    
        public function checkAvailability($vehicleId) {
            $query = "
                SELECT status 
                FROM reservations 
                WHERE vehicleId = :vehicleId 
                AND status = 'confirmed'
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":vehicleId", $vehicleId);
            $stmt->execute();
    
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result === false; // Available if no confirmed reservation
        }
    
        // ... (other methods)
    public function updateStatus($status) {
        $query = "UPDATE vehicles SET status = :status WHERE id = :id";  
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $this->id);  
        
        return $stmt->execute(); 
    }
    public function editVehicle($vehicleId, $model, $brand, $description, $pricePerDay, $status, $categoryId, $characteristics, $image) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE vehicles
                SET model = ?, brand = ?, description = ?, pricePerDay = ?, status = ?, categoryId = ?, characteristics = ?, image = ?
                WHERE id = ?
            ");

            return $stmt->execute([
                $model,
                $brand,
                $description,
                $pricePerDay,
                $status,
                $categoryId,
                $characteristics,
                $image,
                $vehicleId
            ]);
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
    public function getReviews() {
        $query = "SELECT * FROM reviews WHERE vehicle_id = :vehicle_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":vehicle_id", $this->id);  
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);  
    }

    public function getAverageRating() {
        $query = "SELECT AVG(rating) AS average_rating FROM reviews WHERE vehicle_id = :vehicle_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":vehicle_id", $this->id);  
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['average_rating']; 
    }

    public function searchByModel($model) {
        $query = "SELECT * FROM vehicles WHERE model LIKE :model";  // Changed 'vihcule' to 'vehicles'
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":model", "%$model%");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    // Function to get available vehicles considering the start and end dates
    public function getAvailableVehicles($startDate, $endDate) {
        $query = "
            SELECT * 
            FROM vehicles v
            WHERE v.status = 'available' AND NOT EXISTS (
                SELECT 1 
                FROM reservations r
                WHERE r.vehicleId = v.id
                AND (
                    (r.startDate <= :endDate AND r.endDate >= :startDate)  -- overlap check
                )
            )
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":startDate", $startDate);
        $stmt->bindParam(":endDate", $endDate);
        $stmt->execute();

        $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $vehicles;
    }
    public function paginateVehicles($page, $limit) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT * FROM vehicles LIMIT :limit OFFSET :offset";  
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }
    public function getTotalCars() {
        $query = "SELECT COUNT(*) FROM vehicles";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}













?>
