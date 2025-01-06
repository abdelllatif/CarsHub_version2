<?php 
// Adminedit.php
require '../classes/classvihcule.php';

$vehicle = new Vehicle();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vehicle_id'])) {
    $vehicleId = $_POST['vehicle_id'];
    $model = $_POST['model'];
    $brand = $_POST['brand'];
    $description = $_POST['description'];
    $pricePerDay = $_POST['pricePerDay'];
    $status = $_POST['status'];
    $categoryId = $_POST['categoryId'];
    $characteristics = $_POST['characteristics'];
    $image = $_POST['image'];

    if ($vehicle->editVehicle($vehicleId, $model, $brand, $description, $pricePerDay, $status, $categoryId, $characteristics, $image)) {
        echo "Vehicle updated successfully.";
    } else {
        echo "Error updating vehicle.";
    }
}
?>