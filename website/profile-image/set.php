<?php
include("../../api-setting.php");
include("../../include/config.php");
$profile_image_table = "ahke_website.profile_image";
$post_data = '';
$get_profile_image_sql = "SELECT `path` FROM {$profile_image_table} WHERE type = 1";
$profile_image = $conn->query($get_profile_image_sql);

$count_profile_image = $profile_image->rowCount();
$loop_num = ceil($count_profile_image / 15);
$profile_image_arr = $profile_image->fetchAll(PDO::FETCH_ASSOC);
$from = 0;
$limit = 15;

for ($i=1; $i <= $loop_num; $i++) { 
	$file_content_arr = array_slice($profile_image_arr,$from,$limit);
	if($i == 1){
       array_push($file_content_arr, ["page_num" => $loop_num]);
	}
	$file_content = json_encode($file_content_arr);
    $file = fopen("json-data/avater/{$i}.json","w");
    chmod("json-data/avater/{$i}.json",0777);
    fwrite($file,$file_content);
	$from+=15;
}
?>