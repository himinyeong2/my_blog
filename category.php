<?php

include_once 'lib/lib.php';


$category_number = $_GET['category'];

if($category_number!=0){
    if(check_sub_category($category_number)){
        $category_box = get_category($category_number,0);
        echo $category_box;
        exit;
    }
    
}

?>