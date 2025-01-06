<?php
session_start();

require_once '../classes/reservationclass.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$clientId = $_SESSION['user_id'];
 $debueDate =   $name = $_POST['debueDate'];
 $returnDate =   $name = $_POST['returnDate'];    
 $address =   $name = $_POST['address'];    
 $vehicleId =   $name = $_POST['id_car'];    

$Reservation = new Reservation();
$Reservation -> createReservation($vehicleId, $debueDate, $returnDate, $address, $clientId);



}
?>