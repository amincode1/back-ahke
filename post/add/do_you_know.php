<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
include("./_AddPost.php");

$data = json_decode(file_get_contents('php://input'),1);
if(isset($data['unico_id']) && $HTTP_REFERER){
	$unico_id = local_filter_input($data['unico_id']);
	if(em($data['post_text'])){
		echo json_encode(['status_request' => 0,'mess' => 'لم تقم بالادخال']);
	}else{
		$post_text = $data["post_text"];
		$post_text = str_replace(array("\\n\\n"),"<br/>",$post_text);
		$post_text = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"<br/>",$post_text);
		$post_text = local_filter_post($post_text);
 
      if(!empty($data["image"])){
         $post_image_64 = local_filter_post($data["image"]);
	      $post_image_64 = explode(',', $post_image_64);
	      $post_image_64 = $post_image_64[1];
      }else{
        	$post_image_64 = null;
      }

	   $params = [
		   "conn" => $conn,
		   "category_name" => 'do_you_know',
		   "unico_id" => $unico_id,
		   "post_text" => $post_text,
		   "post_title" => null,
		   "post_image_64" => $post_image_64
		];

      new AddPost($params);

	}
}
?>