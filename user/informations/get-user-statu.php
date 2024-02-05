<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$user_stats = "ahke_user.user_stats";
if(isset($data["user_id"]) && $HTTP_REFERER){
	$user_id = local_filter_input($data["user_id"]);
	$get_user_stats_sql = "SELECT * FROM {$user_stats} WHERE user_id = {$user_id} ";
	$get_user_stats = $conn->query($get_user_stats_sql);
	$get_user_stats = $get_user_stats->fetchAll(PDO::FETCH_ASSOC);
	echo json_encode($get_user_stats);
}
?>