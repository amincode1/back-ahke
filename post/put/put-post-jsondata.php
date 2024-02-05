<?php
include("../../include/config.php");
include("../../api-setting.php");
include("./_PutCategoryJsonData.php");
$categories_name = ["do_you_know","from_book","images","questionnaire","quotes","recommendations","series","stories"];

forEach($categories_name as $category_name){
    $get_all_posts = $conn->query("SELECT `id`,`user_id` FROM ahke_category.$category_name ");
	while($post = $get_all_posts->fetch()){
	  $post_id = $post["id"];
	  $user_id = $post["user_id"];
	  $props = [
		"conn"=>$conn,
		"category_name"=> $category_name,
		"type"=>"post",
		"path"=>"../../{$json_database}/category/{$category_name}/post/",
		"limit_file"=>500,
		"post_id"=>"$post_id",
		"user_id" => $user_id,
		"username" => ''
	   ];
	  new PutCategoryJsonData($props);
	}
}
?>