<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
include("./_PutCategoryJsonData.php");

function num_to_name_category($num) {
	$categories = [1 => 'quotes',2 => 'stories',3 => 'from_book',4 => 'do_you_know',5 => 'recommendations',
	                 6 => 'question',7 => 'questionnaire',8 => 'series',10 => 'images',11 => 'videos'];
	return $categories[$num] ?? null;
}

if(isset($_GET["post_id"])){
	$post_id = local_filter_input($_GET["post_id"]);
	$category_num = local_filter_input($_GET["category_num"]);
	$user_id = local_filter_input($_GET['user_id']);

	$category_name = num_to_name_category($category_num);
  $params = [
    "conn"=> $conn,
		"category_num"=> $category_num,
		"user_id" => $user_id,
		"type"=> "post",
		"post_id" => $post_id,
		"path"=>"../../{$json_database}/category/{$category_name}/post/",
		"limit_file"=> 1
  ];
  new PutCategoryJsonData($params);
} else{
	echo json_encode(["status_request" => 0]);
}
?>