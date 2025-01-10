<?php
$pdo = new PDO("mysql:host=localhost;dbname=productes", "root", "");

$query = $_POST['query'] ?? '';

$sql = "SELECT title FROM title WHERE name LIKE :query LIMIT 6";
$stmt = $pdo->prepare($sql);
$stmt->execute(['query' => "%$query%"]);
$suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($suggestions) {
    foreach ($suggestions as $suggestion) {
        echo "<div class='suggestion-item p-2 cursor-pointer hover:bg-gray-200'>{$suggestion['name']}</div>";
    }
} else {
    echo "<p class='p-2 text-gray-500'>Aucune suggestion trouvée.</p>";
}
?>
