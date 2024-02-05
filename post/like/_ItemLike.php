<?php
class ItemLike{
  public $conn;
  public $unico_id;
  public $item_id;
  public $user_id;
  public $user_item_id_liked_arr;
  public $user_item_liked_str;
  public $category_name;
  public $category_num;
  public $type;
  public $category_table;
  public $item_like_table;
  public $user_like_table;
  public $user_table = 'ahke_user.user';

  public function __construct($params){
    $this->conn = $params["conn"];
    $this->unico_id = $params["unico_id"];
    $this->item_id = $params["item_id"];
    $this->category_num = $params["category_num"];
    $this->category_name = $params["category_name"];
    $this->type = $params["type"];
    $this->category_table = "ahke_category.{$this->category_name}";
    $this->item_like_table = "ahke_like.{$this->category_name}";
    $this->user_like_table = "ahke_user.user_like";
    $this->item_like();
  }

  public function item_like(){
    $user_id = $this->user_id = $this->get_user_id($this->unico_id);
    if($this->check_item_liked()){
        $this->remove_like();
    }else{
        // if item not liked
        $this->add_like();
    }
  }

  public function add_like(){
    if(count($this->user_item_id_liked_arr) != 0){
      $new_user_like = implode(',',$this->user_item_id_liked_arr);
      $new_user_like = $new_user_like.$this->item_id.'-'.$this->category_num.',';
      $update = $this->conn->exec("UPDATE $this->user_like_table 
                                   SET items_id_list = '$new_user_like' 
                                   WHERE user_id = $this->user_id AND `type` = $this->type");
      if($update){
        $this->public_item_like('add');
      }
    }else{
      $new_user_like = $this->item_id.'-'.$this->category_num.',';
      $insert = $this->conn->exec("INSERT INTO $this->user_like_table (`user_id`,`type`,`items_id_list`)
                                   VALUES ($this->user_id,$this->type,'$new_user_like')");
      if($insert){
        $this->public_item_like('add');
      }
    }
  }

  public function remove_like(){
    $new_user_like = array_diff($this->user_item_id_liked_arr,[$this->item_id.'-'.$this->category_num]);
    $new_user_like = implode(',',$new_user_like);
    $update = $this->conn->exec("UPDATE $this->user_like_table 
                                 SET `items_id_list` = '$new_user_like' 
                                 WHERE `user_id` = $this->user_id");
    if($update){
       $this->public_item_like('remove');
    }
  }

  public function public_item_like($op){
    $new_users_id_list = '';
    $users_id_list = $this->conn->query("SELECT `users_id_list` 
                                         FROM $this->item_like_table 
                                         WHERE `item_id` = $this->item_id AND `type` = $this->type");
    if($users_id_list->rowCount()){
      $users_id_list = $users_id_list->fetch();
      $users_id_like = $users_id_list['users_id_list'];
      if($op == 'add'){
        $new_users_id_list = $users_id_like.$this->user_id.',';
      }else if($op == 'remove'){
        $users_id_like_arr = explode(',',$users_id_like);
        $users_id_like_arr = array_diff($users_id_like_arr,[$this->user_id]);
        $new_users_id_list = implode(',',$users_id_like_arr);
      }
      $update = $this->conn->exec("UPDATE $this->item_like_table 
                                   SET `users_id_list` = '$new_users_id_list' 
                                   WHERE `item_id` = $this->item_id AND `type` = $this->type ");
      if($update){
        $user_post_like = $this->get_user_like();
        echo json_encode(['status_request' => 1,'post_like_id' => $user_post_like]);
        // save user like in local file
        $this->save_in_local($user_post_like);
      }else{
        echo json_encode(['status_request' => 0]);
      }
    }else{
      $list_user = $this->user_id.',';
      $insert = $this->conn->exec("INSERT INTO $this->item_like_table (`item_id`,`users_id_list`,`type`) 
                                   VALUES ($this->item_id,'$list_user',$this->type)");
      if($insert){
        $user_post_like = $this->get_user_like();
        echo json_encode(['status_request' => 1,'post_like_id' => $user_post_like]);
        // save user like in local file
        $this->save_in_local($user_post_like);
      }else{
        echo json_encode(['status_request' => 0]);
      }
    }
  }

  // save user like in local file
  public function save_in_local($data){
    if(!is_dir("../../json-database/user/@$this->unico_id/")){
      mkdir("../../json-database/user/@$this->unico_id/");
      chmod("../../json-database/user/@$this->unico_id/", 0777);
    }
    $file = fopen("../../json-database/user/@$this->unico_id/post-like-id.txt","w");
    chmod("../../json-database/user/@$this->unico_id/post-like-id.txt",0777);
    fwrite($file,$data);
  }

  // check item liked or not liked
  public function check_item_liked(){
    $user_item_id_liked = $this->conn->query("SELECT `items_id_list` 
                                              FROM $this->user_like_table 
                                              WHERE `user_id` = $this->user_id AND `type` = $this->type");
    // echo "SELECT `items_id_list` 
    //                                           FROM $this->user_like_table 
    //                                           WHERE `user_id` = $this->user_id";
    if($user_item_id_liked->rowCount()){
      $user_item_id_liked = $user_item_id_liked->fetch();
      $user_item_id_liked = $user_item_id_liked['items_id_list'];
      $this->user_item_id_liked_arr = explode(',',$user_item_id_liked);
       if(in_array($this->item_id.'-'.$this->category_num,$this->user_item_id_liked_arr)){
          return true;
       }else{
          return false;
       }
    }else{
      $this->user_item_id_liked_arr = [];
      return false;
    }
  }

  // get user like 
  public function get_user_like(){
    $get_user_item_like_sql = "SELECT `items_id_list`
                               FROM $this->user_like_table
                               WHERE `user_id` = $this->user_id AND `type` = 0 ";
    $get_user_item_like = $this->conn->query($get_user_item_like_sql);
    $user_item_like = $get_user_item_like->fetch();
    $user_item_like = $user_item_like['items_id_list'];
    return $user_item_like;
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