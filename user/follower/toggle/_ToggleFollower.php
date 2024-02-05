<?php
/**
 * 
 */
class ToggleFollower
{
   public $unico_id;
   public $user_id;
   public $user_follow_id;
   public $user_table = 'ahke_user.user';
   public $user_follower_table = 'ahke_user.user_follower';
   public $user_stats_table = 'ahke_user.user_stats'; 

   public function __construct($params)
   {
      $this->conn = $params["conn"];
      $this->unico_id = $params["unico_id"];
      $this->user_id = $this->get_user_id();
      $this->user_follow_id = $params["user_follow_id"];
      $this->toggle_follower();
   }

   public function toggle_follower(){
      $check_if_follower = $this->check_if_follower();
      if($check_if_follower[0] == true){
         $user_follower_id_arr = $check_if_follower[1];
         $this->remove_follower($user_follower_id_arr);
      }else{
         if(isset($check_if_follower[1])){
            $user_follower_id_arr = $check_if_follower[1];
            $this->add_follower($user_follower_id_arr);
         }else{
            $this->add_frist_time();
         }
      }
   }

   public function check_if_follower(){
      $user_follower_id = $this->conn->query("SELECT `follow_them_id` FROM $this->user_follower_table 
                                              WHERE user_id = $this->user_id ");
      if($user_follower_id->rowCount()){
         $user_follower_id = $user_follower_id->fetch();
         $user_follower_id = $user_follower_id['follow_them_id'];
         if(empty($user_follower_id)){
            return [false,[]];
         }else{
            $user_follower_id_arr = explode(',',$user_follower_id);
            if(in_array($this->user_follow_id,$user_follower_id_arr)){
               return [true,$user_follower_id_arr];
            }else{
               return [false,$user_follower_id_arr];
            }
         }
      }else{
         return [false];
      }
   }

   public function add_frist_time(){
      $follower_id = $this->user_follow_id.',';
      $insert = $this->conn->exec("INSERT INTO $this->user_follower_table (`user_id`,`follow_them_id`)
                                   VALUES ($this->user_id,'$follower_id') ");
      if($insert){
         $this->rebound_followup('add');
         echo json_encode(["request_status" => ["status" => 1],"follow_them_id" => $this->user_follow_id.',']);
      }else{
         echo json_encode(["request_status" => ["status" => 0]]);
      }
   }

   public function add_follower($follower_id_arr){
      if(count($follower_id_arr) != 0){
         $follower_id = implode(',',$follower_id_arr);
         $follower_id .= $this->user_follow_id.',';
      }else{
         $follower_id = $this->user_follow_id.',';
      }
      $update = $this->conn->exec("UPDATE $this->user_follower_table 
                                   SET `follow_them_id` = '$follower_id' 
                                   WHERE `user_id` = $this->user_id ");
      if($update){
         $this->rebound_followup('add');
         echo json_encode(["request_status" => ["status" => 1],"follow_them_id" => $follower_id]);
      }else{
         echo json_encode(["request_status" => ["status" => 0]]);
      }
   }

   public function remove_follower($follower_id_arr){
      $follower_id = array_diff($follower_id_arr,[$this->user_follow_id]);
      $follower_id = implode(',',$follower_id);
      $update = $this->conn->exec("UPDATE $this->user_follower_table 
                                   SET `follow_them_id` = '$follower_id' 
                                   WHERE `user_id` = $this->user_id ");
      if($update){
         $this->rebound_followup('remove');
         echo json_encode(["request_status" => ["status" => 1],"follow_them_id" => $follower_id]);
      }else{
         echo json_encode(["request_status" => ["status" => 0]]);
      }
   }

   public function rebound_followup($type){
      if($type == 'add'){
         $follower_id = $this->conn->query("SELECT * FROM $this->user_follower_table 
                                            WHERE `user_id` = $this->user_follow_id");
         if($follower_id->rowCount()){
            $follower_id = $follower_id->fetch();
            $follower_id = $follower_id['follower_id'];
            $follower_id = $follower_id.$this->user_id.',';
            $update = $this->conn->exec("UPDATE $this->user_follower_table 
                                         SET `follower_id` = '$follower_id' 
                                         WHERE `user_id` = $this->user_id ");
         }else{
            $follower_id = $this->user_id.',';
            $insert = $this->conn->query("INSERT INTO $this->user_follower_table (`user_id`,`follower_id`) 
                                          VALUES ($this->user_follow_id,'$follower_id') ");
         }
      }else if($type == 'remove'){

      }
   }


   // get user id from unico id
   public function get_user_id(){
      if(!empty($this->unico_id)){
         $get_user_id_sql = "SELECT `id` FROM $this->user_table
                             WHERE `unico_id` = '$this->unico_id' ";
         $get_user_id = $this->conn->query($get_user_id_sql);
         $get_user_id = $get_user_id->fetch();
         return $get_user_id["id"];
      }else{
         return 0;
      } 
   }
}
?>