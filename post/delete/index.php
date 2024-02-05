<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
include("./_DeletePost.php");
$data = json_decode(file_get_contents('php://input'),1);

function c_na_to_nu($name){
  $categories = ["quotes" => 1,"stories" => 2,"from_book" => 3,"do_you_know" => 4,"recommendations" => 5,
                     "question" => 6,"questionnaire" => 7,"series" => 8,"images" => 10];
  return $categories[$name] ?? null;
}

$category_name = local_filter_input($data["category_name"]);
$post_id = local_filter_input($data["post_id"]);
$unico_id = local_filter_input($data["unico_id"]);
$category_num = c_na_to_nu($category_name);

$props = [
  "conn"=>$conn,
  "category_name"=>$category_name,
  "category_num"=>$category_num,
  "post_id"=>$post_id,
  "unico_id"=>$unico_id
];

new DeletePost($props);
?>