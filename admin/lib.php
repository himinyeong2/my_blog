<?php
function gomsg($msg, $url){
    echo "<script>alert('$msg');</script>";
    echo "<script>location.href='$url';</script>";
}
function msg($msg){
    echo "<script>alert('$msg');</script>";
}
function go($url){
    echo "<script>location.href='$url';</script>";
}
function get_object($field1, $table, $field2, $value){
    global $conn;

    $stmt = $conn->prepare("SELECT $field1 as data FROM $table WHERE $field2 = :value ");
    $stmt -> bindParam(':value', $value);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $return = $result[0]['data'];

    return $return;

}
function upload_image($file, $options)
{

    $up_image_name = $file['name'];
    $up_image_temp = $file['tmp_name'];
    $temp_name = explode(".", $up_image_name);
    $ext = strtolower($temp_name[sizeof($temp_name) - 1]);

    // $options = array("jpg", "jpeg", "gif", "png", "PNG");

    for ($z = 0, $m = sizeof($options), $ext_check = ''; $z < $m; $z++) {
        #echo " $ext = ".$options[$z] ."<br>";
        if ($ext == trim($options[$z])) {
            $ext_check = 'ok';
            break;
        }
    }
    if ($ext_check != "ok") {
        //ERROR
        return "ERROR";

    } else {
        $time = date("Ymdhis");
        $random = rand(0, 1000000);
        $file_name = $time . "_" . $random . "." . $ext;

        $image_path = $_SERVER['DOCUMENT_ROOT']."/blog/upload/" . $file_name;
        copy($up_image_temp, $image_path);

        $image_path = "upload/".$file_name;
    }

    return $image_path;
}function get_select_box($array_value, $array_name, $value)
{

    for ($i = 0; $i < sizeof($array_value); $i++) {
        $selected = ($array_value[$i] == $value) ? "selected" : "";
        $option .= '
        <option value="' . $array_value[$i] . '" ' . $selected . '>' . $array_name[$i] . '</option>
        ';
    }
    $RETURN = '
    <select name="field" class="custom-select" style="width:100%;">
                    ' . $option . '
                </select>
    ';
    return $RETURN;
}
function get_category($parent, $number)
{

    global $conn;

    if ($parent == 0) {
        $category = "category";
    } else {
        $category = "sub_category";
    }

    $RETURN = '
    <select name="' . $category . '" class="custom-select ">
	<option value=""></option>
    ';

    $stmt = $conn->prepare("SELECT * FROM me_blog_category WHERE parent = :parent");
    $stmt->bindParam(':parent', $parent);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $data) {
        $selected = ($data['number'] == $number) ? "selected" : "";
        $RETURN .= '
        <option value="' . $data['number'] . '"  ' . $selected . '>' . $data['name'] . '</option>
    ';
    }
    $RETURN .= '</select>';
    return $RETURN;
}
function check_sub_category($parent)
{
    global $conn;

    $stmt = $conn->prepare("SELECT count(number) as cnt FROM me_blog_category WHERE parent = :parent");
    $stmt->bindParam(':parent', $parent);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($result[0]['cnt'] == 0) {
        return false;
    } else {
        return true;
    }

}
?>