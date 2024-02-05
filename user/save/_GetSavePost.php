<?php
class GetSavePost{
	public $posts = [];
	public $category_name;
   public $category_num;
   public $unico_id;
   public $page_num;
   public $conn;

   public function __construct($params){
     $this->conn = $params['conn'];
     $this->category_name = $params['category_name'];
     $this->category_num = $this->num_to_name_category($this->category_name);
     $this->unico_id = $params['unico_id'];
     $this->page_num = (int)$props['page_num'] * 10;
     $this->get_save_post();
   }

   public get_save_post(){
      $post_id = file_get_contents("../../json-database/user/@$this->unico_id/post-id.txt");
      $post_id_arr = explode(',',$post_id);
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
      echo json_encode($this->posts);
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
         if(!empty($get_date) && $get_date != ''){
           $get_date = json_decode($get_date,true);
           array_push($this->posts,$get_date);
         }
      }
  }

}// end class
?>