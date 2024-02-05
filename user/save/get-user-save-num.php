<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$save_num_table = "ahke_user.user_save_num";
$user_table = "ahke_user.user";
if(isset($data['unico_id']) && $HTTP_REFERER){
	$unico_id = local_filter_input($data["unico_id"]);
	// get user id from unico id
    $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
    $get_user_id = $conn->query($get_user_id_sql);
    $get_user_id = $get_user_id->fetch();
    $user_id = $get_user_id["id"];

    $get_save_num_sql = "SELECT * FROM {$save_num_table} WHERE user_id = {$user_id} ";
    $get_save_num = $conn->query($get_save_num_sql);
    $save_num = $get_save_num->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($save_num);
    $save_num = null;
}
?>