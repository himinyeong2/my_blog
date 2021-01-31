<?php
include_once "category.php";
include_once "lib/lib.php";

$mode = $_GET['mode'];
$category = $_GET['category_number'];
$sub_category = $_GET['sub_category'];
$field = $_GET['field'];
$number = $_GET['number'];

include_once "html/header.html";

if ($mode == "list") {
    
    $select_box = get_select_box(array("", "title","contents","tag"),array("all field","제목","내용","태그"),$field);
    if ($category == "1") {
        $title = "PROJECT";
        // $category_box = get_category(1,0);
        $board_list = project_list(null);

    }
    else{
        $title=get_object("me_blog_category", "number", $category, "name");
        $category_box = get_category($category,$sub_category);
        $board_list = board_list(null,$category);
    } 
    include 'html/board_list.html';

} else if ($mode == 'add') {

    $category_box = get_category(0,0);

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
                                    <div class ="col-md-6 px-0"  style="border:1px solid #666; ">
                                        <img style="width:100%; height:100%; vertical-align: middle;" src="'.$result[0]['main_image'].'">
                                    </div>
                                    <div class="col-md-12">
                                        <span><input type="checkbox" name="del_btn" value="clicked">&nbsp;삭제</span>
                                    </div>
                                </div>
            ';
        }

        $tag= $result[0]['tag'];
        $tag_array=explode(",",$tag);
        
        if($result[0]['tag']!=''){
            for($i=0;$i<sizeof($tag_array);$i++){
                $hashtag .= '<span class="badge badge-pill badge-light">'.$tag_array[$i].'</span>';
                // $hashtag.="#".$tag_array[$i]." ";
            }
        }
        
    
    }
    include 'html/board_form.html';

} 
else if ($mode == "reg") {

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
                echo "SUCCESS";
            } else {
                $errMSG = "사용자 추가 에러";
            }
    
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
        echo "<script>alert('수정이 완료되었습니다.');</script>";
        echo "<script>location.href='board.php?mode=show&number=$number'</script>";

         
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
                echo "SUCCESS";
            } else {
                $errMSG = "사용자 추가 에러";
            }
    
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
        echo "<script>alert('입력이 완료되었습니다.');</script>";
        echo "<script>location.href='board.php?mode=list&category_number=$_POST[category]'</script>";
    }
    

}
else if($mode == "show" && $number!=''){

    if($number!=''){
    
        $view_stmt = $conn->prepare("UPDATE me_blog_board SET view = view + 1 WHERE number = $number");
        $view_stmt->execute();

        $stmt = $conn->prepare("SELECT b.number,b.contents, b.title, b.sub_title, b.tag, b.reg_date, c.value, b.main_image FROM me_blog_board b INNER JOIN me_blog_category c ON b.category = c.number WHERE b.number = :number");
        $stmt->bindParam(':number',$number);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


        $title = $result[0]['title'];
        $sub_title = $result[0]['sub_title'];
        $tag_array= explode("," ,$result[0]['tag']);
        $contents= $result[0]['contents'];
        $date= explode(" ",$result[0]['reg_date']);
        $category= $result[0]['value'];
        $main_image= ($result[0]['main_image']=="")? "assets/image/no_image.jpg" : $result[0]['main_image'];
        $comments_list = comments_list($number);

        if($result[0]['tag']!=''){
            for($i=0;$i<sizeof($tag_array);$i++){
                $tag.='<span class="badge badge-primary">'.$tag_array[$i].'</span> ';
                // $tag.="#".$tag_array[$i]." ";
            }
        }
    }
    include 'html/board_detail.html';
}
else if($mode=="del"){
    $img_sql = $conn->prepare("SELECT category, main_image FROM me_blog_board WHERE number=$number");
    $img_sql->execute();
    $result = $img_sql->fetchAll(PDO::FETCH_ASSOC);
    $category = $result[0]['category'];

    if($result[0]['main_image']!=''){
        if(file_exists('main_image')){
            unlink('main_image');
        }
    }
    $del_sql = $conn->prepare("DELETE FROM me_blog_board WHERE number=$number");
    $del_sql->execute();
    
    gomsg("삭제가 완료되었습니다","board.php?mode=list&category_number=$category");
    exit;
}

?>

<?php
include "html/footer.html";
?>