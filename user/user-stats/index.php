<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents("php://input"),1);
$user_table = "ahke_user.user";
$user_stats_table = "ahke_user.user_stats";
$user_post_num_table = "ahke_user.user_post_num";
$user_save_num_table = "ahke_user.user_save_num";
$result = [];
if(isset($data['unico_id'])){
    $unico_id = local_filter_input($data["unico_id"]);
    // Get user id from unico id
    $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
    $get_user_id = $conn->query($get_user_id_sql);
    $get_user_id = $get_user_id->fetch();
    $user_id = $get_user_id["id"];
    
    // get main stats
    $get_main_stats_sql = "SELECT * FROM {$user_stats_table} WHERE user_id = {$user_id} ";
    $get_main_stats = $conn->query($get_main_stats_sql);
    $get_main_stats = $get_main_stats->fetch(PDO::FETCH_ASSOC);
    array_push($result, ['main_stats' => $get_main_stats]);

    // get post number details
    $get_post_num_det_sql = "SELECT * FROM {$user_post_num_table} WHERE user_id = {$user_id} ";
    $get_post_num_det = $conn->query($get_post_num_det_sql);
    $get_post_num_det = $get_post_num_det->fetch(PDO::FETCH_ASSOC);
    array_push($result, ['post_num_det' => $get_post_num_det]);

    // get post saved number details
    $get_save_num_det_sql = "SELECT * FROM {$user_save_num_table} WHERE user_id = {$user_id} ";
    $get_save_num_det = $conn->query($get_save_num_det_sql);
    $get_save_num_det = $get_save_num_det->fetch(PDO::FETCH_ASSOC);
    array_push($result, ['save_num_det' => $get_save_num_det]);

    echo json_encode($result);
}
?>