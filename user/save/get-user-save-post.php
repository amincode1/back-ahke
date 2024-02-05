<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$user_table = "ahke_user.user";
$save_table = "";
$category_table = "";
$user_saves_id = [];
$posts = [];
if(isset($data["page_num"])){
    $page_num = local_filter_input($data["page_num"]);
    if($page_num != 0){
       $from = (int)$page_num * 10;
    }else{
       $from = 0;
    }
    $limit = 10;
}

if(isset($data['unico_id']) && $HTTP_REFERER){
   $unico_id = local_filter_input($data["unico_id"]);
   $category_name = local_filter_input($data["category_name"]);
   $category_table = "ahke_category.{$category_name}";
   $save_table = "ahke_save.{$category_name}_save";
   // get user id from unico id
   $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
   $get_user_id = $conn->query($get_user_id_sql);
   $get_user_id = $get_user_id->fetch();
   $user_id = $get_user_id["id"];
   
   // get post id from user
   $get_save_post_id_sql = "SELECT posts_id_list FROM {$save_table} WHERE user_id = {$user_id} ";
   $get_save_post_id = $conn->query($get_save_post_id_sql);
   $get_save_post_id = $get_save_post_id->fetch();
   $posts_id = $get_save_post_id["posts_id_list"];
   $posts_id = explode(',',$posts_id);
   $posts_id = array_reverse($posts_id);
   $posts_id = array_slice($posts_id,$from,$limit);
   if(count($posts_id) != 0){
      foreach ($posts_id as $post_id) {
        if(!em($post_id)){
          array_push($user_saves_id, $post_id);
        }
      }

      $user_saves_id_str = implode(',', $user_saves_id);
      // get save post from category table
      $get_post_sql = "SELECT a.*,b.username,b.name,b.profile_image
                        FROM {$category_table} a 
                        INNER JOIN ahke_user.user b on a.user_id = b.id
                        WHERE a.id IN ({$user_saves_id_str}) 
                        ORDER BY FIELD(a.id,{$user_saves_id_str})";
      $get_post = $conn->query($get_post_sql);
      if($get_post->rowCount()){
        array_push($posts, $get_post->fetchAll(PDO::FETCH_ASSOC));
          echo json_encode($posts);
      }else{
        echo json_encode(["mess" => '']);
      }
   }else{
      echo json_encode(["mess" => '']);
   }
}
?>