<?php
require_once '../classes/categorieclass.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    $name = $_POST['name'];  
    $description =$_POST['description']; 


    $user = new Categorie();
    $user->Categories($name,$description);
}
?>
