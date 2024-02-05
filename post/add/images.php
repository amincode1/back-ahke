<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
include("./_AddPost.php");

$data = json_decode(file_get_contents('php://input'),1);
if(isset($data['unico_id']) && $HTTP_REFERER){
	$unico_id = local_filter_input($data['unico_id']);
	if(em($data['image'])){
		echo json_encode(['status_request' => 0,'mess' => 'لم تقم برفع صورة']);
	}else{
		if(!empty($data["post_title"])){
         $post_title = local_filter_input($data["post_title"]);
		}else{
		   $post_title = null;
		}
      $post_image_64 = local_filter_post($data["image"]);
	   $post_image_64 = explode(',', $post_image_64);
	   $post_image_64 = $post_image_64[1];

	   $params = [
		   "conn" => $conn,
		   "category_name" => 'images',
		   "unico_id" => $unico_id,
		   "post_text" => null,
		   "post_title" => $post_title,
		   "post_image_64" => $post_image_64
		];

      new AddPost($params);
	}
}
?>