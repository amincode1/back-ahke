<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$user_setting_table = "ahke_user.user_setting";
$user_table = "ahke_user.user";
if(isset($data['unico_id']) && $HTTP_REFERER){
	$unico_id = local_filter_input($data["unico_id"]);
	// Get user id from unico id
    $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
    $get_user_id = $conn->query($get_user_id_sql);
    $get_user_id = $get_user_id->fetch();
    $user_id = $get_user_id["id"];

	$setting_name = local_filter_input($data["setting_name"]);
	$setting_value = local_filter_input($data["setting_value"]);
	$update_setting_sql = "UPDATE {$user_setting_table} SET {$setting_name} = '{$setting_value}' WHERE user_id = {$user_id} ";
	$update_setting = $conn->exec($update_setting_sql);
	if(isset($update_setting)){
        echo json_encode(["status_request" => 1]);
	}else{
		echo json_encode(["status_request" => 0]);
	}
}
?>