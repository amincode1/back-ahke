<?php
include("../../include/class/PutImage64.php");
class UpdatePost{
	public $conn;
	public $category_name;
  public $category_num;
	public $post_id;
	public $unico_id;
	public $user_id;
	public $post_text;
	public $post_title;
	public $post_image_64;
	public $category_table;

	public $status_edit_text = false;
	public $status_edit_title = false;
	public $status_edit_image = false;

	public $text_sql = '';
	public $title_sql = '';
  public $image_sql = ''; 

	public function __construct($params){
    $this->conn = $params["conn"];
    $this->category_name = $params["category_name"];
    $this->category_num = $this->c_na_to_nu($this->category_name);
    $this->post_id = $params["post_id"];
    $this->unico_id = $params["unico_id"];
    $this->category_table = "ahke_category.{$this->category_name}";
    if(!empty($params["post_text"])){
      $this->post_text = $params["post_text"];
      $this->status_edit_text = true;
    }
    if(!empty($params["post_title"])){
      $this->post_title = $params["post_title"];
      $this->status_edit_title = true;
    }
    if(!empty($params["post_image_64"])){
      $this->post_image_64 = $params["post_image_64"];
      $this->status_edit_image = true;
    }
    $this->update_post();
	}

	public function update_post(){
    if($this->verify_ownership()){
    	if($this->status_edit_text){
        $this->text_sql = "post_text = '{$this->post_text}'";
    	}
    	if($this->status_edit_title){
    		$this->status_edit_text ? $comma = ',' : $comma = '';
        $this->title_sql = "{$comma} post_title = '{$this->post_title}'";
    	}
    	if($this->status_edit_image){
    		$this->status_edit_title ? $comma = ',' : $comma = '';
    		$image_path = $this->update_image();
        $this->image_sql = "{$comma} image_path = '{$image_path}'";
    	}
    	$sql = "UPDATE {$this->category_table} 
    	        SET $this->text_sql $this->title_sql $this->image_sql 
    	        WHERE id = {$this->post_id}";
    	$update_post = $this->conn->exec($sql);
    	if($update_post){
        echo json_encode(["status_request" => 1,"post_text" => $this->post_text]);
        $this->update_post_json_file();
    	}else{
    		echo json_encode(["status_request" => 0]);
    	}
    }else{
    	echo json_encode(["status_request" => 0]);
    }
  }

  public function get_user_id(){
    $user_table = "ahke_user.user";
    $get_user_id_sql = "SELECT id FROM {$user_table} 
                        WHERE unico_id = '{$this->unico_id}' ";
    $get_user_id = $this->conn->query($get_user_id_sql);
    $get_user_id = $get_user_id->fetch();
    return $get_user_id["id"]; 
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

	// put and update image
	public function update_image(){
    $this->post_image_64 = explode(',', $this->post_image_64);
    $this->post_image_64 = $this->post_image_64[1];
    $putImage = new putImage64($this->post_image_64,"../../media/images/category/$this->category_name/");
    // remove old image
    $this->remove_image();
    // return image path to set in database
    return $this->category_name.'/'.$putImage->getPath();
	}

	public function remove_image(){
		// get old image path from database
    $sql = "SELECT image_path FROM {$this->category_table} WHERE id = {$this->post_id} ";
    $image_path = $this->conn->query($sql);
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

  // convert category name to category number
   public function c_na_to_nu($name){
      $categories = ["quotes" => 1,"stories" => 2,"from_book" => 3,"do_you_know" => 4,"recommendations" => 5,
                     "question" => 6,"questionnaire" => 7,"series" => 8,"images" => 10];
      return $categories[$name] ?? null;
   }
}
?>