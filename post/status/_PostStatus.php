<?php
class PostStatus{
  public $unico_id;
  public $type;
  public $status;
  public $category_name;
  public $post_id;
  public $category_table;

  public function __construct($params){
    $this->conn = $params["conn"];
    $this->unico_id = $params["unico_id"];
    $this->type = $params["type"];
    $this->status = $params["status"];
    $this->category_name = $params["category_name"];
    $this->post_id = $params["post_id"];
    $this->category_table = "ahke_category.{$this->category_name}";
    $this->update_status();
  }
  
  // Update status
  public function update_status(){
    if($this->verify_ownership()){
      if($this->type == "post"){
        $sql = "UPDATE {$this->category_table} 
              SET status = {$this->status} WHERE id = {$this->post_id} ";
      }else if($this->type == "like"){
        $sql = "UPDATE {$this->category_table} 
                SET status_like = {$this->status} WHERE id = {$this->post_id} ";
      }else if($this->type == "comment"){
        $sql = "UPDATE {$this->category_table} 
                SET status_comment = {$this->status} WHERE id = {$this->post_id} ";
      }
      $update_status = $this->conn->exec($sql);
      if($update_status){
        echo json_encode(["status_request" => 1]);
        // update post in json database
        $url = "http://localhost/api.ahke.net/post/put/put-single-post.php?post_id=$this->post_id&category_name=$this->category_name";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        curl_close($ch);
      }else{
        echo json_encode(["status_request" => 0]);
      }
    }else{
      echo json_encode(["status_request" => 0]);
    }
  }

  // get user id from unico id
  public function get_user_id(){
    $user_table = 'ahke_user.user';
    $sql = "SELECT id FROM {$user_table} 
            WHERE `unico_id` = '{$this->unico_id}' ";
    $user_id = $this->conn->query($sql);
    $user_id = $user_id->fetch();
    return $user_id["id"];
  }

  // Verify ownership of the post
  public function verify_ownership(){
    $this->user_id = $this->get_user_id();
    $sql = "SELECT id FROM {$this->category_table} 
                       WHERE id = {$this->post_id} AND user_id = {$this->user_id} ";
    $check_post = $this->conn->query($sql);
    if($check_post->rowCount()){
      return true;
    }else{
      return false;
    }
  }
}
?>