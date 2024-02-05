<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$comment_table = 'ahke_comment.question_comment';
$category_table = "ahke_category.question";
$user_table = "ahke_user.user";
if(isset($data['unico_id']) && $HTTP_REFERER){
   $unico_id = local_filter_input($data["unico_id"]);
	$parent_id = local_filter_input($data["parent_id"]);
   $comment_id = local_filter_input($data["comment_id"]);
   $type = local_filter_input($data["type"]);

   // Get user id from unico id
   $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
   $get_user_id = $conn->query($get_user_id_sql);
   $get_user_id = $get_user_id->fetch();
   $user_id = $get_user_id["id"];

   // Verify ownership of the post
   $check_post_sql = "SELECT id FROM {$comment_table} WHERE id = {$comment_id} AND user_id = {$user_id} ";
   $check_post = $conn->query($check_post_sql);
   if($check_post->rowCount()){
      $delete_comment_sql = "DELETE FROM {$comment_table} WHERE id={$comment_id}";
      $delete_comment = $conn->exec($delete_comment_sql);
      if($delete_comment){
         echo json_encode(["mess" => "تم الحذف"]);
         if($type == 1){
            $minus_comment_num_sql = "UPDATE {$category_table} SET comment_num = comment_num - 1 WHERE id = {$parent_id}  ";
         }else{
            $minus_comment_num_sql = "UPDATE {$comment_table} SET reply_num = reply_num - 1 WHERE id = {$parent_id} ";
         }
         $minus_comment_num = $conn->exec($minus_comment_num_sql);
      }else{
         echo json_encode(["mess" => "لم يتم الحذف حاول مرة اخرى"]);
      }   
   }else{
      echo json_encode(["mess" => "لا تملك الصلاحيات لاتمام هذا الاجراء"]);
   }
}
?>