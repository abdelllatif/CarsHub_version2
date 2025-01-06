<?php
require_once '../classes/userclasse.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    $email = $_POST['email2'];  
    $password =$_POST['password2']; 

    $user = new User();
    $user->login($email, $password);

}
?>
