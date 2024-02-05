<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
function num_to_name_category($num) {
  $categories = [1 => "quotes",2 => "stories",3 => "from_book",4 => "do_you_know",5 => "recommendations",
                 6 => "question",7 => "questionnaire",8 => "series",10 => "images",11 => "videos",];
  return $categories[$num] ?? null;
}
$data = json_decode(file_get_contents('php://input'),1);
$user_table = 'ahke_user.user';
$category_table = null;
if(isset($data['unico_id']) && $HTTP_REFERER){
	$unico_id = local_filter_input($data["unico_id"]);
	$posts_views_id = local_filter_input($data["posts_views_id"]);
	// Get user id from unico id
    $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
    $get_user_id = $conn->query($get_user_id_sql);
    $get_user_id = $get_user_id->fetch();
    $user_id = $get_user_id["id"];

    $posts_id = explode(',',$posts_views_id);
     foreach($posts_id as $post_id){
        if($post_id != null && $post_id != ''){
           $post_id = explode("-",$post_id);
           $view_post_id = $post_id[0];
           $view_post_category = num_to_name_category($post_id[1]);
           $category_table = "ahke_category.{$view_post_category}";
           $update_views_sql = "UPDATE {$category_table} SET views_num = views_num + 1 WHERE id = {$view_post_id} AND last_user_id_view != {$user_id};
                                UPDATE {$category_table} SET last_user_id_view = {$user_id} WHERE id = {$view_post_id}";
                                // echo $update_views_sql;
           $update_views = $conn->exec($update_views_sql);
        }
    }
}
echo 'amin';
?>