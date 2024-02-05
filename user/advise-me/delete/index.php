<?php
include("../../../api-setting.php");
include("../../../include/config.php");
include("../../../include/crypt.php");
$category_table = 'ahke_category.advise_me';
$user_table = 'ahke_user.user';
$data = json_decode(file_get_contents('php://input'),1);
if(isset($data['unico_id']) && $HTTP_REFERER){
   $unico_id = local_filter_input($data["unico_id"]);
   $post_id = local_filter_input($data["post_id"]);

   // get user id from unico id
   $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
   $get_user_id = $conn->query($get_user_id_sql);
   $get_user_id = $get_user_id->fetch();
   $user_id = $get_user_id["id"];

   if(!em($user_id)){
      $delete_advise_sql = "DELETE FROM {$category_table} WHERE id = {$post_id} AND user_id = {$user_id} ";
      $delete_advise = $conn->exec($delete_advise_sql);
      if($delete_advise){
        echo json_encode(["mess" => "تم الحذف"]);
      }else{
        echo json_encode(["mess" => "لم يتم الحذف حاول مرة اخرى"]);
      }
   }
}
?>