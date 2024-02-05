<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
include("./_AddPost.php");
$category_table = 'ahke_category.quotes';
$user_table = "ahke_user.user";
$data = json_decode(file_get_contents('php://input'),1);
if(isset($data['unico_id']) && $HTTP_REFERER){
	$unico_id = local_filter_input($data['unico_id']);
	$post_text = $data["post_text"];
    $post_text = str_replace(array("\\n\\n"),"<br/>",$post_text);
    $post_text = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"<br/>",$post_text);
    $post_text = local_filter_post($post_text);
	if(em($data['post_text'])){
		echo json_encode(['status_request' => 0,'mess' => 'لم تقم بالادخال']);
	}else{
	    $params = [
		   "conn" => $conn,
		   "category_name" => 'quotes',
		   "unico_id" => $unico_id,
		   "post_text" => $post_text,
		   "post_title" => '',
		   "post_image_64" => ''
		];

      new AddPost($params);
	}
}
?>