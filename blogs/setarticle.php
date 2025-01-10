<?php 
session_start();
require_once '../classes/blogsclass.php';

if (isset($_POST['title'], $_POST['theme'])) {
    $user_id = $_SESSION['user_id'];
    $theme_id = $_POST['theme'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $media = $_FILES['image'];
    $tags = $_POST['tags']; 

    if (empty($media['name'])) {
        echo "The image cannot be empty.";
        exit();
    }

    $blog = new blogs();

    $article_id = $blog->insertarticle($user_id, $theme_id, $title, $content, $media);
    if (!$article_id) {
        echo "Error adding article.";
        exit();
    }

    foreach ($tags as $tagName) {
        $tagName = trim($tagName);
        if (!empty($tagName)) {
            $tag_id = $blog->createtags($tagName); 
            if ($tag_id) {
                $blog->insertArticleTag($article_id, $tag_id); 
            }
        }
    }

    echo "Article and tags added successfully.";
}
