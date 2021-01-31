<?php
include 'lib.php';
include 'db/dbcon.php';
include 'session.php';

session_test();

$mode = $_GET['mode'];
if ($mode == "") {

    include 'html/nav.html';
    include 'html/category.html';

} else if ($mode == "select") {
    $category_number = $_GET['category'];

    if ($category_number != 0) {
        if (check_sub_category($category_number)) {
            $category_box = get_category($category_number, 0);
            echo $category_box;
            exit;
        }
    }
    exit;

} else if ($mode == "get") {
    $stmt = $conn->prepare("SELECT number,parent,name, value  FROM me_blog_category WHERE number=:number");
    $stmt->bindParam(':number', $_GET['number']);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $data = json_encode($result);

    echo $data;
    exit;
} else if ($mode == "add") {
    $mode2 = $_POST['mode'];
    $parent = $_POST['parent'];
    $number = $_POST['number'];
    $name = $_POST['name'];
    $value = $_POST['value'];

    if ($mode2 == "insert") {
        if ($parent == 0) { //최상위 카테고리 일때
            $sort_sql = $conn->prepare("
                SELECT IF( (

                    SELECT MAX( sort )
                    FROM `me_blog_category`) IS NULL , 1, (

                    SELECT MAX( sort )
                    FROM `me_blog_category` )+1
                    ) AS sort");

            $sort_sql->execute();
            $result = $sort_sql->fetchAll(PDO::FETCH_ASSOC);
            $sort = $result[0]['sort'];
        } else { //하위 카테고리일때
            $sort_sql = $conn->prepare("
            SELECT IF( (

                SELECT MAX( sort )
                FROM `me_blog_category`
                WHERE parent=$parent ) IS NULL , (SELECT sort FROM me_blog_category WHERE number=$parent)+1,
                (SELECT MAX( sort )
                FROM `me_blog_category`
                WHERE parent=$parent
                )+1
                ) AS sort
            ");
            $sort_sql->execute();
            $result = $sort_sql->fetchAll(PDO::FETCH_ASSOC);
            $sort = $result[0]['sort'];

            $update_sql = $conn->prepare("UPDATE me_blog_category SET sort = sort+1 WHERE sort>=$sort");
            $update_sql->execute();
        }
        $insert_sql = $conn->prepare("INSERT INTO me_blog_category
                                        SET parent=$parent,
                                            sort= $sort,
                                            name='$name',
                                            value='$value'");
        $insert_sql->execute();
        gomsg("카테고리가 성공적으로 추가되었습니다.", "?");
    } else if ($mode2 == "update") {
        $stmt = $conn->prepare("UPDATE me_blog_category SET
                                        name='$name',
                                        value='$value'
                                WHERE   number=$number");
        $stmt->execute();
        gomsg("카테고리가 성공적으로 수정되었습니다.", "?");
    }
    exit;
} else if ($mode == "del") {
    $del_stmt = $conn->prepare("DELETE FROM me_blog_category WHERE parent=$_GET[number]");
    $del_stmt->execute();
    $stmt = $conn->prepare("DELETE FROM me_blog_category WHERE number=$_GET[number]");
    $stmt->execute();

    echo "SUCCESS";
    exit;

}
function print_category()
{
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM me_blog_category ORDER BY sort");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $return = '';

    foreach ($result as $data) {
        $number = ($data['parent'] != 0) ? 'ㄴ' : 'ㅇ';
        $btns = ($data['parent'] != 0) ? '
        <a class="btn btn-secondary btn-sm"  onclick="show_form(\'mod\', ' . $data['number'] . ')">수정</a>
        <a class="btn btn-info btn-sm" onclick="del_category(' . $data['number'] . ')">삭제</a>' 
        :
         '
        <a class="btn btn-primary btn-sm" onclick="show_form(\'add\', ' . $data['number'] . ')">추가</a>
        <a class="btn btn-secondary btn-sm" onclick="show_form(\'mod\', ' . $data['number'] . ')">수정</a>
        <a class="btn btn-info btn-sm" onclick="del_category(' . $data['number'] . ')">삭제</a>';
        $return .= '
        <tr align="center">
            <td hidden></td>
            <td widtd="10">' . $number . '</td>
            <td widtd="10">' . $data['number'] . '</td>
            <td>' . $data['name'] . '</td>
            <td>' . $data['value'] . '</td>
            <td >
               ' . $btns . '
            </td>
        </tr>
        ';
    }
    return $return;
}
include 'html/footer.html';
