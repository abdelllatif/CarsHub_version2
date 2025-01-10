<?php
session_start();
include '../classes/blogsclass.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {

    $userId = $_SESSION['user_id']; 
    $yourClassInstance = new blogs(); 
    var_dump($_FILES['profile_picture']);
    $result = $yourClassInstance->uploadImageAndSaveTopdo($_FILES['profile_picture'], $userId);

    if ($result) {
        echo "Image uploaded and saved successfully!";
        
    } else {
        echo "There was an error.";
    }
}




?>