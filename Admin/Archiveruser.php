<?php
session_start();
require_once '../classes/userclasse.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clientId'])) {
    $clientId = $_POST['clientId'];
    $client = new user();
    $result = $client->archiveClient($clientId);
    
    if ($result) {
        header('Location: dasheboredAdmin.php');
        exit;
    } else {
        echo "Failed to archive client.";
    }
} else {
    header('Location: manage_users.php');
    exit;
}
?>