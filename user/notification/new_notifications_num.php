<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$user_table = "ahke_user.user";
$user_notification_table = "ahke_user.user_notification";
if(isset($data['unico_id']) && $HTTP_REFERER){
	$unico_id = local_filter_input($data["unico_id"]);
	// Get user id from unico id
    $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
    $get_user_id = $conn->query($get_user_id_sql);
    $get_user_id = $get_user_id->fetch();
    $user_id = $get_user_id["id"];

    $new_notifications_num_sql = "SELECT COUNT(id) as new_notifications_num FROM {$user_notification_table} WHERE to_user_id = $user_id AND watched = 0";
    $new_notifications_num = $conn->query($new_notifications_num_sql);
    $new_notifications_num = $new_notifications_num->fetchAll(PDO::FETCH_ASSOC);
    // $new_notifications_num = $new_notifications_num['new_notifications_num']; 
    echo json_encode($new_notifications_num);
}
?>