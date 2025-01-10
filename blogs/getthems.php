<?php 
require_once '../classes/blogsclass.php';  
  
 
$pdo= new blogs();
  
$themes=$pdo-> gettheme();  
$tags=$pdo-> gettheme();
?>
