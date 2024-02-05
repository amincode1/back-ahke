<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
include("./_AddPost.php");

$data = json_decode(file_get_contents('php://input'),1);
if(isset($data['unico_id']) && $HTTP_REFERER){
	$unico_id = local_filter_input($data['unico_id']);
	if(em($data['post_text']) && em($data['image'])){
		echo json_encode(['status_request' => 0,'mess' => 'الرجاء ادخال نص او رفع صورة']);
	}else if(em($data['post_title'])){
      echo json_encode(['status_request' => 0,"mess" => 'لم تقم بادخال اسم الكتاب']);
	}else{
		$post_title = local_filter_input($data["post_title"]);
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
		   "category_name" => 'from_book',
		   "unico_id" => $unico_id,
		   "post_text" => $post_text,
		   "post_title" => $post_title,
		   "post_image_64" => $post_image_64
		];

      new AddPost($params);
	}
}
?>