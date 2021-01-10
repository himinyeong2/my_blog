<?php

    $mode = $_GET['mode'];
    include_once "html/header.html";
    include_once "lib/lib.php";

    $mode = $_GET['mode'];
    if($mode==''){
        
        $projects=project_list("index");
        $studies=study_list("index");
        $algorithms = algorithm_list("index");

        include_once 'html/blog_index.html';
    }
    
?>

<?php
    include_once "html/footer.html";
?>