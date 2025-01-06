<?php
require_once '../classes/classvihcule.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $vehicle = new Vehicle();
        
        // Collect form data
        $modelData = isset($_POST['model']) ? $_POST['model'] : '';
        $brandData = isset($_POST['brand']) ? $_POST['brand'] : '';
        $descriptionData = isset($_POST['description']) ? $_POST['description'] : '';
        $pricePerDayData = isset($_POST['pricePerDay']) ? $_POST['pricePerDay'] : '';
        $statusData = isset($_POST['status']) ? $_POST['status'] : '';
        $categoryId = isset($_POST['categoryId']) ? $_POST['categoryId'] : ''; // Corrected category ID handling

        // Debugging: print received data
        echo "<pre>";
        print_r($_POST);
        print_r($_FILES);
        echo "</pre>";

        // Validate required fields
        if (empty($modelData) || empty($brandData) || empty($categoryId)) {
            throw new Exception("Model, brand, and category are required fields");
        }

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageData = [
                'name' => $_FILES['image']['name'],
                'type' => $_FILES['image']['type'],
                'tmp_name' => $_FILES['image']['tmp_name'],
                'error' => $_FILES['image']['error'],
                'size' => $_FILES['image']['size']
            ];
            $vehicle->uploadImage($imageData);
        } else {
            throw new Exception("Image upload error.");
        }

        // Prepare characteristics data
        $characteristicsData = json_encode([
            'model' => $modelData,
            'brand' => $brandData
        ]);

        // Get current date and time
        $currentDate = date("Y-m-d H:i:s");

        // Save vehicle data
        $result = $vehicle->save(
            $modelData,
            $brandData,
            $descriptionData,
            $pricePerDayData,
            $statusData,
            $categoryId,
            $characteristicsData,
            $vehicle->image, // This is set by uploadImage method
            $currentDate
        );

        if ($result) {
            echo "Vehicle saved successfully!";
        } else {
            echo "Error saving vehicle.";
        }

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        error_log("Vehicle Save Error: " . $e->getMessage());
    }
}
?>