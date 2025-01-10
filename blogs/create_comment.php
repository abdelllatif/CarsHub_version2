<?php 
session_start();
require_once '../classes/commentclass.php';  
  
if($_SERVER['REQUEST_METHOD']=='POST'){
    $previousPage = $_SERVER['HTTP_REFERER'];
    $article_id=$_POST['article_id'];
    $user_id=$_SESSION['user_id'];
    $content=$_POST['content'];
    $rating=$_POST['rating'];
    var_dump($rating);
$pdo= new Comment();

  $pdo-> insertComment($article_id, $user_id, $content, $rating);
  header("location: $previousPage");
}
else{
    echo"cant insert data";
}

?>
