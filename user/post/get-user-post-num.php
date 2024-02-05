<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$post_num_table = "ahke_user.user_post_num";
$user_table = "ahke_user.user";
if(isset($data["username"]) && isset($_SERVER['HTTP_REFERER'])){
	$username = local_filter_input($data["username"]);
    // get user id from username
    $get_user_id_sql = "SELECT id FROM {$user_table} WHERE username = '{$username}' ";
    $get_user_id = $conn->query($get_user_id_sql);
    $get_user_id = $get_user_id->fetch();
    $user_id = $get_user_id["id"];

    $get_post_post_num_sql = "SELECT a.*,b.name,b.profile_image FROM {$post_num_table} a INNER JOIN {$user_table} b on a.user_id = b.id WHERE a.user_id = {$user_id} ";
    $post_post_num = $conn->query($get_post_post_num_sql);
    $post_post_num = $post_post_num->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($post_post_num);
    $post_post_num = null;
}
?>