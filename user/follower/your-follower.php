<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$user_table = "ahke_user.user";
$user_follower_table = "ahke_user.user_follower";
$follower = [];
if(isset($data["page_num"]) && isset($_SERVER['HTTP_REFERER'])){
    $page_num = local_filter_input($data["page_num"]);
    if($page_num != 0){
       $from = (int)$page_num * 10;
    }else{
       $from = 0;
    }
    $limit = 10;
}
if(isset($data["unico_id"])){
	$unico_id = local_filter_input($data["unico_id"]);
	// get user id from unico id
    $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
    $get_user_id = $conn->query($get_user_id_sql);
    $get_user_id = $get_user_id->fetch();
    $user_id = $get_user_id["id"];

    // get user being follower
    $get_being_follower_sql = "SELECT users_follower_id FROM {$user_follower_table} WHERE user_id = {$user_id} ";
    $get_being_follower = $conn->query($get_being_follower_sql);
    if($get_being_follower){
        $get_being_follower = $get_being_follower->fetch();
        $being_follower_str = $get_being_follower["users_follower_id"];
        $being_follower_arr = explode(',',$being_follower_str);
        $being_follower_arr = array_diff($being_follower_arr,[null]);
        $being_follower_str = implode(',',$being_follower_arr);
        array_push($follower,["users_follower_id" => $being_follower_str]);

        // get user being follower info
        $being_follower_arr = array_reverse($being_follower_arr);
        $being_follower_arr = array_slice($being_follower_arr,$from,$limit);
        if(!empty($being_follower_arr)){
            $being_follower_str = implode(',',$being_follower_arr);
            $get_follower_info_sql = "SELECT id,username,name,profile_image FROM {$user_table} WHERE id IN ({$being_follower_str}) 
                                      ORDER BY FIELD(id,{$being_follower_str})";
            $get_follower_info = $conn->query($get_follower_info_sql);
            $get_follower_info = $get_follower_info->fetchAll(PDO::FETCH_ASSOC);
            array_push($follower,$get_follower_info);
            echo json_encode($follower);
        }else{
            echo json_encode(["mess" => '']);
        }
    }else{
        echo json_encode(["mess" => '']);
    }
}
?>