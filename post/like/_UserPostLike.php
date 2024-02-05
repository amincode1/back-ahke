<?php
class PostUserLike{
  public $conn;
  public $post_id;
  public $category_name;
  public $page_num;
  public $category_table;
  public $post_like_table;

  public function __construct($params){
    $this->conn = $params['conn'];
    $this->post_id = $params['post_id'];
    $this->category_name = $params['category_name'];
    $this->page_num = $params['page_num'];
    $this->category_table = "ahke_category.{$this->category_name}";
    $this->post_like_table = "ahke_like.post_{$this->category_name}_like";
    $this->post_user_like();
  }

  public function post_user_like(){
    $list_user_id = $this->get_list_user_id();
    if(!empty($list_user_id)){
      // convert user id list to string
      $list_user_id_str = implode(',',$list_user_id);
      $list_user_id_str = trim($list_user_id_str,',');
      
      // get user info
      $user_table = 'ahke_user.user';
      $sql = "SELECT id,username,name,profile_image 
              FROM {$user_table} 
              WHERE id IN ({$list_user_id_str}) 
              ORDER BY FIELD(id,{$list_user_id_str})";
      $user_like = $this->conn->query($sql);
      $user_like = $user_like->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode($user_like);
    }
  }
  
  // get user id liked the post
  public function get_list_user_id(){
    $from = (int)$this->page_num * 10;
    $limit = $from + 10;

    $sql = "SELECT user_id_list FROM {$this->post_like_table} WHERE post_id = $this->post_id";
    $user_id = $this->conn->query($sql);
    if($user_id->rowCount()){
      $user_id = $user_id->fetch();
      $user_id = $user_id['user_id_list'];
      // convert return user id to array
      $user_id = explode(',',$user_id);
      $user_id = array_reverse($user_id);
      $user_id = array_slice($user_id,$from,$limit);
      return $user_id;
    }else{
      return null;
    }
  }
}
?>