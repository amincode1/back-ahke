<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$user_table = 'ahke_user.user';
if(isset($data["old_password"]) && $HTTP_REFERER){
    $unico_id = local_filter_input($data["unico_id"]);
    $old_password = local_filter_input(local_crypt($data["old_password"]));
    $new_password = local_filter_input(local_crypt($data["new_password"]));
    $con_new_password = local_filter_input(local_crypt($data["con_new_password"]));
    //check old pasword 
    $get_old_password_sql = "SELECT password FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
    $get_old_password = $conn->query($get_old_password_sql);
    $get_old_password = $get_old_password->fetch();
    $user_old_password = $get_old_password['password']; 
    if($old_password == ""){
        echo json_encode(["stats_request" => 0,"mess" => "الرجاء ادخال كلمة المرور القديمة"]);
    }else if($new_password == ""){
        echo json_encode(["stats_request" => 0,"mess" => "الرجاء ادخال كلمة المرور الجديدة"]);
    }else if($con_new_password == ""){
        echo json_encode(["stats_request" => 0,"mess" => "الرجاء ادخال تاكيد كلمة المرور الجديدة"]);
    }else if($old_password != $user_old_password){
        echo json_encode(["stats_request" => 0,"mess" => "كلمة المرور القديمة غير صحيحة"]);
    }else if($new_password != $con_new_password){
        echo json_encode(["stats_request" => 0,"mess" => "كلمة المرور الجديد غير متطابقة"]);
    }else{
        $update_password_sql = "UPDATE {$user_table} SET password = '{$new_password}' WHERE unico_id = '{$unico_id}' ";
        $update_password = $conn->exec($update_password_sql);
        if($update_password){
            echo json_encode(["stats_request" => 1,"mess" => "تم تغيير كلمة المرور"]);
        }
    }
}
?>