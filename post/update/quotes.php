<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
include("./UpdatePost.php");
$data = json_decode(file_get_contents('php://input'),1);
if(isset($data['unico_id']) && $HTTP_REFERER){
	$unico_id = local_filter_input($data["unico_id"]);
	$post_id = local_filter_input($data["post_id"]);
    if(isset($data["post_text"])){
      $post_text = $data["post_text"];
      $post_text = str_replace(array("\\n\\n"),"<br/>",$post_text);
      $post_text = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"<br/>",$post_text);
      $post_text = local_filter_post($post_text);
    }else{
      $post_text = null;
    }
    if(isset($data["post_title"])){
      $post_title = local_filter_post($data["post_title"]);
    }else{
      $post_title = null;
    }
    if(isset($data["image"])){
      $post_image_64 = local_filter_post($data["image"]);
    }else{
      $post_image_64 = null;
    }
    
    $params = [
      "conn" => $conn,
      "category_name" => 'quotes',
      "post_id" => $post_id,
      "unico_id" => $unico_id,
      "post_text" => $post_text,
      "post_title" => $post_title,
      "post_image_64" => $post_image_64
    ];
    new UpdatePost($params);   
}
    
?>