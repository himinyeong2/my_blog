<?php
$host = 'localhost';
$username = 'pre106879'; # MySQL 계정 아이디
$password = 'admy106879!'; # MySQL 계정 패스워드
$dbname = 'pre106879';  # DATABASE 이름

global $conn;

$options = array(PDO:: MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

try {

    $conn = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password);
} catch (PDOException $e) {
    die("Failed to connect to the database: ".$e -> getMessage());
}


$conn -> setAttribute(PDO:: ATTR_ERRMODE, PDO:: ERRMODE_EXCEPTION);
$conn -> setAttribute(PDO:: ATTR_DEFAULT_FETCH_MODE, PDO:: FETCH_ASSOC);

if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
    function undo_magic_quotes_gpc(&$array) {
        foreach($array as & $value) {
            if (is_array($value)) {
                undo_magic_quotes_gpc($value);
            }
            else {
                $value = stripslashes($value);
            }
        }
    }

    undo_magic_quotes_gpc($_POST);
    undo_magic_quotes_gpc($_GET);
    undo_magic_quotes_gpc($_COOKIE);
} 
?>