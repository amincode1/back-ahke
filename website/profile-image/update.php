<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents("php://input"),1);
$user_table = "ahke_user.user";
if(isset($data['unico_id']) && $HTTP_REFERER){
    $unico_id = local_filter_input($data["unico_id"]);
    $image_path = local_filter_input($data["image_path"]);
    
    //Remove old image 
    $old_image_path_sql = "SELECT profile_image FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
    $old_image_path = $conn->query($old_image_path_sql);
    $old_image_path = $old_image_path->fetch();
    $old_image_path = $old_image_path["profile_image"];
    $old_image_path_arr = explode('/',$old_image_path);
    if(in_array('upload-profile-image',$old_image_path_arr)){
        if($old_image_path != null && $old_image_path != ""){
            $old_image_path_mini = explode('.', $old_image_path);
            $old_image_path_mini = $old_image_path_mini[0]."-mini.".$old_image_path_mini[1];
            if(file_exists("../../media/images/category/{$old_image_path}")){
               unlink("../../media/images/category/{$old_image_path}");
            }
            if(file_exists("../../media/images/category/{$old_image_path}")){
               unlink("../../media/images/category/{$old_image_path_mini}");
            }
        }
    }

    $update_user_image_sql = "UPDATE {$user_table} SET profile_image = '{$image_path}' WHERE unico_id = '{$unico_id}' ";
    $update_user_image = $conn->exec($update_user_image_sql);
    if($update_user_image){
        echo json_encode(["mess" => "تم التعديل"]);
    }else{
        echo json_encode(["mess" => "لم يتم التعديل حاول مرة اخرى"]);
    }
    
}
?>