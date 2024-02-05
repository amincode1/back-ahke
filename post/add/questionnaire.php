<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
include("./_AddPost.php");

$data = json_decode(file_get_contents('php://input'),1);
if(isset($data['unico_id']) && $HTTP_REFERER){
	$unico_id = local_filter_input($data['unico_id']);
	if(em($data['post_title'])){
		echo json_encode(['status_request' => 0,'mess' => 'لم تقم بالادخال']);
	}else if(em($data["option_1"]) || em($data["option_2"])){
        echo json_encode(['status_request' => 0,'mess' => 'يجب ملء خيارين علي الاقل']);
	}else{
		$post_title = local_filter_input($data['post_title']);
		$option_1 = local_filter_input($data['option_1']);
		$option_2 = local_filter_input($data['option_2']);
		$option_3 = local_filter_input($data['option_3']);
		$option_4 = local_filter_input($data['option_4']);
		$option_5 = local_filter_input($data['option_5']);
		$correct_answer_num = local_filter_input($data['correct_answer_num']);

		$params = [
		   "conn" => $conn,
		   "category_name" => 'questionnaire',
		   "unico_id" => $unico_id,
		   "post_text" => null,
		   "post_title" => $post_title,
		   "post_image_64" => null,
		   'option_1' => $option_1,
		   'option_2' => $option_2,
		   'option_3' => $option_3,
		   'option_4' => $option_4,
		   'option_5' => $option_5,
		   'correct_answer_num' => $correct_answer_num
		];

       new AddPost($params);
	}
}
?>