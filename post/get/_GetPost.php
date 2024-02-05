<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
class GetPost{
	public $type;
  public $request_return;
	public $posts = [];
	public $category_name;
  public $category_num;
	public $post_id;
  public $username;
  public $unico_id;
  public $search_text;
  public $page_num;
  public $user_id;
  // tables
  public $category_table;
  public $user_table = 'ahke_user.user';
  public $user_like_table = 'ahke_user.user_like';
  public $user_save_table = 'ahke_user.user_save';
  // 
  public $conn;

  public function __construct($props){
    $this->type = $props['type'];
    $this->category_name = $props['category_name'];
    $this->category_num = $this->c_na_to_nu($this->category_name);
    $this->post_id = $props['post_id'];
    $this->username = $props['username'];
    $this->unico_id = $props['unico_id'];
    $this->search_text = $props['search_text'];
    $this->page_num = (int)$props['page_num'] * 10;
    // set table
    $this->category_table = "ahke_category.{$this->category_name}";
    //
    $this->conn = $props['conn'];
    //
    if($this->type == 'main_post'){
      $this->main_post();
    }elseif($this->type == 'all_user_post'){
      $this->all_user_post();
    }else if ($this->type == 'user_post'){
      $this->user_post();
    }else if ($this->type == 'my_post'){
      $this->my_post();
    }else if ($this->type == 'my_save'){
      $this->my_save();
    }else if($this->type == 'single_post'){
      $this->single_post();
    }else if($this->type == 'search_post'){
      $this->search_post();
    }
  }

  public function main_post(){
    $file_num = rand(0,499);
    $random_post_id = file_get_contents("../../json-database/category/$this->category_name/random-post-id/$file_num".'.txt');
    $random_post_id_arr = explode(',',$random_post_id);
    foreach ($random_post_id_arr as $post_info) {
      if(!empty($post_info)){
        $post_info = explode('-',$post_info);
        $post_id = $post_info[0];
        $category_num = $post_info[1];
        $this->get_and_merge_post($post_id,$category_num);
      }
    }
    // echo request result
    $posts = ["posts" => $this->posts];
    if(count($this->posts) != 0){
      $request_info = ["request_info" => ["statu" => 1]];
    }else{
      $request_info = ["request_info" => ["statu" => 0]];
    }
    $request_return = array_merge($posts,$request_info);
    echo json_encode($request_return);
  }

  public function all_user_post(){
    $user_id = $this->get_user_id('username',$this->username);
    $user_post_id_sql = "SELECT `post_id_list` FROM ahke_user.user_post WHERE user_id = $user_id";
    $user_post_id = $this->conn->query($user_post_id_sql);
    $user_post_id = $user_post_id->fetch();
    $user_post_id = $user_post_id["post_id_list"];
    $user_post_id_arr = explode(',', $user_post_id);
    $user_post_id_arr = array_reverse($user_post_id_arr);
    $user_post_id_arr = array_slice($user_post_id_arr,$this->page_num,10);
    // print_r($user_post_id_arr);
    foreach ($user_post_id_arr as $user_post_info) {
      if(!empty($user_post_info)){
        $user_post_info = explode('-',$user_post_info);
        $post_id = $user_post_info[0];
        $category_num = $user_post_info[1];
        $this->get_and_merge_post($post_id,$category_num);
      }
    }
    // echo request result
    $posts = ["posts" => $this->posts];
    if(count($this->posts) != 0){
      $request_info = ["request_info" => ["statu" => 1]];
    }else{
      $request_info = ["request_info" => ["statu" => 0]];
    }
    $request_return = array_merge($posts,$request_info);
    echo json_encode($request_return);
  }

  public function get_and_merge_post($post_id,$category_num){
    $category_name = $this->num_to_name_category($category_num);
    $post_id_length = strcspn($post_id,'');
    $sub_path = '';
    for ($i=0; $i < $post_id_length; $i++) { 
      $sub_path .= $post_id[$i]."/";
    }
    if(file_exists("../../json-database/category/{$category_name}/post/{$sub_path}{$post_id}.json")){
      $get_date = file_get_contents("../../json-database/category/{$category_name}/post/{$sub_path}{$post_id}.json");
      if($get_date != '[]'){
        $get_date = json_decode($get_date,true);
        array_push($this->posts,$get_date);
      }
    }
  }

