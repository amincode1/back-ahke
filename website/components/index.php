<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$components_table = "ahke_website.components";
$user_table = "ahke_user.user";
if(isset($data['unico_id']) && $HTTP_REFERER){
   $unico_id = local_filter_input($data["unico_id"]);
   $category_num = local_filter_input($data["category_num"]);
   $item_id = local_filter_input($data["item_id"]);
   $item_type = local_filter_input($data["item_type"]);
   $component_text = local_filter_input($data["component_text"]);
   // get user id from unico id
   $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
   $get_user_id = $conn->query($get_user_id_sql);
   $get_user_id = $get_user_id->fetch();
   $user_id = $get_user_id["id"];

   $component_num = local_filter_input($data["component_num"]);
   $insert_component_sql = "INSERT INTO {$components_table} (from_user_id,category_num,item_id,item_type,components_num,component_text)
                         VALUES ({$user_id},{$category_num},{$item_id},{$item_type},{$component_num},'{$component_text}')";
   $insert_components = $conn->exec($insert_component_sql);
   if($insert_components){
      echo json_encode(["status_request" => 1]);
   }else{
      echo json_encode(["status_request" => 0]);
   }
}
?>