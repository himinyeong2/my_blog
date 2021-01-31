<?php
include_once "lib/lib.php";
include_once "db/dbcon.php";

$mode = $_GET['mode'];

if($mode==""){
    $projects = project_list("index");
    $studies = board_list("index", 2);
    $algorithms = board_list("index", 3);
    
    include_once "html/header.html";
    include_once 'html/blog_index.html';
    include_once "html/footer.html";
}





?>
