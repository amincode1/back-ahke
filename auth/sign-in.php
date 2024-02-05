<?php
include("../api-setting.php");
include("../include/config.php");
include("../include/crypt.php");
$user_table = "ahke_user.user";
$user_stats_table = "ahke_user.user_stats";
$user_setting_table = "ahke_user.user_setting";
$data = json_decode(file_get_contents('php://input'),1);

if(isset($data["username"]) && isset($_SERVER['HTTP_REFERER'])){
    $username = local_filter_input($data["username"]);
    $password = local_filter_input($data["password"]);
    $password = local_crypt($password);
    // check username and password in database
    $check_user_sql = "SELECT id FROM {$user_table} WHERE (username = '{$username}' OR email = '{$username}' ) AND password = '{$password}' ";
    $check_user = $conn->query($check_user_sql);
    if($check_user->rowCount()){
        // user user info
        $check_user = $check_user->fetch();
        $user_id = $check_user["id"];
        $get_user_info_sql = "SELECT * FROM {$user_table} a
                              INNER JOIN {$user_stats_table} b on a.id = b.user_id 
                              INNER JOIN {$user_setting_table} c on a.id = c.user_id
                              WHERE a.id = {$user_id}";
        $get_user_info = $conn->query($get_user_info_sql);
        if($get_user_info->rowCount()){
            $get_user_info = $get_user_info->fetchAll(PDO::FETCH_ASSOC);
            $get_user_info[0]["password"] =  null;
            $user_id = $get_user_info[0]["user_id"];
            // array_push($result,["mess" => "تم التسجيل"]);
            // array_push($result,$get_user_info);
            $request_status = ["request_status" => 1];
            $get_user_info = ["info" => $get_user_info[0]];
            // get user post
            $user_post = file_get_contents("../json-database/user/@$user_id/post-id.txt");
            $user_post = ["post" => $user_post];
            // get user save post
            $save_post = file_get_contents("../json-database/user/@$user_id/save-post-id.txt");
            $save_post = ["save_post" => $save_post];
            echo json_encode(array_merge($request_status,$get_user_info,$user_post,$save_post));
        }else{
            $request_status = ["request_status" => 0];
            echo json_encode($request_status);
        }
    }else{
        $request_status = ["request_status" => 0];
        echo json_encode($request_status);
    }
}
?>