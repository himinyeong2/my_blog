<?php 
include 'lib.php';
include 'db/dbcon.php';
include 'session.php';

session_test();
$mode = $_GET['mode'];
// $password = base64_encode(hash('sha256',$_POST['password'],true));
if($mode==""){
    $stmt = $conn->prepare("SELECT *,count(*) as cnt FROM me_blog_config");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $name = $result[0]['name'];
    $id = $result[0]['id'];
    $code = $result[0]['code'];
    $img = $result[0]['img'];

    if($result[0]['cnt']==0)
        $mode2 = "insert";
    else
        $mode2 = "update";

    $img_div=($img!='')?'
        <div class="form-group">
                           <div class="card px-0 col-md-3">
                               <img src="../'.$img.'" style="width:100%; ">
                           </div>
                           <small class="form-text text-muted"><input type="checkbox" name="del" value="ok">삭제를 원할 시 클릭 해주세요</small>
                        </div>
        ':
        ' <div class="form-group">
        <div class="card px-0 col-md-3">
            <img src="/blog/assets/image/no_image.jpg" style="width:100%; ">
        </div>
     </div>';
    
    include 'html/nav.html';
    include 'html/config.html';
    include 'html/footer.html';
}
else if($mode=="add"){
    $del_ok = $_POST['del'];
    $name = $_POST['name'];
    $id=$_POST['id'];
    $password = ($_POST['password']!='')? base64_encode(hash('sha256',$_POST['password'],true)) :"";
    $code = $_POST['code'];
    $img_path = $_POST['img_path'];
    $mode2 = $_POST['mode2'];

    if($del_ok=="ok"){
        unlink($_SERVER['DOCUMENT_ROOT']."/blog/".$img_path);
        $stmt= $conn->prepare("UPDATE me_blog_config SET img='' WHERE number=1");
        $stmt->execute();
     }

    $set_sql = "
                name='$name',
                id='$id',
                code='$code',
                mod_date=now()
    ";

    if($_FILES['img']['name']!=''){
        $old_img = $img_path;
        $img_path = upload_image($_FILES['img'],array("jpg","jpeg","gif","png","PNG"));
        if($img_path=="ERROR"){
            gomsg("확장자를 확인해주세요!","?");
            exit;
        }else{
            $set_sql.= ",img = '$img_path' ";
            if($old_img!='')
                unlink($_SERVER['DOCUMENT_ROOT']."/blog/".$old_img);
        }
    
       
    }

    if($password!=""){
        $set_sql .=", password='$password'";
    }

    if($mode2=="insert"){
        $sql = "INSERT me_blog_config SET".$set_sql;
    }
    else{
        $sql = "UPDATE me_blog_config SET ".$set_sql." WHERE number=1";
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    gomsg("설정이 저장되었습니다.","?");

}
?>

