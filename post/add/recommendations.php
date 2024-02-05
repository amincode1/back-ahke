<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
include("./_AddPost.php");

$data = json_decode(file_get_contents('php://input'),1);
function format_duration($duration) {
    $interval = new DateInterval($duration);
    $minutes = $interval->i;
    $seconds = $interval->s;
    return sprintf('%d:%02d', $minutes, $seconds);
}

if(isset($data['unico_id']) && $HTTP_REFERER){
	$unico_id = local_filter_input($data['unico_id']);
	if(em($data['post_text']) && !isset($data["video_id"])){
		echo json_encode(['status_request' => 0,'mess' => 'لم تقم بالادخال']);
	}else{
		if(!em($data['post_text'])){
			$video_id = null;
			$post_text = $data["post_text"];
			$post_text = str_replace(array("\\n\\n"),"<br/>",$post_text);
			$post_text = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"<br/>",$post_text);
			$post_text = local_filter_post($post_text);
		}

		if(!em($data['video_id'])){
         $api_key = "AIzaSyBcj2KTP-KPhctRULDwOoIoK-dipydWAV4";
			$video_id = local_filter_input($data["video_id"]);
			$url = "https://www.googleapis.com/youtube/v3/videos?id=".$video_id."&part=snippet,contentDetails&key=".$api_key;

			// Send HTTP request using cURL
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($ch);
			curl_close($ch);

			// Parse the response data
			$json = json_decode($response);
			$video_title = $json->items[0]->snippet->title;
			$duration = $json->items[0]->contentDetails->duration;

			// Convert duration to mm:ss format
			$duration_formatted = format_duration($duration);

         $video_duration = $duration_formatted;
         $post_text = $video_title;
         $video_id = $video_id."/y";
		}else{
			$video_duration = null;
			$video_id = null;
		}

		$params = [
		   'conn' => $conn,
		   'category_name' => 'recommendations',
		   'unico_id' => $unico_id,
		   'post_text' => $post_text,
		   'post_title' => null,
		   'post_image_64' => null,
		   'video_duration' => $video_duration,
		   'video_id' => $video_id
		];

      new AddPost($params);
	}
}
?>