<?php
require_once '../classes/categorieclass.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    $reservationId = $_POST['reservationId'];  
    $status = $_POST['status'];  


    $user = new Categorie();
    $user->updateStatus($reservationId, $status);
}
?>