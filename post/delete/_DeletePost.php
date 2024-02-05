<?php
class DeletePost{
  public $category_name;
  public $category_num;
  public $post_id;
  public $unico_id;
  public $user_id;
  public $category_table;

  public function __construct($props){
    $this->conn = $props["conn"];
    $this->category_name = $props["category_name"];
    $this->category_num = $props["category_num"];
    $this->post_id = $props["post_id"];
    $this->unico_id = $props["unico_id"];
    $this->category_table = "ahke_category.".$this->category_name;
    $this->delete_post();
  }

  public function delete_post(){
    if($this->verify_ownership($this->unico_id)){
      if($this->have_image()){
        $this->remove_image();
      }
      $delete_post_sql = "DELETE FROM {$this->category_table} WHERE id = {$this->post_id} ";
      $delete_post = $this->conn->exec($delete_post_sql);
      if($delete_post){
        $request_info = ["request_info" => ["status" => 1]];
        echo json_encode($request_info);
        $this->update_post_json_file();
        $this->delete_user_post();
       }else{
          echo json_encode(["mess" => "لم يتم الحذف حاول مرة اخرى"]);
       }
    }else{
      echo json_encode(["mess" => "لم يتم الحذف حاول مرة اخرى"]);
    }
  }
   
  // Verify ownership of the post
  public function verify_ownership(){
    $this->user_id = $this->get_user_id($this->unico_id);
    $check_post_sql = "SELECT id FROM {$this->category_table}
                       WHERE id = {$this->post_id} AND user_id = {$this->user_id} ";
    $check_post = $this->conn->query($check_post_sql);
     if($check_post->rowCount()){
        return true;
     }else{
        return false;
     }
  }

  // Get user id from unico id
  public function get_user_id(){
    $user_table = "ahke_user.user";
    $get_user_id_sql = "SELECT id FROM {$user_table} 
                        WHERE unico_id = '{$this->unico_id}' ";
    $get_user_id = $this->conn->query($get_user_id_sql);
    $get_user_id = $get_user_id->fetch();
    return $get_user_id["id"]; 
  }

  public function have_image(){
    $category_have_image = ["from_book","do_you_know","images"];
    if(in_array($this->category_name,$category_have_image)){
      return true;
    }else{
      return false;
    }
  }

  public function remove_image(){
    $image_path_sql = "SELECT image_path FROM {$this->category_table} WHERE id = {$this->post_id} ";
    $image_path = $this->conn->query($image_path_sql);
    $image_path = $image_path->fetch();
    $image_path = $image_path["image_path"];
    if($image_path != null && $image_path != ""){
      $quality_image_path = explode('.', $image_path);
      $image_path_mini = $quality_image_path[0]."-mini.".$quality_image_path[1];
      $image_path_load = $quality_image_path[0]."-load.".$quality_image_path[1];
        if(file_exists("../../media/images/category/{$image_path}")){
          unlink("../../media/images/category/{$image_path}");
        }
        if(file_exists("../../media/images/category/{$image_path_mini}")){
          unlink("../../media/images/category/{$image_path_mini}");
        }
        if(file_exists("../../media/images/category/{$image_path_load}")){
          unlink("../../media/images/category/{$image_path_load}");
        }
    }
  }

  // add post in json database
  public function update_post_json_file(){
    $url = "http://localhost/api.ahke.net/post/put/put-single-post.php?post_id=$this->post_id&category_num=$this->category_num&user_id=$this->user_id";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_exec($ch);
    curl_close($ch);
  }

   // delete post in user post table 
   public function delete_user_post(){
     $old_user_post_id = $this->conn->query("SELECT posts_id_list FROM ahke_user.user_post
                                             WHERE user_id = $this->user_id");
     $old_user_post_id = $old_user_post_id->fetch();
     $old_user_post_id = $old_user_post_id['posts_id_list'];
     $old_user_post_id = explode(',', $old_user_post_id);
     $new_user_post_id = array_diff($old_user_post_id,[$this->post_id.'-'.$this->category_num]);
     $new_user_post_id = implode(',', $new_user_post_id);
     $update = $this->conn->query("UPDATE ahke_user.user_post 
                   SET posts_id_list = '$new_user_post_id' 
                   WHERE user_id = $this->user_id");
     if($update){
        $this->save_in_local($new_user_post_id);
     }
   }

   // save user post id in local file
   public function save_in_local($data){
      if(!is_dir("../../json-database/user/@$this->user_id/")){
         mkdir("../../json-database/user/@$this->user_id/");
         // chmod("../../json-database/user/@$this->user_id/", 0777);
      }
      chmod("../../json-database/user/@$this->user_id/", 0777);
      $file = fopen("../../json-database/user/@$this->user_id/post-id.txt","w");
      // chmod("../../json-database/user/@$this->user_id/post-id.txt",0777);
      fwrite($file,$data);
   }
}
?>