  public function user_post(){
    $this->get_user_post_from_local("user_post");
  }

  
  public function my_post(){
    $this->get_user_post_from_local("my_post");
  }

  public function my_save(){
    $this->get_user_post_from_local("my_save");
  }

  public function get_user_post_from_local($type){
      if($type == "user_post"){
        $user_id = $this->get_user_id('username',$this->username);
        $path = "../../json-database/user/@{$user_id}/post-id.txt";
      }
      if($type == "my_post"){
        $user_id = $this->get_user_id('unico_id',$this->unico_id);
        $path = "../../json-database/user/@{$user_id}/post-id.txt";
      }
      if($type == "my_save"){
        $user_id = $this->get_user_id('unico_id',$this->unico_id);
        $path = "../../json-database/user/@{$user_id}/save-post-id.txt";
      }
      $post_id_list = '';
      $post_id_str = file_get_contents($path);
      $post_id_arr = explode(',',$post_id_str);
      // $post_id_arr = array_pop($post_id_arr);
      if($this->category_name != 'all'){
         forEach($post_id_arr as $post_info){
          if(!empty($post_info)){
             $post_info = explode('-',$post_info);
             if(isset($post_info[1])){
                if($post_info[1] == $this->category_num){
                  $post_id_list .= $post_info[0].'-'.$post_info[1].',';
                }
             }
          }
        }
        $post_id_arr = explode(',',$post_id_list);
      }else{
        // get all category
        $post_id_arr = explode(',',$post_id_str);
      }
    
      $post_id_arr = array_reverse($post_id_arr);
      $post_id_arr = array_slice($post_id_arr,$this->page_num,10);
      forEach($post_id_arr as $user_post_info){
         if(!empty($user_post_info)){
          $user_post_info = explode('-',$user_post_info);
          $post_id = $user_post_info[0];
          $category_num = $user_post_info[1];
          $this->get_and_merge_post($post_id,$category_num);
        }
      }
      // echo request result
      $posts = ["posts" => $this->posts];
      if(count($this->posts) != 0){
        $request_info = ["request_info" => ["statu" => 1]];
      }else{
        $request_info = ["request_info" => ["statu" => 0]];
      }
      $post_id_str = ["post_id" => $post_id_str];
      $request_return = array_merge($posts,$post_id_str,$request_info);
      echo json_encode($request_return);
  }

  public function single_post(){
    $post_id_length = strcspn($this->post_id,"");
		$sub_path = '';
    $post_id = $this->post_id;
		for ($i=0; $i < $post_id_length; $i++) { 
			$sub_path .= $post_id[$i]."/";
		}
    if(file_exists("../../json-database/category/{$this->category_name}/post/{$sub_path}{$this->post_id}.json")){
      $get_date = file_get_contents("../../json-database/category/{$this->category_name}/post/{$sub_path}{$this->post_id}.json");
      $get_date = json_decode($get_date,true);
    }else{
      $get_date = [];
    }
    
    // array_push($this->posts,$get_date);
    // echo request result
    $posts = ["posts" => [$get_date]];
    if(count($this->posts) != 0){
      $request_info = ["request_info" => ["statu" => 1]];
    }else{
      $request_info = ["request_info" => ["statu" => 0]];
    }
    $request_return = array_merge($posts,$request_info);
    echo json_encode($request_return);
  }

