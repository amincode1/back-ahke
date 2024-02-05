<?php
class AddPost{
	public $conn;
	public $post_text;
	public $post_title;
	public $post_image_64;
	public $post_id;
	public $unico_id;
	public $user_id;
   public $username;
	public $category_name;
   public $category_num;
   public $category_table;
   public $user_table = 'ahke_user.user';

   public $image_path;
   public $video_id;
   public $video_duration;

   public $option_1;
   public $option_2;
   public $option_3;
   public $option_4;
   public $option_5;
   public $correct_answer_num;

	public function __construct($params){
		$this->conn = $params['conn'];
      $this->post_text = $params['post_text'];
      $this->post_title = $params['post_title'];
      $this->post_image_64 = $params['post_image_64'];
      $this->unico_id = $params['unico_id'];
      $this->category_name = $params['category_name'];
      $this->category_num = $this->c_na_to_nu($this->category_name);
      $this->category_table = 'ahke_category.'.$this->category_name;
      if($this->category_name == 'recommendations'){
         $this->video_id = $params['video_id'];
         $this->video_duration = $params['video_duration'];
      }
      if($this->category_name == 'questionnaire'){
         $this->option_1 = $params['option_1'];
         $this->option_2 = $params['option_2'];
         $this->option_3 = $params['option_3'];
         $this->option_4 = $params['option_4'];
         $this->option_5 = $params['option_5'];
         $this->correct_answer_num = $params['correct_answer_num'];
      }
      $this->add_post();
	}

	public function add_post(){
		$user_info = $this->get_user_info($this->unico_id);
      $this->user_id = $user_info["id"];
      $this->username = $user_info["username"];

      if(!empty($this->post_image_64)){
         $this->add_image();
      }
      $add_post_sql = $this->post_sql($this->category_name);
      $add_post = $this->conn->exec($add_post_sql);
      if($add_post){
         $this->post_id = $this->get_post_id();
         $post_id = file_get_contents("../../json-database/user/@$this->user_id/post-id.txt");
         $post_id = $post_id."$this->post_id-$this->category_num";
         echo json_encode(['status_request' => 1,'mess' => 'تمت الاضافة','id' => $this->post_id,'post_id' => $post_id]);
         // add post to user post table 
         $this->update_user_post_id();
         // add post in json database
         $this->add_post_json_database();
      }else{
      	echo json_encode(['status_request' => 0,'mess' => 'حدث خطأ ما حاول مرة اخرى']);
      }
	}

	public function post_sql($category_name){
		$add_post_sql = [
        'quotes' => "INSERT INTO {$this->category_table} (`user_id`,`post_text`) 
		               VALUES ({$this->user_id},'{$this->post_text}')",

		  'do_you_know' => "INSERT INTO {$this->category_table} (`user_id`,`post_text`,`image_path`) 
		                    VALUES ({$this->user_id},'{$this->post_text}','{$this->image_path}')",

		  'from_book' => "INSERT INTO {$this->category_table} (`user_id`,`post_title`,`post_text`,`image_path`)
		                  VALUES ({$this->user_id},'{$this->post_title}','{$this->post_text}','{$this->image_path}')",

		  'images' => "INSERT INTO {$this->category_table} (`user_id`,`post_title`,`image_path`) 
		               VALUES ({$this->user_id},'{$this->post_title}','{$this->image_path}')",

		  'questionnaire' => "INSERT INTO {$this->category_table}
		                      (`user_id`,`post_title`,`option_1`,`option_2`,`option_3`,`option_4`,`option_5`,`correct_answer_num`)
		                      VALUES ({$this->user_id},'{$this->post_title}','{$this->option_1}','{$this->option_2}','{$this->option_3}','{$this->option_4}','{$this->option_5}',{$this->correct_answer_num})",

		   'recommendations' => "INSERT INTO {$this->category_table} 
		                         (`user_id`,`post_text`,`video_id`,`video_duration`) 
		                         VALUES ({$this->user_id},'{$this->post_text}','{$this->video_id}','{$this->video_duration}')",

		   'series' => "INSERT INTO {$this->category_table} (`user_id`,`post_text`,`post_title`) 
		                VALUES ({$this->user_id},'{$this->post_text}','{$this->post_title}')",

		   'stories' => "INSERT INTO {$this->category_table} (`user_id`,`post_text`,`post_title`) 
		                 VALUES ({$this->user_id},'{$this->post_text}','{$this->post_title}')"              
		];
		return $add_post_sql[$category_name];
	}

	// get user id from unico id
   public function get_user_info($value){
      if(!empty($value)){
       	$user_info = $this->conn->query("SELECT id,username FROM {$this->user_table} WHERE unico_id = '{$value}' ");
         $user_info = $user_info->fetch();
         return $user_info;
      }else{
         return 0;
      } 
   }

   public function get_post_id(){
   	$post_id = $this->conn->query("SELECT id FROM $this->category_table 
		    	                         WHERE user_id = $this->user_id 
		    	                         ORDER BY id DESC LIMIT 1 ");
		$post_id = $post_id->fetch();
		return $post_id["id"];
   }

   public function add_image(){
      include("../../include/class/PutImage64.php");
      // conver image base64 to image and put in path
      $base64String = $this->post_image_64;
      // put base64 image
      $category_name = str_replace('_','-',$this->category_name);
      $putImage = new putImage64($base64String,"../../media/images/category/$category_name/");
      $this->image_path = $category_name."/".$putImage->getPath();
   }

   // update user post id in ahke_user.user_post
   public function update_user_post_id(){
      $user_post_id = $this->conn->query("SELECT `posts_id_list` FROM ahke_user.user_post 
                                          WHERE `user_id` = $this->user_id");
      if($user_post_id->rowCount()){
         $user_post_id = $user_post_id->fetch();
         $user_post_id = $user_post_id['posts_id_list'];
         // add new user post id
         $user_post_id .= $this->post_id.'-'.$this->category_num.',';
         // update in database
         $update = $this->conn->exec("UPDATE ahke_user.user_post 
                                      SET `posts_id_list` = '$user_post_id' 
                                      WHERE user_id = $this->user_id");
         if($update){
            $this->save_in_local($user_post_id);
         }else{

         }
      }else{
         $user_post_id = $this->post_id.'-'.$this->category_num.',';
         $insert = $this->conn->exec("INSERT INTO ahke_user.user_post (`user_id`,`posts_id_list`) 
                                      VALUES ($this->user_id,'$user_post_id')");
         if($insert){
            $this->save_in_local($user_post_id);
         }else{

         }
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

   // add post in json database
   public function add_post_json_database(){
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