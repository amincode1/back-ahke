<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$category_table = "ahke_category.questionnaire";
$participants_questionnaire_table = "ahke_category.participants_questionnaire";
$user_table = "ahke_user.user";
  if(isset($data['unico_id']) && $HTTP_REFERER){
    $post_id = local_filter_input($data["post_id"]);
    $unico_id = local_filter_input($data["unico_id"]);
    $option_num = local_filter_input($data["option_num"]);

    // get user id from unico id
    $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
    $get_user_id = $conn->query($get_user_id_sql);
    $get_user_id = $get_user_id->fetch();
    $user_id = $get_user_id["id"];
 
    $questionnaire_id_list_sql = "SELECT * FROM {$participants_questionnaire_table} WHERE user_id = {$user_id} ";
    $questionnaire_id_list = $conn->query($questionnaire_id_list_sql);
    if($questionnaire_id_list->rowCount()){
       $questionnaire_id_list = $questionnaire_id_list->fetch();
       $questionnaire_id_list = $questionnaire_id_list["questionnaire_id_list"];
       $questionnaire_id_list = $questionnaire_id_list."-({$post_id},{$option_num})";
       $update_sql = "UPDATE {$participants_questionnaire_table} SET questionnaire_id_list = '{$questionnaire_id_list}' ";
       $insert = $conn->exec($update_sql);
    }else{
      $questionnaire_id_list = "({$post_id},{$option_num})";
      $insert_sql = "INSERT INTO {$participants_questionnaire_table} (user_id,questionnaire_id_list) VALUES ({$user_id},'{$questionnaire_id_list}') ";
      $insert = $conn->exec($insert_sql);
    }
    
    
      $update_questionnaire_sql = "UPDATE {$category_table} SET 
                              option_{$option_num}_num = option_{$option_num}_num + 1
                              WHERE id = {$post_id}";
      $update_questionnaire = $conn->exec($update_questionnaire_sql);
      if($update_questionnaire){
         echo json_encode(["mess" => "تمت المشاركة","questionnaire_id_list" => $questionnaire_id_list ]);
      }
  }
?>