<?php
include("../../../api-setting.php");
include("../../../include/config.php");
include("../../../include/crypt.php");

$data = json_decode(file_get_contents("php://input"),1);
$advise_me_table = 'ahke_category.advise_me';
$user_table = "ahke_user.user";
if(isset($data['unico_id']) && $HTTP_REFERER){
   $unico_id = local_filter_input($data["unico_id"]);
   // get user id from unico id
   $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
   $get_user_id = $conn->query($get_user_id_sql);
   $get_user_id = $get_user_id->fetch();
   $user_id = $get_user_id["id"];

   $get_advise_me_sql = "SELECT * FROM {$advise_me_table} WHERE user_id = {$user_id} ORDER BY id DESC";
   $get_advise_me = $conn->query($get_advise_me_sql);
   if($get_advise_me->rowCount()){
      $advise_me = $get_advise_me->fetchAll(PDO::FETCH_ASSOC);
     if($advise_me != null){
         echo json_encode($advise_me);
         $update_watched_sql = "UPDATE {$advise_me_table} SET watched = 1 ";
         $update_watched = $conn->exec($update_watched_sql);
     }else{
         echo json_encode(["mess" => "حدث خطا ما حاول مرة اخرى"]);
     }
   }else{
      echo json_encode(["mess" => ""]);
   }
}
?>