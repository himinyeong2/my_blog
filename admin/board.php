<?php
include 'lib.php';
include 'db/dbcon.php';
include 'session.php';

session_test();
$mode = $_GET['mode'];


if ($mode == "") {
    include 'html/nav.html';
    include 'html/board_index.html';

}
else if($mode=="mod"){
    $stmt = $conn->prepare("UPDATE me_blog_board SET display=:display WHERE number= :number");
    $stmt->bindParam(':display',$_GET['value']);
    $stmt->bindParam(':number', $_GET['number']);
    $stmt->execute();
    
    echo "SUCCESS";
    exit;
}

else if($mode=="del"){
    $stmt = $conn->prepare("DELETE FROM me_blog_board WHERE number= :number");
    $stmt->bindParam(':number', $_GET['number']);
    $stmt->execute();
    
    echo "SUCCESS";
    exit;
}
else if($mode=="add"){
    $category_box = get_category(0,0);
    $number = $_GET['number'];
    $page_name = ($number=="")? "Add" : "Edit";

    if($number!=''){
        $stmt = $conn->prepare("SELECT * FROM me_blog_board WHERE number = :number");
        $stmt->bindParam(':number',$number);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $title = $result[0]['title'];
        $sub_title = $result[0]['sub_title'];
        $contents = $result[0]['contents'];
        $category_box = get_category(0,$result[0]['category']);

        if($result[0]['sub_category']!='')
            $category_box.= get_category($result[0]['category'],$result[0]['sub_category']);

        if($result[0]['main_image']!=''){
            $main_image='
            <div class="form-group ">
                                    <div class ="col-md-4 px-0"  style="border:1px solid #666; ">
                                        <img style="width:100%; height:100%; vertical-align: middle;" src="../'.$result[0]['main_image'].'">
                                    </div>
                                    <div class="col-md-12">
                                        <span><input type="checkbox" name="del_btn" value="clicked">&nbsp;삭제</span>
                                    </div>
                                </div>
            ';
        }

        $hashtag= $result[0]['tag'];
    }
    $page_path = ($number=="")? "Add Page" : "<a href='board.php'>Board Management</a>&nbsp;/&nbsp;".$title;
    include 'html/nav.html';
    include 'html/board_form.html';
}
else if($mode=="reg"){

    $number = $_GET['number'];
    if($number!=''){
        $img_sql = $conn->prepare("SELECT main_image,category FROM me_blog_board WHERE number = $number");
        $img_sql->execute();
        $result = $img_sql->fetchAll(PDO::FETCH_ASSOC);
        $main_image = $result[0]['main_image'];

        if($_POST['del_btn']=="clicked"){//이미지 삭제 버튼을 클릭했을 경우
            unlink($main_image);
            $main_image = '';
        }
        if($_FILES['main_image']['name']!='' ){//이미지를 업로드 했을 경우
            if($main_image!='' && file_exists($main_image)){//이미지 업로드하는데 기존에 이미지가 존재할 경우
                unlink($main_image);//기존의 이미지 삭제
            }
            $main_image = upload_image($_FILES['main_image'],array("jpg", "jpeg", "gif", "png", "PNG"));
        }
        
        try {
            $stmt = $conn->prepare('UPDATE me_blog_board
                                    SET title = :title,
                                        sub_title = :sub_title,
                                        contents = :contents,
                                        category = :category,
                                        sub_category = :sub_category,
                                        main_image = :main_image,
                                        mod_date = now(),
                                        tag = :tag
                                    WHERE number = :number
            ');
    
            $stmt->bindParam(':title', $_POST['title']);
            $stmt->bindParam(':sub_title', $_POST['sub_title']);
            $stmt->bindParam(':contents', $_POST['contents']);
            $stmt->bindParam(':category', $_POST['category']);
            $stmt->bindParam(':sub_category', $_POST['sub_category']);
            $stmt->bindParam(':main_image', $main_image);
            $stmt->bindParam(':tag', $_POST['hashtag']);
            $stmt->bindParam(':number',$number);
    
            if ($stmt->execute()) {
                // echo "SUCCESS";
            } else {
                $errMSG = "사용자 추가 에러";
            }
    
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
        gomsg("수정이 완료되었습니다!","?");

         
    }
    else{
        
        // INSERT
        if ($_FILES['main_image']['name'] != '') {
            $image_path = upload_image($_FILES['main_image'],array("jpg", "jpeg", "gif", "png", "PNG"));
        }
        try {
            $stmt = $conn->prepare('INSERT INTO me_blog_board
                                    SET title = :title,
                                        sub_title = :sub_title,
                                        contents = :contents,
                                        category = :category,
                                        sub_category = :sub_category,
                                        main_image = :main_image,
                                        mod_date = now(),
                                        reg_date = now(),
                                        tag = :tag
            ');
    
            $stmt->bindParam(':title', $_POST['title']);
            $stmt->bindParam(':sub_title', $_POST['sub_title']);
            $stmt->bindParam(':contents', $_POST['contents']);
            $stmt->bindParam(':category', $_POST['category']);
            $stmt->bindParam(':sub_category', $_POST['sub_category']);
            $stmt->bindParam(':main_image', $image_path);
            $stmt->bindParam(':tag', $_POST['hashtag']);
    
            if ($stmt->execute()) {
                // echo "SUCCESS";
            } else {
                $errMSG = "사용자 추가 에러";
            }
    
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
        gomsg("입력이 완료되었습니다!","?");
    }
    
}
function print_table()
{
    global $conn;
    $stmt = $conn->prepare("SELECT b.number, b.title, c.name,b.display, b.tag, b.view, b.reg_date ,c2.name as sub_category
                            FROM me_blog_board b
                            LEFT OUTER JOIN me_blog_category c ON b.category = c.number
                            LEFT OUTER JOIN me_blog_category c2 ON b.sub_category = c2.number
                            ORDER BY b.reg_date desc");
    $stmt->bindParam(':number', $number);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $cnt = sizeof($result);
    $table_data = '';

    foreach ($result as $data) {
        $date = substr($data['reg_date'], 0, 10);
        $tag = (mb_strlen($data['tag'], 'utf-8') >= 15) ? substr($data['tag'], 0, 15) . "..." : $data['tag'];
        $y_selected = ($data['display']=="Y")? "selected" : "";
        $n_selected = ($data['display']=="N")? "selected" : "";
        $table_data .= '
        <tr style="height:30px" align="center">
            <td hidden></td>
            <td>' . $cnt-- . '</td>
             <td> <a href="/blog/board.php?mode=show&number='.$data['number'].'">' . $data['title'] . '</a></td>
            <td>' . $data['name'] . '</td>
            <td>' . $data['sub_category'] . '</td>
            <td>' . $data['view'] . '</td>
            <td>' . $date . '</td>
            <td>
            <span hidden>'.$data['display'].'</span>
                <select class="display_s col-md-9" id="display_'.$data['number'].'">
                    <option value="Y" '.$y_selected.'>Y</option>
                    <option value="N" '.$n_selected.'>N</option>
                </select>
            </td>
            <td>
                <a href="board.php?mode=add&number='.$data['number'].'" class="btn btn-primary btn-sm">수정</a>
                <a onclick="del_board('.$data['number'].')" class="btn btn-secondary btn-sm">삭제</a>
            </td>
         </tr>
        ';
    }
    return $table_data;
}
include 'html/footer.html';
