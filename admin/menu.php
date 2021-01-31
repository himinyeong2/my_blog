<?php
include 'lib.php';
include 'db/dbcon.php';
include 'session.php';

session_test();

$mode = $_GET['mode'];
if($mode==""){
    include 'html/nav.html';
    include 'html/menu.html';
}
else if($mode=="get"){
    $stmt = $conn->prepare("SELECT * FROM me_blog_menu WHERE number=:number");
    $stmt->bindParam(':number', $_GET['number']);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $data= json_encode($result);
    
    echo $data;
    exit;
}
else if($mode=="add"){
    $mode2 = $_POST['mode'];
    $parent = $_POST['parent'];
    $number = $_POST['number'];
    $name = $_POST['name'];
    $sort = $_POST['sort'];
    $link = $_POST['link'];

    $sql = '';
    $msg = '';

    $set_sql = "parent = $parent,
                name = '$name',
                sort = $sort,
                link = '$link'";

    if($mode2=="insert"){
        $sql = "INSERT INTO me_blog_menu SET ".$set_sql;
        $msg = "메뉴가 추가되었습니다.";
    }
    else if($mode2=="update"){
        $sql = "UPDATE me_blog_menu SET ".$set_sql." WHERE number =".$number;
        $msg = "메뉴가 수정되었습니다";
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    gomsg($msg, "?");

}
else if($mode=="del"){
    $del_stmt = $conn->prepare("DELETE FROM me_blog_menu WHERE parent=$_GET[number]");
    $del_stmt->execute();
    $stmt = $conn->prepare("DELETE FROM me_blog_menu WHERE number=$_GET[number]");
    $stmt->execute();

    echo "SUCCESS";
    exit;

}
function print_menu(){
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM me_blog_menu ORDER BY sort");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $return = '';

    foreach($result as $data){
        $number = ($data['parent']!=0)? 'ㄴ' : 'ㅇ';
        $btns = ($data['parent']!=0)? '
        <a class="btn btn-secondary btn-sm"  onclick="show_form(\'mod\', '.$data['number'].')">수정</a>
        <a class="btn btn-info btn-sm" onclick="del_category('.$data['number'].')">삭제</a>' 
        :
         ' 
        <a class="btn btn-primary btn-sm" onclick="show_form(\'add\', '.$data['number'].')">추가</a>
        <a class="btn btn-secondary btn-sm" onclick="show_form(\'mod\', '.$data['number'].')">수정</a>
        <a class="btn btn-info btn-sm" onclick="del_category('.$data['number'].')">삭제</a>';
        $return.='
        <tr align="center">
            <td hidden></td>
            <td widtd="10">'.$number.'</td>
            <td>'.$data['name'].'</td>
            <td>'.$data['sort'].'</td>
            <td >
               '.$btns.'
            </td>
        </tr>
        ';
    }
    return $return;
}
include 'html/footer.html';
?>