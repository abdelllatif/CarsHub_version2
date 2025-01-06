<?php
require_once '../classes/connectiondatabase.php';

$pdo = new data();

$pdo->connextion();  

$sth = $pdo->pdo->prepare('SELECT count(*) as total FROM clients'); 
$str = $pdo->pdo->prepare('SELECT count(*) as total FROM vehicles'); 
$stt = $pdo->pdo->prepare('SELECT count(*) as total FROM reservations'); 
$stf = $pdo->pdo->prepare('SELECT count(*) as total FROM reviews'); 

$sth->execute();
$str->execute();
$stt->execute();
$stf->execute();

$total1 = $sth->fetchColumn();
$total2 = $str->fetchColumn();
$total3 = $stt->fetchColumn();
$total4 = $stf->fetchColumn();





$categoris = $pdo->pdo->prepare('SELECT *  FROM categories'); 
$categoris->execute();
$categories=$categoris->fetchAll(PDO::FETCH_ASSOC);

?>





