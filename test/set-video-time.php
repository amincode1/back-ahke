<?php
set_time_limit(1000);
include("../include/config.php");
include("../include/crypt.php");
function format_duration($duration) {
    $interval = new DateInterval($duration);
    $minutes = $interval->i;
    $seconds = $interval->s;
    return sprintf('%d:%02d', $minutes, $seconds);
}
$video_id = $GET["id"];
$get_post_sql = "SELECT * FROM ahke_category.recommendations WHERE video_id = '{$video_id}'  ";
$get_post = $conn->query($get_post_sql);
if($get_post->rowCount()){
   while($post = $get_post->fetch()){
   $post_id = $post["id"];
   $api_key = "AIzaSyBcj2KTP-KPhctRULDwOoIoK-dipydWAV4";
			
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
            

			$update_sql = "UPDATE ahke_category.recommendations SET video_duration = '{$video_duration}' WHERE id = {$post_id} ";
			$update = $conn->exec($update_sql);
			echo $update_sql;
}  
}
?>




<?php

// $get_post_sql = "SELECT * FROM ahke_category.recommendations WHERE video_id != '' OR video_id != null LIMIT 200,900  ";
// $get_post = $conn->query($get_post_sql);
// if($get_post->rowCount()){
//     while($post = $get_post->fetch()){
//         echo $post['video_id']."<br>";
//     }
// }  


?>