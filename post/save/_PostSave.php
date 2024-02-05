<?php
class PostSave{
  public $conn;
  public $unico_id;
  public $post_id;
  public $user_id;
  public $user_post_id_saved_arr;
  public $user_post_saved_str;
  public $category_name;
  public $category_num;
  public $post_save_table;
  public $user_save_table;
  public $user_table = 'ahke_user.user';

  public function __construct($params){
    $this->conn = $params["conn"];
    $this->unico_id = $params["unico_id"];
    $this->post_id = $params["post_id"];
    $this->category_num = $params["category_num"];
    $this->category_name = $params["category_name"];
    $this->post_save_table = "ahke_save.{$this->category_name}";
    $this->user_save_table = "ahke_user.user_save";
    $this->post_save();
  }

  public function post_save(){
    $user_id = $this->user_id = $this->get_user_id($this->unico_id);
    if($this->check_post_saved()){
      $this->remove_save();
    }else{
      // if post not saved
      $this->add_save();
    }
  }

  public function add_save(){
    if(count($this->user_post_id_saved_arr) != 0){
      $new_user_save = implode(',',$this->user_post_id_saved_arr);
      $new_user_save = $new_user_save.$this->post_id.'-'.$this->category_num.',';
      $update = $this->conn->exec("UPDATE $this->user_save_table 
                                   SET `posts_id_list` = '$new_user_save' 
                                   WHERE user_id = $this->user_id");
      if($update){
        $user_save_id = $this->get_user_save();
        echo json_encode(["status_request" => 1,'post_save_id' => $user_save_id]);
        // save user like in local file
        $this->save_in_local($user_save_id);
      }else{
        echo json_encode(["status_request" => 0]);
      }
    }else{
      $new_user_save = $this->post_id.'-'.$this->category_num.',';
      $insert = $this->conn->exec("INSERT INTO $this->user_save_table (`user_id`,`posts_id_list`)
                                   VALUES ($this->user_id,'$new_user_save')");
      if($insert){
        $user_save_id = $this->get_user_save();
        echo json_encode(["status_request" => 1,'post_save_id' => $user_save_id]);
        // save user like in local file
        $this->save_in_local($user_save_id);
      }else{
        echo json_encode(["status_request" => 0]);
      }
    }
  }
  
  public function remove_save(){
    $new_user_save = array_diff($this->user_post_id_saved_arr,[$this->post_id.'-'.$this->category_num]);
    $new_user_save = implode(',',$new_user_save);
    $update = $this->conn->exec("UPDATE $this->user_save_table 
                                 SET `posts_id_list` = '$new_user_save' 
                                 WHERE `user_id` = $this->user_id");
    if($update){
      $user_save_id = $this->get_user_save();
      echo json_encode(["status_request" => 1,'post_save_id' => $user_save_id]);
      // save user like in local file
      $this->save_in_local($user_save_id);
    }else{
      echo json_encode(["status_request" => 0]);
    }
  }

  // check post saved or not saved
  public function check_post_saved(){
    $user_post_id_saved = $this->conn->query("SELECT `posts_id_list` 
                                              FROM $this->user_save_table 
                                              WHERE `user_id` = $this->user_id");
    if($user_post_id_saved->rowCount()){
      $user_post_id_saved = $user_post_id_saved->fetch();
      $user_post_id_saved = $user_post_id_saved['posts_id_list'];
      $this->user_post_id_saved_arr = explode(',',$user_post_id_saved);
       if(in_array($this->post_id.'-'.$this->category_num,$this->user_post_id_saved_arr)){
          return true;
       }else{
          return false;
       }
    }else{
      $this->user_post_id_saved_arr = [];
      return false;
    }
  }

  // get user save 
  public function get_user_save(){
    $get_user_item_save_sql = "SELECT `posts_id_list`
                               FROM $this->user_save_table
                               WHERE `user_id` = $this->user_id";
    $get_user_item_save = $this->conn->query($get_user_item_save_sql);
    $user_item_save = $get_user_item_save->fetch();
    $user_item_save = $user_item_save['posts_id_list'];
    return $user_item_save;
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

  // save user like in local file
  public function save_in_local($data){
    if(!is_dir("../../json-database/user/@$this->user_id/")){
      mkdir("../../json-database/user/@$this->user_id/");
      chmod("../../json-database/user/@$this->user_id/", 0777);
    }
    $file = fopen("../../json-database/user/@$this->user_id/save-post-id.txt","w");
    // chmod("../../json-database/user/@$this->user_id/post-save-id.txt",0777);
    fwrite($file,$data);
  }
}
?>