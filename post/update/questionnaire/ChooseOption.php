<?php
/*
p_q = participants_questionnaire
*/
class ChooseOption{
  public $questionnaire_table = "ahke_category.questionnaire";
  public $p_q_table = "ahke_category.participants_questionnaire";
  public $user_table = "ahke_user.user";
  public $unico_id;
  public $user_id;
  public $post_id;
  public $option_num;

  public function __construct($props){
    $this->conn = $props["conn"];
    $this->unico_id = $props["unico_id"];
    $this->post_id = $props["post_id"];
    $this->option_num = $props["option_num"];
    $this->set_choose();
  }

  public function set_choose(){
    $this->user_id = $this->get_user_id();
    $get_p_q_id = $this->get_participants_questionnaire_id();
    $p_q_id = $get_p_q_id['p_q_id'];
    $row_count = $get_p_q_id['row_count'];
    $questionnaire_id_list;
    if($row_count){
       // add new to questionnaire id list
       $questionnaire_id_list = $p_q_id."-({$this->post_id},{$this->option_num})";
       $update_sql = "UPDATE {$this->p_q_table} 
                      SET questionnaire_id_list = '{$questionnaire_id_list}'
                      WHERE user_id = {$this->user_id} ";
       $insert = $this->conn->exec($update_sql);
    }else{
      // first add to questionnaire id list
      $questionnaire_id_list = "({$this->post_id},{$this->$option_num})";
      $insert_sql = "INSERT INTO {$this->p_q_table} (user_id,questionnaire_id_list)
                     VALUES ({$this->user_id},'{$questionnaire_id_list}') ";
      $insert = $this->conn->exec($insert_sql);
    }
    
    // update option num in questionnaire table
    $update_questionnaire_sql = "UPDATE {$this->questionnaire_table} 
                                 SET option_{$this->option_num}_num = option_{$this->option_num}_num + 1
                                 WHERE id = {$this->post_id}";
    $update_questionnaire = $this->conn->exec($update_questionnaire_sql);

    if($update_questionnaire){
      echo json_encode([
        "status_request" => 1,
        "participants_questionnaire_id" => $questionnaire_id_list 
      ]);
    }
  }

  // get user id from unico id
  public function get_user_id(){
    $get_user_id_sql = "SELECT id FROM {$this->user_table} WHERE `unico_id` = '{$this->unico_id}' ";
    $get_user_id = $this->conn->query($get_user_id_sql);
    $get_user_id = $get_user_id->fetch();
    return $get_user_id["id"];
  }

  public function get_participants_questionnaire_id(){
    $sql = "SELECT * FROM {$this->p_q_table} WHERE user_id = {$this->user_id} ";
    $p_q_id = $this->conn->query($sql);
    if($p_q_id->rowCount()){
      $p_q_id = $p_q_id->fetch();
      return [
        'row_count' => true, 
        'p_q_id' => $p_q_id['questionnaire_id_list']
      ];
    }else{
      return [
        'row_count' => false, 
        'p_q_id' => ''
      ];
    }
  }
}  
?>