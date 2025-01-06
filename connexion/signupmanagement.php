<?php
require_once '../classes/userclasse.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = filter_var($_POST['email2']);  
    $password = password_hash($_POST['password2'], PASSWORD_DEFAULT); 
    $phone = $_POST['phone'];
    $role = 'user'; 

    $user = new User();
    $user->register($firstName, $lastName, $email, $password, $phone); 
 
    echo '<br>';
    var_dump($user);
    echo '<br>';
}

?>
