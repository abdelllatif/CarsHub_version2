<?php 
class data{
protected $db = "mysql:host=localhost;dbname=carshub";
protected $username = "root";
protected $password = "";
public $pdo;
public function connextion(){
    try{
    $this->pdo = new PDO($this -> db,$this ->username,$this ->password);
    return $this ->pdo;
}
catch(PDOException $e){
    echo "there is error". $e->getMessage();
}
}
}
