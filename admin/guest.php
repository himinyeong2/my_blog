<?php 
include 'lib.php';
include 'db/dbcon.php';
include 'session.php';

session_test();
$mode = $_GET['mode'];

if($mode == ""){
    include 'html/nav.html';
    include 'html/guest.html';
    include 'html/footer.html';
}
else if($mode == "del"){
    $stmt = $conn->prepare("DELETE FROM me_blog_guest WHERE number = $_GET[number]");
    $stmt->execute();

    echo "SUCCESS";
    exit;
    
}
else if($mode=="get"){
    $stmt = $conn->prepare("SELECT * FROM me_blog_guest WHERE number=$_GET[number]");
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
    $contents = $_POST['contents'];

    $password_sql = ($password=="")? "" : ", password = '$password'";

    $sql = "UPDATE me_blog_guest 
            SET     name='$name',
                    contents = '$contents',
                    mod_date = now()
                    $password_sql
            WHERE   number = $number";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    gomsg("수정이 완료되었습니다","?");
}
function print_guest()
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM me_blog_guest ORDER BY reg_date");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $cnt = sizeof($result);
    $table_data = '';

    foreach ($result as $data) {
        $table_data .= '
        <tr align="center">
            <td hidden></td>
            <td widtd="10">'.$cnt--.'</td>
            <td>'.$data['name'].'</td>
            <td>'.$data['contents'].'</td>
            <td>'.$data['reg_date'].'</td>
            <td>
                <a onclick="edit_guest('.$data['number'].')" class="btn btn-primary btn-sm">수정</a>
                <a onclick="del_guest('.$data['number'].')" class="btn btn-secondary btn-sm">삭제</a>
            </td>
        </tr>
        ';
    }
    return $table_data;
}

?>

