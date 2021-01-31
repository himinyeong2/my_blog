<?php 
include 'lib.php';
include 'db/dbcon.php';
include 'session.php';

session_test();
$mode = $_GET['mode'];

if($mode == ""){
    include 'html/nav.html';
    include 'html/comments.html';
    include 'html/footer.html';
}
else if($mode == "del"){
    $stmt = $conn->prepare("DELETE FROM me_blog_comments WHERE parent = $_GET[number]");
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM me_blog_comments WHERE number = $_GET[number]");
    $stmt->execute();

    echo "SUCCESS";
    exit;
    
}
else if($mode=="get"){
    $stmt = $conn->prepare("SELECT c.number, c.name, c.password, c.comments, b.title FROM me_blog_comments c
                            INNER JOIN me_blog_board b ON c.board_number = b.number
                            WHERE c.number=$_GET[number]");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $json_data = json_encode($result);

    echo $json_data;
    exit;
}
else if($mode=="add"){
    $number = $_POST['number'];
    $name = $_POST['name'];
    $password = $_POST['password'];
    $comments = $_POST['comments'];

    $password_sql = ($password=="")? "" : ", password = '$password'";

    $sql = "UPDATE me_blog_comments 
            SET     name='$name',
                    comments = '$comments',
                    mod_date = now()
                    $password_sql
            WHERE   number = $number";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    gomsg("수정이 완료되었습니다","?");
}
function print_comments()
{
    global $conn;
    $stmt = $conn->prepare("SELECT c.number, c.name, c.comments, c.reg_date, c.parent,  b.title,b.number as b_number  FROM me_blog_comments c 
                            INNER JOIN me_blog_board b ON c.board_number = b.number
                            ORDER BY board_number, sort asc");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $cnt = sizeof($result);
    $table_data = '';

    foreach ($result as $data) {
        $reply = ($data['parent']!=0)? "ㄴ " : "";
        $table_data .= '
        <tr align="center">
            <td hidden></td>
            <td widtd="10">'.$cnt--.'</td>
            <td><a href="/blog/board.php?mode=show&number='.$data['b_number'].'">'.$data['title'].'</a></td>
            <td>'.$data['name'].'</td>
            <td>'.$reply.$data['comments'].'</td>
            <td>'.$data['reg_date'].'</td>
            <td>
                <a onclick="edit_comment('.$data['number'].')" class="btn btn-primary btn-sm">수정</a>
                <a onclick="del_comment('.$data['number'].')" class="btn btn-secondary btn-sm">삭제</a>
            </td>
        </tr>
        ';
    }
    return $table_data;
}

?>

