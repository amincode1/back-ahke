<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");

$data = json_decode(file_get_contents('php://input'),1);
$user_table = "ahke_user.user";

if(isset($data["unico_id"])){
  $unico_id = local_filter_input($data["unico_id"]);
  $profile_image_path = local_filter_input($data["path"]);

  $update_profile_image_sql = "UPDATE $user_table SET `profile_image` = '$profile_image_path' WHERE unico_id = '$unico_id' ";
  $update_profile_image = $conn->exec($update_profile_image_sql);

  if($update_profile_image){
	echo json_encode(["request_status" => 1]);
  }else{
	echo json_encode(["request_status" => 0]);
  }
}
?>