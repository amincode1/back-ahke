<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$user_table = "ahke_user.user";
$user_follower_table = "ahke_user.user_follower";
$user_stats_table = "ahke_user.user_stats"; 
if($data["unico_id"] && isset($_SERVER['HTTP_REFERER'])){
	$unico_id = local_filter_input($data["unico_id"]);
    $follower_user_id = local_filter_input($data["follower_user_id"]);
	// get user id from unico id
    $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
    $get_user_id = $conn->query($get_user_id_sql);
    $get_user_id = $get_user_id->fetch();
    $user_id = $get_user_id["id"];

    // check follower in user_follower table
    $get_being_follower_sql = "SELECT users_follower_id FROM {$user_follower_table} WHERE user_id = {$user_id} ";
    $get_being_follower = $conn->query($get_being_follower_sql);
    $get_being_follower = $get_being_follower->fetch();
    $being_follower = $get_being_follower["users_follower_id"];
    $being_follower_arr = explode(',', $being_follower);
    if(in_array($follower_user_id,$being_follower_arr)){
       $being_follower_arr = array_diff($being_follower_arr,[$follower_user_id]);
       $user_follower_id_str = implode(",", $being_follower_arr);
       $being_followed_num = count($being_follower_arr) - 1;
       $update_being_follower_sql = "UPDATE {$user_follower_table} SET users_follower_id = '{$user_follower_id_str}' WHERE user_id = {$user_id};
                                     UPDATE {$user_stats_table} SET being_followed_num = {$being_followed_num} WHERE user_id = {$user_id} ";
       $update_being_follower = $conn->exec($update_being_follower_sql);
       if($update_being_follower){
          echo json_encode(["mess" => "تمت إلغاء المتابعة","user_being_followed" => $user_follower_id_str]);

          // update follower user info
          $get_user_follower_sql = "SELECT * FROM {$user_follower_table} WHERE user_id = {$follower_user_id} ";
          $get_user_follower = $conn->query($get_user_follower_sql);
          $get_user_follower = $get_user_follower->fetch();
          $user_follower_str = $get_user_follower["users_follower_id"];
          $user_follower_arr = explode(',', $user_follower_str);
          $user_follower_arr = array_diff($user_follower_arr, [$user_id]);
          $user_follower_num = count($user_follower_arr) - 1;
          $user_follower_str = implode(',',$user_follower_arr);
          $update_user_follower_sql = "UPDATE {$user_follower_table} SET users_follower_id = '{$user_follower_str}' WHERE user_id = {$follower_user_id};
                                       UPDATE {$user_stats_table} SET follower_num = {$user_follower_num} WHERE user_id = {$follower_user_id}  ";
          $update_user_follower_sql = $conn->exec($update_user_follower_sql);

       }
    }else{
       $user_follower_id_str = implode(",", $being_follower_arr);
       $user_follower_id_str = $user_follower_id_str.$follower_user_id.",";
       $being_follower_arr = explode(',',$user_follower_id_str);
       $being_followed_num = count($being_follower_arr) - 1;
       $update_being_follower_sql = "UPDATE {$user_follower_table} SET users_follower_id = '{$user_follower_id_str}' WHERE user_id = {$user_id};
                                     UPDATE {$user_stats_table} SET being_followed_num = {$being_followed_num} WHERE user_id = {$user_id} ";
       $update_being_follower = $conn->exec($update_being_follower_sql);
       if($update_being_follower){
          echo json_encode(["mess" => "تمت المتابعة","user_being_followed" => $user_follower_id_str]);

          // update follower user info
          $get_user_follower_sql = "SELECT * FROM {$user_follower_table} WHERE user_id = {$follower_user_id} ";
          $get_user_follower = $conn->query($get_user_follower_sql);
          $get_user_follower = $get_user_follower->fetch();
          $user_follower_str = $get_user_follower["users_follower_id"];
          $user_follower_str = $user_follower_str.$user_id.",";
          $user_follower_arr = explode(',',$user_follower_str);
          $user_follower_num = count($user_follower_arr) - 1;
          $update_user_follower_sql = "UPDATE {$user_follower_table} SET users_follower_id = '{$user_follower_str}' WHERE user_id = {$follower_user_id};
                                       UPDATE {$user_stats_table} SET follower_num = {$user_follower_num} WHERE user_id = {$follower_user_id}";
          $update_user_follower_sql = $conn->exec($update_user_follower_sql);
          
       }
    }

    
}
?>