<?php 
include 'db/dbcon.php';
include 'lib.php';
include 'session.php';

$mode = $_GET['mode'];

if($mode == ""){
  include 'html/login.html';
}
else if($mode == "login"){
  $id = $_POST['id'];
  $pw = base64_encode(hash('sha256',$_POST['password'],true));

  $stmt = $conn->prepare("SELECT name  FROM me_blog_config WHERE id='$id' and password='$pw'");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $count = $stmt->rowCount();

  if($count==1){
    $_SESSION['login'] = $result[0]['name'];
    go("/blog/admin/index.php");
  }
  else{
    gomsg("아이디 및 패스워드를 다시 확인해주세요!","?");
  }
}
else if($mode=="logout"){
  session_destroy();
  gomsg("로그아웃 되었습니다","index.php");
}


?>