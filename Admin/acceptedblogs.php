<?php
session_start();
require_once '../classes/blogsclass.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['article_id']) && isset($_POST['status'])) {
    $articleId = $_POST['article_id'];
    $status = $_POST['status'] === 'accepted' ? 1 : 0;

    $blog = new blogs();
    $result = $blog->updateArticleStatus($articleId, $status);

    if ($result) {
        $_SESSION['message'] = "Article status updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update article status.";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>