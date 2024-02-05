<?php
include("../../../api-setting.php");
include("../../../include/config.php");
include("../../../include/crypt.php");
$data = json_decode(file_get_contents("php://input"),1);
$advise_me_table = 'ahke_category.advise_me';
$user_notification_table = "ahke_user.user_notification";
$user_table = 'ahke_user.user';
if(isset($data["username"]) && isset($_SERVER['HTTP_REFERER'])){
   $username = local_filter_post($data["username"]);
   $advise_text = local_filter_post($data["advise_text"]);
   // get user id from username
   $get_user_id_sql = "SELECT id FROM {$user_table} WHERE username = '{$username}' ";
   $get_user_id = $conn->query($get_user_id_sql);
   $get_user_id = $get_user_id->fetch();
   $user_id = $get_user_id["id"];

   if(!em($advise_text)){
       $add_advise_sql = "INSERT INTO {$advise_me_table} (user_id,advise_text) VALUES ({$user_id},'{$advise_text}') ";
       $add_advise = $conn->exec($add_advise_sql);
       if($add_advise){
          echo json_encode(["mess" => "تم الارسال"]);
          // add notification
          $insert_notification_sql = "INSERT INTO {$user_notification_table} (from_user_id,to_user_id,notification_type)
                                      VALUES (1,$user_id,5) ";
          $insert_notification = $conn->exec($insert_notification_sql);
       }else{
       	   echo json_encode(["mess" => "لم يتم الارسال حاول مجدداً"]);
       }
   }else{
   	  echo json_encode(["mess" => "لم تقم بالادخال"]);
   }
}
?>