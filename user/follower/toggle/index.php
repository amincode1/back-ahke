<?php
include("../../../api-setting.php");
include("../../../include/config.php");
include("../../../include/crypt.php");
include("./_ToggleFollower.php");
$data = json_decode(file_get_contents('php://input'),1);

if(isset($data['unico_id']) && $HTTP_REFERER){
	$unico_id = local_filter_input($data['unico_id']);
	$user_follow_id = local_filter_input($data["user_follow_id"]);

	$params = [
	   'conn' => $conn,
       'unico_id' => $unico_id,
       'user_follow_id' => $user_follow_id,
	];

	new ToggleFollower($params);
}
?>