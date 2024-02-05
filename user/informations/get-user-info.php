<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
include('./_UserInfo.php');
$data = json_decode(file_get_contents('php://input'),1);
$user_table = "ahke_user.user";
$user_post_num_table = "ahke_user.user_post_num";
$user_stats_table = "ahke_user.user_stats"; 
$user_setting_table = "ahke_user.user_setting"; 

if((isset($data["user_id"]) || isset($data["username"])) && $HTTP_REFERER){
   if(isset($data["user_id"])){
      $user_id = local_filter_input($data["user_id"]);
   }else{
      $user_id = null;
   }

   if(isset($data["username"])){
      $username = local_filter_input($data["username"]);
   }else{
      $username = null;
   }

   $params = [
     'conn' => $conn,
     'user_id' => $user_id,
     'username' => $username
   ];

   new UserInfo($params);	
}
?>


