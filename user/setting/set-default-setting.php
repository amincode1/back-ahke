<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents("php://input"),1);
$user_setting_table = "ahke_user.user_setting";
$user_table = "ahke_user.user";
if(isset($data['unico_id']) && $HTTP_REFERER){
    $unico_id = local_filter_input($data["unico_id"]);
    // get user id from unico id
    $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
    $get_user_id = $conn->query($get_user_id_sql);
    $get_user_id = $get_user_id->fetch();
    $user_id = $get_user_id["id"];

    $set_default_setting_sql = "UPDATE {$user_setting_table} SET
    theme = 1,
    font_size = 16,
    data_saving = 0,
    sign_out_time = 1,
    hide_email = 1,
    hide_followers_num = 1,
    advise_me_email = 1,
    hide_btn_advise_me = 1 ,
    last_update_date = NOW() 
    WHERE user_id = {$user_id} ";
    $set_default_setting = $conn->exec($set_default_setting_sql);
    if($set_default_setting){
       echo json_encode(["mess" => "تم استعادة الاعدادات الافتراضية"]);
    }else{
       echo json_encode(["mess" => "لم يتم التعديل حاول مرة اخرى"]);
    }
}
?>