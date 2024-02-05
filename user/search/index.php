<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents("php://input"),1);
$user_table = "ahke_user.user";
$limit = 15;
if(isset($data["search_text"]) && isset($_SERVER['HTTP_REFERER'])){
    $search_text = local_filter_input($data["search_text"]);
    $page_num = local_filter_input($data["page_num"]);
    $from_num = (int)$page_num * $limit;
    if(!em($search_text)){
      $get_user_sql = "SELECT id,name,username,profile_image FROM {$user_table} WHERE name LIKE '%{$search_text}%' OR username LIKE '%{$search_text}%' LIMIT {$from_num},{$limit}  ";
      $get_user = $conn->query($get_user_sql);
      if($get_user->rowCount()){
        $get_user = $get_user->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($get_user);  
      }else{
      	echo json_encode(["mess" => '']);  
      }
    }else{
      	echo json_encode(["mess" => '']);  
     }
}
?>