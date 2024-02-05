<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$category_table = 'ahke_category.question';
$user_table = "ahke_user.user";
$data = json_decode(file_get_contents('php://input'),1);
if(isset($data['unico_id']) && $HTTP_REFERER){
	$unico_id = local_filter_input($data['unico_id']);
	if(em($data['post_text'])){
		echo json_encode(['status_request' => 0,'mess' => 'لم تقم بالادخال']);
	}else{
		// get user id from unico id
	    $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
	    $get_user_id = $conn->query($get_user_id_sql);
	    $get_user_id = $get_user_id->fetch();
	    $user_id = $get_user_id["id"];

		$post_text = $data["post_text"];
		$post_text = str_replace(array("\\n\\n"),"<br/>",$post_text);
		$post_text = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"<br/>",$post_text);
		$post_text = local_filter_post($post_text);
		
		$add_post_sql = "INSERT INTO {$category_table} (`user_id`,`post_text`) VALUES ({$user_id},'{$post_text}')";
		$add_post = $conn->query($add_post_sql);
		if($add_post){
		    // add post to archives
		    $get_post_id_sql = "SELECT id FROM $category_table WHERE user_id = $user_id ORDER BY id DESC LIMIT 1 ";
		    $get_post_id = $conn->query($get_post_id_sql);
		    $get_post_id = $get_post_id->fetch();
		    $get_post_id = $get_post_id["id"];
		    echo json_encode(['status_request' => 1,"mess" => 'تمت الاضافة',"id" => $get_post_id]);
		    $url = "$api_host/website/archives/post/question.php?id=$get_post_id";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($ch);
			curl_close($ch);
		}else{
			echo json_encode(['status_request' => 0,'mess' => 'حدث خطأ ما حاول مرة اخرى']);
		} 
	}
}
?>