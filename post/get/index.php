<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
include("./_GetPost.php");

if($HTTP_REFERER){
  $data = json_decode(file_get_contents('php://input'),1);
  $category_name = local_filter_input($data["category_name"]);
  $type = local_filter_input($data["type"]);
  if(isset($data["unico_id"])){
     $unico_id = local_filter_input($data["unico_id"]);
  }else{
     $unico_id = null;
  }
  if(isset($data["post_id"])){
     $post_id = local_filter_input($data["post_id"]);
  }else{
     $post_id = null;
  }
  if(isset($data["username"])){
    $username = local_filter_input($data["username"]);
  }else{
    $username = null;
  }
  if(isset($data["search_text"])){
    $search_text = local_filter_input($data["search_text"]);
  }else{
    $search_text = null;
  }
  if(isset($data["page_num"])){
    $page_num = local_filter_input($data["page_num"]);
  }else{
    $page_num = 0;
  }

  $props = [
    "conn" => $conn,
    "category_name" => $category_name,
    "type" => $type,
    "post_id" => $post_id,
    "unico_id" => $unico_id,
    "username" => $username,
    "search_text" => $search_text,
    "page_num" => $page_num
  ];

  new GetPost($props);
}
?>