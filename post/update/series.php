<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$category_table = 'ahke_category.series';
$data = json_decode(file_get_contents('php://input'),1);
$user_table = 'ahke_user.user';
if(isset($data['unico_id']) && $HTTP_REFERER){
	$unico_id = local_filter_input($data["unico_id"]);
	$post_id = local_filter_input($data["post_id"]);
    $post_text = $data["post_text"];
    $post_text = str_replace(array("\\n\\n"),"<br/>",$post_text);
    $post_text = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"<br/>",$post_text);
    $post_text = local_filter_post($post_text);
    $post_title = local_filter_input($data["post_title"]);
    // Get user id from unico id
    $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
    $get_user_id = $conn->query($get_user_id_sql);
    $get_user_id = $get_user_id->fetch();
    $user_id = $get_user_id["id"];

    // Verify ownership of the post
    $check_post_sql = "SELECT id FROM {$category_table} WHERE id = {$post_id} AND user_id = {$user_id} ";
    $check_post = $conn->query($check_post_sql);
    if($check_post->rowCount()){
        if(em($post_text)){
            echo json_encode(['mess' => 'لم تقم بالادخال']);
	    }else{
	        $update_post_sql = "UPDATE {$category_table} SET post_text = '{$post_text}',post_title = '{$post_title}',last_update_date = NOW() WHERE id = {$post_id}";
			$update_post = $conn->query($update_post_sql);
			if($update_post){
				echo json_encode(["mess" => 'تم التعديل',"post_text" => $post_text]);
                // update post in archives
                // $url = "$api_host/website/archives/post/quotes.php?id=$post_id";
                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_URL, $url);
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                // $response = curl_exec($ch);
                // curl_close($ch);
			}else{
			    echo json_encode(['mess' => 'لم يتم التعديل حاول مرة اخرى']);
			}
	    }
    }else{
    	echo json_encode(["mess" => "لا تملك الصلاحيات لاتمام هذا الاجراء"]);
    }   
}
    
?>