<?php
include("../../api-setting.php");
include("../../include/config.php");
include("./PutCategoryJsonData.php");

$category_name = "images";
$category_table = "ahke_category.{$category_name}";
$get_post_category_sql = "SELECT * FROM {$category_table}";
$post_category = $conn->query($get_post_category_sql);
while ($post = $post_category->fetch()) {
	$props = [
		"conn"=>$conn,
		"category_name"=>"{$category_name}",
		"type"=>"post",
		"post_id" => "{$post['id']}",
		"path"=>"../../{$json_database}/category/{$category_name}/post/",
		"limit_file"=>500
	];
  new PutCategoryJsonData($props);
}
?>