  public function search_post(){
    $category_have_title = ['from_book','do_you_know','image'];
    if(in_array($this->category_name,$category_have_title)){
       $title = "OR a.post_title LIKE '%{$this->search_text}%'";
    }else{
      $title = '';
    }
    $sql = "SELECT a.*,b.username,b.name,b.profile_image
              FROM {$this->category_table} a 
              INNER JOIN ahke_user.user b on a.user_id = b.id
              WHERE a.post_text LIKE '%{$this->search_text}%' {$title}
              LIMIT {$this->page_num},10 ";
    $posts = $this->fetch_post($sql);
    array_push($this->posts, $posts);
    // get search result number
    if($this->page_num == 0){
      $sql = "SELECT Count(a.id) as search_result_num
              FROM {$this->category_table} a 
              INNER JOIN ahke_user.user b on a.user_id = b.id
              WHERE a.post_text LIKE '%{$this->search_text}%' {$title}
              LIMIT {$this->page_num},10";
      $result_num = $this->conn->query($sql);
      $result_num = $result_num->fetch();
      $result_num = $result_num['search_result_num'];
      array_push($this->posts, ['search_result_num' => $result_num]);
    }
    // echo request result
    $posts = ["posts" => $this->posts];
    if(count($this->posts) != 0){
      $request_info = ["request_info" => ["statu" => 1]];
    }else{
      $request_info = ["request_info" => ["statu" => 0]];
    }
    $request_return = array_merge($posts,$request_info);
    echo json_encode($request_return);
  }

  public function fetch_post($sql){
  	$posts = $this->conn->query($sql);
    if($posts->rowCount()){
      $posts = $posts->fetchAll(PDO::FETCH_ASSOC);
    }else{
      $posts = [];
    }
    return $posts;
  }

  // Update watched 
  public function update_post_watch(){
    $views_id = [];
    foreach ($posts[0] as $post) {
      array_push($views_id,$post["id"]);
    }
    $views_id = implode(',',$views_id);
    $update_views_sql = "UPDATE {$this->category_name} SET views_num = views_num + 1 WHERE id IN ($views_id) AND last_user_id_view != {$user_id};
                         UPDATE {$this->category_name} SET last_user_id_view = {$user_id} WHERE id IN ($views_id) ";
    $update_views = $this->conn->exec($update_views_sql);
  }
  
  // get like and save item from user
  public function get_user_like_save(){
  	$this->user_id = $this->get_user_id('unico_id',$this->unico_id);
    $get_user_item_like_sql = "SELECT a.items_id_list as user_post_like,
                                      b.posts_id_list as user_post_save
	                             FROM {$this->user_like_table} a 
	                             INNER JOIN {$this->user_save_table} b on a.user_id = b.user_id
	                             WHERE a.user_id = {$this->user_id} AND a.type = 0 ";
    $get_user_item_like = $this->conn->query($get_user_item_like_sql);
    $user_item_like = $get_user_item_like->fetchAll(PDO::FETCH_ASSOC);
    return $user_item_like;
  }
  
  // get user id from username or unico_id
  public function get_user_id($name,$value){
    if(!empty($value)){
      $get_user_id_sql = "SELECT `id` FROM $this->user_table
                          WHERE `$name` = '$value' ";
      $get_user_id = $this->conn->query($get_user_id_sql);
      $get_user_id = $get_user_id->fetch();
      return $get_user_id["id"];
    }else{
      return 0;
    } 
  }

  public function participants_questionnaire_id(){
    $category_table = 'ahke_category.participants_questionnaire';
    $sql = "SELECT * FROM {$category_table} WHERE user_id = {$this->user_id} ";
    $participants_questionnaire_id = $this->conn->query($sql);
    if($participants_questionnaire_id->rowCount()){
      $participants_questionnaire_id = $participants_questionnaire_id->fetch();
      return $participants_questionnaire_id["questionnaire_id_list"];
    }else{
      return null;
    }
  }

  public function num_to_name_category($num) {
    $categories = [1 => 'quotes',2 => 'stories',3 => 'from_book',4 => 'do_you_know',5 => 'recommendations',
                 6 => 'question',7 => 'questionnaire',8 => 'series',10 => 'images',11 => 'videos'];
    return $categories[$num] ?? null;
  }

  // convert category name to category number
   public function c_na_to_nu($name){
      $categories = ["quotes" => 1,"stories" => 2,"from_book" => 3,"do_you_know" => 4,"recommendations" => 5,
                     "question" => 6,"questionnaire" => 7,"series" => 8,"images" => 10];
      return $categories[$name] ?? null;
   }

}// end class
?>