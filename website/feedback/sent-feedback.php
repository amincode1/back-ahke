<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$feedback_table = "ahke_website.feedback";
if(isset($data["feedback_text"])){
   $feedback_text = local_filter_input($data["feedback_text"]);
   $user_id = local_filter_input($data["user_id"]);
   if(em($user_id)){
      $user_id = 0;
   }
   $insert_sql = "INSERT INTO {$feedback_table} (feedback_text,user_id) VALUES ('{$feedback_text}','{$user_id}') ";
   $insert = $conn->exec($insert_sql);
   if($insert){
      echo json_encode(["mess" => "تم إرسال اقتراحك شكراً لك"]);
   }else{
   	  echo json_encode(["mess" => "حدث خطأ ما الرجاء المحاولة مرة اخرى"]);
   }

}
?>