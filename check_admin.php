<?php

$mode = $_GET['mode'];
$admin_password = "MINYEONG";
$password = $_POST['password'];


if($admin_password == $password){
    echo "SUCCESS";
}else{
    echo "ERROR";
}

?>