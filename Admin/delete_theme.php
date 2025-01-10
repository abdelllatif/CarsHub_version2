<?php
session_start();
require_once '../classes/blogsclass.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['theme_id'])) {
    $themeId = $_POST['theme_id'];

    $blog = new blogs();
    $result = $blog->deleteTheme($themeId);

    if ($result) {
        $_SESSION['message'] = "Theme deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete theme.";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>