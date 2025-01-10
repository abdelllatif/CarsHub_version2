<?php 
session_start();
require_once '../classes/commentclass.php';  
  
if($_SERVER['REQUEST_METHOD']=='POST'){
  
$deletd= new Comment();
$previousPage = $_SERVER['HTTP_REFERER'];
$userId = $_SESSION['user_id'];
$commentId = $_POST['comment_id'];
$deleted = $deletd->deleteComment($commentId, $userId);
  header("location: $previousPage");
}
else{
    echo"cant insert data";
}

?>
