<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$user_table = "ahke_user.user";
$user_notification_table = "ahke_user.user_notification";
if(isset($data['unico_id']) && $HTTP_REFERER){
	$unico_id = local_filter_input($data["unico_id"]);
	$page_num = local_filter_input($data["page_num"]);
	$page_num = $page_num * 10;
	// Get user id from unico id
    $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
    $get_user_id = $conn->query($get_user_id_sql);
    $get_user_id = $get_user_id->fetch();
    $user_id = $get_user_id["id"];

    $get_notification_sql = "SELECT a.*,b.username as from_username,b.name as from_user_name,b.profile_image as from_user_profile_image
                             FROM {$user_notification_table} a 
                             INNER JOIN {$user_table} b on b.id = a.from_user_id 
                             WHERE a.to_user_id = $user_id ORDER BY a.id DESC LIMIT {$page_num},10";
    $get_notification = $conn->query($get_notification_sql);
    if($get_notification->rowCount()){
        $get_notification = $get_notification->fetchAll(PDO::FETCH_ASSOC);
	    echo json_encode($get_notification);
	    // Update watched 
	    $watched_id = [];
	    foreach ($get_notification as $notification) {
	    	array_push($watched_id,$notification["id"]);
	    }
	    $watched_id = implode(',',$watched_id);
	    $update_watched_sql = "UPDATE {$user_notification_table} SET watched = 1 WHERE id IN ($watched_id) ";
	    $update_watched = $conn->exec($update_watched_sql);
    }else{
    	echo json_encode(["mess" => '']);
    }
}
?>