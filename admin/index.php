<?php 
include 'lib.php';
include 'db/dbcon.php';
include 'session.php';

if($login!=null){
    include 'html/nav.html';
    include 'html/index.html';
    include 'html/footer.html';
}else{
    go('login.php');
}


?>

