<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$question_comment_table = 'ahke_comment.question_comment';
$user_table = "ahke_user.user";
$question_table = 'ahke_category.question';
function user_notification($conn,$user_id,$item_id,$item_type,$category_num,$op_type){
    $user_notification_table = "ahke_user.user_notification";
    $category_name = "question";
    if($item_type == 1){
       $category_table = "ahke_category.{$category_name}";
    }else{
       $category_table = "ahke_comment.{$category_name}_comment";
    }

    $get_ower_user_id_sql = "SELECT user_id FROM {$category_table} WHERE id = {$item_id} ";
    $get_ower_user_id = $conn->query($get_ower_user_id_sql);
    $get_ower_user_id = $get_ower_user_id->fetch();
    $ower_user_id = $get_ower_user_id["user_id"];

    if($ower_user_id != $user_id){
        if($op_type == "insert"){
        $insert_notification_sql = "INSERT INTO {$user_notification_table} (from_user_id,to_user_id,item_id,category_num,notification_type,item_type)
                                   VALUES ($user_id,$ower_user_id,$item_id,$category_num,4,{$item_type}) ";
        $insert_notification = $conn->exec($insert_notification_sql);
        }else if($op_type == "delete"){
           $delete_notification_sql = "DELETE FROM {$user_notification_table} WHERE to_user_id = {$ower_user_id}
                                       AND item_id = {$item_id} AND category_num = {$category_num} AND item_type = {$item_type} ";
           $delete_notification = $conn->exec($delete_notification_sql);                 
        }
    }
}
if(isset($data['unico_id']) && $HTTP_REFERER){
    $unico_id = local_filter_input($data["unico_id"]);
    // get user id from unico id
    $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
    $get_user_id = $conn->query($get_user_id_sql);
    $get_user_id = $get_user_id->fetch();
    $user_id = $get_user_id["id"];

    $item_id = local_filter_input($data["item_id"]);
    $type = local_filter_input($data["type"]);
    $comment_text = local_filter_post($data["comment_text"]);
    $comment_text = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"<br/>",$comment_text);
    $add_comment_sql = "INSERT INTO {$question_comment_table} (user_id,item_id,type,comment_text)
                        VALUES ({$user_id},{$item_id},{$type},'{$comment_text}') ";
    $add_comment = $conn->query($add_comment_sql);
    if($add_comment){
        echo json_encode(["mess" => "تمت الاضافة"]);
        if($type == 1){
           $plus_comment_num_sql = "UPDATE {$question_table} SET comment_num = comment_num + 1 WHERE id = {$item_id}  ";
        }else{
           $plus_comment_num_sql = "UPDATE {$question_comment_table} SET reply_num = reply_num + 1 WHERE id = {$item_id} ";
        }
        $plus_comment_num = $conn->exec($plus_comment_num_sql);
        user_notification($conn,$user_id,$item_id,$type,6,"insert");
    }else{
    	echo json_encode(["mess" => "لم تتم الاضافة حاول مرة اخرى"]);
    }
}
?>