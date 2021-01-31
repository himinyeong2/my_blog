<?php
    session_start();
    
    $login = (isset($_SESSION['login']))? $_SESSION['login'] : null;

    function session_test(){
        global $login;

        if(!isset($login)){
            gomsg("로그인 후 이용해주세요!","index.php");
        }
    }
    
?>