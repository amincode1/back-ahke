<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
include("../../include/class/PutImage64.php");

function remove_image($conn,$post_id,$category_table){
    $old_image_path_sql = "SELECT image_path FROM {$category_table} WHERE id = {$post_id} ";
    $old_image_path = $conn->query($old_image_path_sql);
    if($old_image_path->rowCount()){
        $old_image_path = $old_image_path->fetch();
	    $old_image_path = $old_image_path["image_path"];
	    if($old_image_path != null && $old_image_path != ""){
	        $old_image_path_mini = explode('.', $old_image_path);
	        $old_image_path_mini = $old_image_path_mini[0]."-mini.".$old_image_path_mini[1];
	        if(file_exists("../../media/images/category/{$old_image_path}")){
               unlink("../../media/images/category/{$old_image_path}");
	        }
	        if(file_exists("../../media/images/category/{$old_image_path}")){
	           unlink("../../media/images/category/{$old_image_path_mini}");
	        }
	    }
    }
}

$category_table = 'ahke_category.do_you_know';
$data = json_decode(file_get_contents('php://input'),1);
$user_table = 'ahke_user.user';

if(isset($data['unico_id']) && $HTTP_REFERER){
	$unico_id = local_filter_input($data["unico_id"]);
	$post_id = local_filter_input($data["post_id"]);
    $post_text = $data["post_text"];
    $post_text = str_replace(array("\\n\\n"),"<br/>",$post_text);
    $post_text = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"<br/>",$post_text);
    $post_text = local_filter_post($post_text);

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
	    	if(!em($data["image"])){
	            $image_64 = local_filter_input($data["image"]);
	            
	            $image_64 = explode(',', $image_64);
	            $image_64 = $image_64[1];
	            // conver image base64 to image and put in path
	            $base64String = $image_64;
	            // put base64 image
	            $putImage = new putImage64($base64String,"../../media/images/category/do-you-know/");
	            $image_path = "do-you-know/".$putImage->getPath();
	            $update_image_sql = ",image_path = '$image_path' ";
	            // remove old image
	            remove_image($conn,$post_id,$category_table);        
	        }else{
	            $update_image_sql = "";
	        }

	        $update_post_sql = "UPDATE {$category_table} SET 
	                            post_text = '{$post_text}',
	                            last_update_date = NOW() 
	                            {$update_image_sql}
	                            WHERE id = {$post_id}";
			$update_post = $conn->query($update_post_sql);
			if($update_post){
				echo json_encode(["mess" => 'تم التعديل',"post_text" => $post_text]);
				// update post in archives
                $url = "$api_host/website/archives/post/do_you_know.php?id=$post_id";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);
			}else{
			    echo json_encode(['mess' => 'لم يتم التعديل حاول مرة اخرى']);
			}
	    }
    }else{
    	echo json_encode(["mess" => "لا تملك الصلاحيات لاتمام هذا الاجراء"]);
    }   
}
    
?>