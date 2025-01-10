<?php
session_start();
require_once '../classes/blogsclass.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['theme_id']) && isset($_POST['name']) && isset($_POST['description'])) {
    $themeId = $_POST['theme_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];

    $blog = new blogs();
    $result = $blog->updateTheme($themeId, $name, $description);

    if ($result) {
        $_SESSION['message'] = "Theme updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update theme.";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>