<?php
include_once "lib/lib.php";

$mode = $_GET['mode'];
$category = $_GET['category'];
$number = $_GET['number'];


if($mode="show_contents" && $number!=''){
    $stmt = $conn->prepare("SELECT contents FROM me_blog_board  WHERE number = :number");
    $stmt->bindParam(':number',$number);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $contents = $result[0]['contents'];

    echo '
    <link rel="stylesheet" href="assets/css/minyeong.css" />'.$contents;

}
?>