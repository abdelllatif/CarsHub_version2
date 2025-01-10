<?php 
session_start();
require_once '../classes/commentclass.php';  
  
if($_SERVER['REQUEST_METHOD']=='POST'){
    $previousPage = $_SERVER['HTTP_REFERER'];
    $commentId=$_POST['commentID'];
    $user_id=$_SESSION['user_id'];
    $content=$_POST['content'];
    $rating=$_POST['rating'];
    var_dump($content);
$pdo= new Comment();

  $pdo-> updateComment($commentId, $user_id, $content, $rating) ;
  header("location: $previousPage");
}
else{
    echo"cant insert data";
}

?>
