<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
include("./_PostStatus.php");
$data = json_decode(file_get_contents('php://input'),1);
if(isset($data['unico_id']) && $HTTP_REFERER){
   $params = [
    "conn" => $conn,
    "unico_id" => local_filter_input($data["unico_id"]),
    "type" => local_filter_input($data["type"]),
    "status" => local_filter_input($data["status"]),
    "category_name" => local_filter_input($data["category_name"]),
    "post_id" => local_filter_input($data["post_id"])
   ];
   new PostStatus($params);
}
?>