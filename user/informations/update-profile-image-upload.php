<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
include("../../include/put-image-64.php");

$data = json_decode(file_get_contents('php://input'),1);
$user_table = 'ahke_user.user';

if(isset($data['unico_id']) && $HTTP_REFERER){
   $unico_id = local_filter_input($data["unico_id"]);
   if(!em($data["image_base64"])){
        $image_64 = $data["image_base64"];
        $image_64 = explode(',', $image_64);
        // conver image base64 to image and put in path
        $base64String = $image_64[1];
        $putImage = new putImage64($base64String,"../../media/images/upload-profile-image/");
        $image_path = "u/".$putImage->getPath();
    }else{
        $image_path = "";
    }

    $update_profile_image_sql = "UPDATE {$user_table} SET profile_image = '{$image_path}' WHERE unico_id = '{$unico_id}' ";
    $update_profile_image = $conn->exec($update_profile_image_sql);
    if($update_profile_image){
        echo json_encode(["request_status" => 1,'image_path' => $image_path]);
    }else{
        echo json_encode(["request_status" => 0]);
    }
}
?>