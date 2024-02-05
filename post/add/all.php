<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$category_db_name = 'ahke_category.quotes';
if(isset($id)){
   $id = local_filter_input($id);
   $get_post_sql = "SELECT a.*,b.username,b.name,b.profile_image FROM {$category_db_name} a INNER JOIN ahke_user.user b on a.user_id = b.id WHERE a.id = '$id'";
}else{
   $get_post_sql = "SELECT a.*,b.username,b.name,b.profile_image FROM {$category_db_name} a INNER JOIN ahke_user.user b on a.user_id = b.id ORDER BY RAND() LIMIT 10";
}
$get_date = $conn->query($get_post_sql);
$data = $get_date->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($data);
?>