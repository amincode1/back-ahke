<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$category_db_name = 'ahke_comment.question_comment';
$user_table = "ahke_user.user";
if(isset($data["item_id"]) && isset($_SERVER['HTTP_REFERER'])){
    $item_id = local_filter_input($data["item_id"]);
    $type = local_filter_input($data["type"]);
    if(isset($data["page_num"])){
        $page_num = local_filter_input($data["page_num"]);
        $page_num = (int)$page_num * 10;
    }else{
    	$page_num = 0;
    }
    $get_comment_sql = "SELECT a.*,b.id as comment_user_id,b.name as comment_user_name,b.username as comment_username,b.profile_image as comment_user_profile_image
                        FROM {$category_db_name} a
                        INNER JOIN {$user_table} b on b.id = a.user_id
                        WHERE a.item_id = {$item_id} AND a.type = {$type} LIMIT {$page_num},10 ";
    $get_comment = $conn->query($get_comment_sql);
    $get_comment = $get_comment->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($get_comment);
    $get_comment = null;
}

if(isset($data["comment_id"])){
   $comment_id = local_filter_input($data["comment_id"]);
   $get_comment_sql = "SELECT * FROM {$category_db_name} WHERE id = {$comment_id} ";
   $get_comment = $conn->query($get_comment_sql);
   $get_comment = $get_comment->fetchAll(PDO::FETCH_ASSOC);
   echo json_encode($get_comment);
}
?>