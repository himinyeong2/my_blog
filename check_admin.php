<?php
include 'db/dbcon.php';

$stmt = $conn->prepare("SELECT code FROM me_blog_config WHERE number=1");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);


$mode = $_GET['mode'];
$password = $_POST['password'];
$admin_password = $result[0]['code'];


if($admin_password == $password){
    echo "SUCCESS";
}else{
    echo "ERROR";
}

?>