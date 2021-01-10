<?php

include_once 'lib/lib.php';
global $conn;

$mode = $_GET['mode'];
$board_number = $_POST['board_number'];
$name = $_POST['name'];
$password = $_POST['password'];
$comments = $_POST['comments'];
$parent = $_POST['parent'];
$number = ($_POST['number']!='')? $_POST['number'] : $_GET['number'];

if($mode == "reg"){
    $sort_sql = $conn->prepare("
            SELECT IF( (

                SELECT MAX( sort )
                FROM `me_blog_comments`
                WHERE board_number=$board_number ) IS NULL , 1, (
                
                SELECT MAX( sort )
                FROM `me_blog_comments`
                WHERE board_number=$board_number
                )+1
                ) AS sort");
    
    $sort_sql->execute();
    $result = $sort_sql->fetchAll(PDO::FETCH_ASSOC);
    $sort = $result[0]['sort'];

    $sql = $conn->prepare(
        "INSERT INTO me_blog_comments 
        SET     board_number = $board_number,
                name = '$name',
                password = '$password',
                comments = '$comments',
                parent = $parent,
                sort = $sort,
                reg_date = now(),
                mod_date = now()
                "
    );
    $sql->execute();

    gomsg("댓글이 등록되었습니다","board.php?mode=show&number=".$board_number);
    
}
else if($mode=="reg_reply"){
    $sort_sql = $conn->prepare("
        SELECT IF( (

            SELECT MAX( sort )
            FROM `me_blog_comments`
            WHERE parent=$parent ) IS NULL , (SELECT sort FROM me_blog_comments WHERE number=$parent)+1, 
            (SELECT MAX( sort )
            FROM `me_blog_comments`
            WHERE parent=$parent
            )+1
            ) AS sort
        ");
        $sort_sql->execute();
        $result = $sort_sql->fetchAll(PDO::FETCH_ASSOC);
        $sort = $result[0]['sort'];

        $update_sql = $conn->prepare("UPDATE me_blog_comments SET sort = sort+1 WHERE sort>=$sort and board_number=$board_number");
        $update_sql->execute();

        $insert_sql = $conn->prepare("INSERT INTO me_blog_comments 
        SET     board_number = $board_number,
                name = '$name',
                password = '$password',
                comments = '$comments',
                parent = $parent,
                sort = $sort,
                reg_date = now(),
                mod_date = now()
                    ");
        $insert_sql->execute();

        gomsg("댓글이 등록되었습니다","board.php?mode=show&number=".$board_number);
}
else if($mode=="mod"){
    $update_sql = $conn->prepare("UPDATE me_blog_comments SET comments = '$comments', mod_date=now() WHERE number=$number");
    $update_sql->execute();

    gomsg("댓글이 수정되었습니다","board.php?mode=show&number=".$board_number);
    exit;
}
else if($mode=="check_password"){
    
    $sql = $conn->prepare("SELECT password FROM me_blog_comments WHERE number = $number");
    $sql->execute();
    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    $board_password = $result[0]['password'];

    if($password == $board_password) echo "SAME";
    else echo "DISCORD";

    exit;

}
else if($mode=="del" ){
    $check_sql = $conn->prepare("SELECT count(number) as cnt FROM me_blog_comments WHERE parent = $number");
    $check_sql->execute();
    $result = $check_sql->fetchALL(PDO::FETCH_ASSOC);
    if($result[0]['cnt']!=0){
        echo "답글이 달린 댓글은 삭제가 불가합니다.";
        exit;
    }else{
        $del_sql = $conn->prepare("DELETE FROM me_blog_comments WHERE number = $number");
        $del_sql->execute();
        echo "SUCCESS";
        exit;
    }
} 
?>