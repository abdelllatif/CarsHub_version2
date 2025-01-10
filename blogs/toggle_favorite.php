<?php
session_start();
require_once '../classes/blogsclass.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['article_id'])) {
    header('Location: ../login.php'); 
    exit;
}

$blogs = new Blogs();
$userId = $_SESSION['user_id'];
$articleId = (int)$_POST['article_id'];

try {
    $isFavorited = $blogs->toggleFavorite($userId, $articleId);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
} catch (Exception $e) {
    header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=' . urlencode($e->getMessage()));
    exit;
}
?>