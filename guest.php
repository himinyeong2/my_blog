<?php

include_once 'lib/lib.php';

$number = ($_GET['number']!='')? $_GET['number'] : $_POST['number'];
$mode = $_GET['mode'];
if($mode == ''){
    include_once 'html/header.html';

    $guest_list = guest_list();
    include 'html/guest.html';
}
else if($mode=="reg"){
    $stmt = $conn->prepare("INSERT INTO me_blog_guest 
                            SET         name=:name,
                                        password=:password,
                                        contents=:contents,
                                        mod_date =now(),
                                        reg_date=now()");
    $stmt->bindParam(':name',$_POST['name']);
    $stmt->bindParam(':password',$_POST['password']);
    $stmt->bindParam(':contents',$_POST['contents']);

    $stmt->execute();

    gomsg("방명록이 등록되었습니다", "?mode=");
}
else if($mode=="check_password"){
    
    $sql = $conn->prepare("SELECT password FROM me_blog_guest WHERE number = $number");
    $sql->execute();
    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    $board_password = $result[0]['password'];

    if($_POST['password'] == $board_password) echo "SAME";
    else echo "DISCORD";

    exit;

}
else if($mode=="del" ){

    $del_sql = $conn->prepare("DELETE FROM me_blog_guest WHERE number = $number");
    $del_sql->execute();
    echo "SUCCESS";
    exit;
} 
else if($mode=="mod"){
    $stmt = $conn->prepare("UPDATE me_blog_guest 
            SET     contents=:contents,
                    mod_date =now()
            where   number=:number");
    $stmt->bindParam(':contents',$_POST['contents']);
    $stmt->bindParam(':number',$number);

    $stmt->execute();

    gomsg("방명록이 수정되었습니다", "?mode=");
    exit;
}
include 'html/footer.html';
?>