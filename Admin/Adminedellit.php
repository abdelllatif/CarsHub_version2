<?php // Adminedellit.php
require '../classes/classvihcule.php';

$vehicle = new Vehicle();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vehicle_id'])) {
    $vehicleId = $_POST['vehicle_id'];
    header('Location:dasheboredAdmin.php');
        if ($vehicle->deleteVehicle($vehicleId)) {
        echo "Vehicle deleted successfully.";
    } else {
        echo "Error deleting vehicle.";
    }
}

?>