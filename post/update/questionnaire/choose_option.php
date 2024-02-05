<?php
include("../../../api-setting.php");
include("../../../include/config.php");
include("../../../include/crypt.php");
include("./ChooseOption.php");

$data = json_decode(file_get_contents('php://input'),1);
if(isset($data['unico_id']) && $HTTP_REFERER){
  $unico_id = local_filter_input($data["unico_id"]);
  $post_id = local_filter_input($data["post_id"]);
  $option_num = local_filter_input($data["option_num"]);
  $props = [
    "conn" => $conn,
    "unico_id" => $unico_id,
    "post_id" => $post_id,
    "option_num" => $option_num
  ];
  new ChooseOption($props);
}
?>