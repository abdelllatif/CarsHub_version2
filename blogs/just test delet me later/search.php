<?php
$pdo = new PDO("mysql:host=localhost;dbname=carshub", "root", "");

$query = $_POST['query'] ?? '';
$name = $_POST['name'] ?? '';

$sql = "SELECT * FROM article ";
$params = [];

if (!empty($query)) {
    $sql .= " AND (name LIKE :query OR description LIKE :query)";
    $params['query'] = "%$query%";
}

if (!empty($name)) {
    $sql .= " AND name = :name";
    $params['tag'] = $name;
}


$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);



$nameQuery = "SELECT DISTINCT name FROM articles ORDER BY title";
$nameStmt = $pdo->query($nameQuery);
$names = $nameStmt->fetchAll(PDO::FETCH_ASSOC);


?>
