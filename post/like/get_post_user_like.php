<?php
include('../../api-setting.php');
include('../../include/config.php');
include('../../include/crypt.php');
include('./_PostUserLike.php');
$data = json_decode(file_get_contents('php://input'),1);
if(isset($data["post_id"]) && isset($_SERVER['HTTP_REFERER'])){
   $post_id = local_filter_input($data['post_id']);
   $category_name = local_filter_input($data['category_name']);
   $page_num = local_filter_input($data['page_num']);

   $params = [
     'conn' => $conn,
     'post_id' => $post_id,
     'category_name' => $category_name,
     'page_num' => $page_num
   ];

   new PostUserLike($params);
}
?>