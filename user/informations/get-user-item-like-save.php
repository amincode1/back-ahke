<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$user_table = "ahke_user.user";
$like_table = "";
$save_table = "";
if(isset($data['unico_id']) && $HTTP_REFERER){
    $unico_id = local_filter_input($data["unico_id"]);
    $category_name = local_filter_input($data["category_name"]);
    $type = local_filter_input($data["type"]);
    $like_table = "ahke_like.{$category_name}_like";
    $save_table = "ahke_save.{$category_name}_save";
    // get user id from unico id
    $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
    $get_user_id = $conn->query($get_user_id_sql);
    $get_user_id = $get_user_id->fetch();
    $user_id = $get_user_id["id"];
    
    $get_user_item_like_sql = "SELECT a.items_id_list as {$category_name}_user_like , b.posts_id_list as {$category_name}_user_save
                               FROM {$like_table} a 
                               INNER JOIN {$save_table} b on a.user_id = b.user_id
                               WHERE a.user_id = {$user_id} AND a.type = {$type} ";
    $get_user_item_like = $conn->query($get_user_item_like_sql);
    $get_user_item_like = $get_user_item_like->fetchAll(PDO::FETCH_ASSOC);
    $user_item_like = $get_user_item_like;
    echo json_encode($user_item_like);
}
?>