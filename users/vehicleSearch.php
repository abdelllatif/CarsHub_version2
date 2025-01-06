<?php
// vehicleSearch.php

// Include necessary files
require_once '../classes/classvihcule.php'; // Vehicle class with search and filter methods

// Get the incoming data (search term and category filter)
$inputData = json_decode(file_get_contents('php://input'), true);
$searchTerm = isset($inputData['search']) ? $inputData['search'] : '';
$categoryId = isset($inputData['category']) ? $inputData['category'] : '';

// Create a new instance of the Vehicle class
$vehicle = new Vehicle();

// Get filtered vehicles based on search term and category
$vehicles = $vehicle->searchAndFilterVehicles($searchTerm, $categoryId);

// Prepare the response
$response = [
    'vehicles' => $vehicles
];

// Send the JSON response back to the client
header('Content-Type: application/json');
echo json_encode($response);
