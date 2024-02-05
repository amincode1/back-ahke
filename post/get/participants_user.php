<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$category_table = "ahke_category.questionnaire";
$user_table = "ahke_user.user";
$participants_user_str = '';
$user_arr = [];
if(isset($data["post_id"]) && isset($_SERVER['HTTP_REFERER'])){
   $post_id = local_filter_input($data["post_id"]);
   $page_num = local_filter_input($data["page_num"]);
   if($page_num != 0){
      $from = (int)$page_num * 10;
      $to = $from + 10;
   }else{
      $from = 0;
      $to = $from + 10;
   }

   $get_participants_sql = "SELECT participants FROM {$category_table} WHERE id = {$post_id} ";
   $get_participants = $conn->query($get_participants_sql);
   if($get_participants->rowCount()){
   	  $get_participants = $get_participants->fetch();
      $get_participants = $get_participants["participants"];
      $get_participants_arr = explode('-',$get_participants);
      $get_participants_arr = array_reverse($get_participants_arr);
      $get_participants_arr = array_slice($get_participants_arr,$from,$to);

      foreach ($get_participants_arr as $participant) {
      	$participant = explode(',',$participant);
      	$participant = trim($participant[0],'(');
      	$participants_user_str .= $participant.',';
      }
      $participants_user_str = rtrim($participants_user_str,',');
      $participants_user_str = trim($participants_user_str,',');
      
      if(!empty($participants_user_str)){
        $get_user_info_sql = "SELECT id,username,name,profile_image FROM {$user_table} WHERE id IN ({$participants_user_str}) 
                              ORDER BY FIELD(id,{$participants_user_str})";
        $user_like = $conn->query($get_user_info_sql);
        $user_like = $user_like->fetchAll(PDO::FETCH_ASSOC);
        array_push($user_arr, ["mess" => 1]);
        array_push($user_arr, $user_like);
      }else{
         array_push($user_arr, ["mess" => 0]);
      }
      
      echo json_encode($user_arr);
   }
}
?>