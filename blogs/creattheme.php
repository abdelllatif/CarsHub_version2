<?php 
require_once '../classes/blogsclass.php';  
  
if($_SERVER['REQUEST_METHOD']=='POST'){
    $name=$_POST['name'];
    $description=$_POST['description'];
$pdo= new blogs();
  
  $pdo-> createtheme($name,$description);


}
else{
    echo"cant insert data";
}

?>
