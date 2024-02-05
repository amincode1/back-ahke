<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
include("./_ItemLike.php");
$data = json_decode(file_get_contents("php://input"),1);
function num_to_name_category($num) {
  $categories = [1 => "quotes",2 => "stories",3 => "from_book",4 => "do_you_know",5 => "recommendations",
                 6 => "question",7 => "questionnaire",8 => "series",10 => "images",11 => "videos",];
  return $categories[$num] ?? null;
}
if(isset($data['unico_id']) && $HTTP_REFERER){
	$unico_id = local_filter_input($data["unico_id"]);
	$item_id = local_filter_input($data["item_id"]);
	$type = local_filter_input($data["type"]);
	$category_num = local_filter_input($data["category_num"]);
	$category_name = num_to_name_category($category_num);
   
   $params = [
    	"conn" => $conn,
      "unico_id" => $unico_id,
      "item_id" => $item_id,
      "type" => $type,
      "category_num" => $category_num,
      "category_name" => $category_name,
   ];

   new ItemLike($params);
}
?>