<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$user_table = "ahke_user.user";
if(isset($data['unico_id']) && $HTTP_REFERER){
   $unico_id = local_filter_input($data["unico_id"]);
   $post_id = local_filter_input($data["post_id"]);
   $category_name = local_filter_input($data["category_name"]);
   $image_path = local_filter_input($data["image_path"]);
   $category_table = "ahke_category.{$category_name}";

   // Get user id from unico id
   $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
   $get_user_id = $conn->query($get_user_id_sql);
   $get_user_id = $get_user_id->fetch();
   $user_id = $get_user_id["id"];

   // Verify ownership of the post
   $check_post_sql = "SELECT id FROM {$category_table} WHERE id = {$post_id} AND user_id = {$user_id} ";
   $check_post = $conn->query($check_post_sql);
   if($check_post->rowCount()){
   	   $remove_image_path = $image_path;
   	   $remove_image_path_mini = explode('.',$remove_image_path);
   	   $remove_image_path_mini = $remove_image_path_mini[0]."-mini.".$remove_image_path_mini[1];
         $remove_image = unlink($remove_image_path);
	      if(isset($remove_image)){
   	      $remove_image_sql = "UPDATE {$category_table} SET image_path = null WHERE id = {$post_id} ";
   	      $remove_image = $conn->exec($remove_image_sql);
   	      if($remove_image){
   	        echo json_encode(["mess" => "تم حذف الصورة"]);
   	      }else{
   	        echo json_encode(["mess" => "لم يتم حذف الضورة حاول مرة اخرى"]);
   	      }
   	      $remove_image_mini = unlink($remove_image_path_mini);
	      }
   }else{
   	  echo json_encode(["mess" => "لا تملك الصلاحيات لاتمام هذا الاجراء"]);
   }
}
?>