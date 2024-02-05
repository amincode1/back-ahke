<?php
class PutCategoryJsonData{
	public $category_table;
	public $type;
	public $limit_file;
	public $path;
	public $conn;
	public $post_id;
	public $user_id;
	public $category_name;
	public $user_post_table = "ahke_user.user_post";

	public function __construct($params){
		$this->category_name = $this->num_to_name_category($params["category_num"]);
        $this->category_table = "ahke_category.".$this->category_name;
        $this->type = $params["type"];
        $this->path = $params["path"];
        $this->conn = $params["conn"];
        $this->user_id = $params['user_id'];

        if(isset($params["limit_file"])){
          $this->limit_file = $params["limit_file"];
        }
        if(isset($params["post_id"])){
          $this->post_id = $params["post_id"];
        }
        if($this->type == "post"){
           $this->put_post($this->conn);
        }else if($this->type == "posts"){
           $this->put_posts($this->conn);
        }
	}

    public function put_post(){
    	$this->update_post_id();
    	
    	// fount path
    	$post_id_length = strcspn($this->post_id,"");
	    $path = $this->path;
		for ($i=0; $i < $post_id_length; $i++) { 
			$path .= $this->post_id[$i]."/";
			if(!is_dir($path)){
		        mkdir($path);
			    chmod($path,0777);
			}
		}

		// get post data from database
        $get_posts_sql = "SELECT a.*,b.username,b.name,b.profile_image
				          FROM {$this->category_table} a 
				          INNER JOIN ahke_user.user b on a.user_id = b.id
				          WHERE a.status = 1 AND a.id = $this->post_id ";

		$posts = $this->conn->query($get_posts_sql);

		if($posts->rowCount()){
            $posts = $posts->fetch(PDO::FETCH_ASSOC);
            $posts = json_encode($posts);
            // put in local file
			$file = fopen($path.$this->post_id.".json","w");
			chmod($path.$this->post_id.".json",0777);
			fwrite($file,$posts);
		}else{
			echo $path.$this->post_id.".json";
            if(is_dir($path)){
            	echo 'fount';
               unlink($path.$this->post_id.".json");
            }
		}
    }

	public function put_posts(){
		for ($x=0; $x < $this->limit_file; $x++) {
		    $get_posts_sql = "SELECT a.*,b.username,b.name,b.profile_image
				                FROM {$this->category_table} a 
				                INNER JOIN ahke_user.user b on a.user_id = b.id
				                WHERE a.status = 1
				                ORDER BY RAND() LIMIT 10";

			$posts = $this->conn->query($get_posts_sql);
			$posts = $posts->fetchAll(PDO::FETCH_ASSOC);
	   	    $posts = json_encode($posts);
		    $file = fopen($this->path."data-".$x.".json","w");
			// chmod($this->path."data-".$x.".json",0777);
			fwrite($file,$posts);
			$posts = null;
		}
	}

    // update post id file from user in json-database
	public function update_post_id(){
        $posts_id = $this->conn->query("SELECT * FROM $this->user_post_table WHERE `user_id` = $this->user_id ");
        // echo "SELECT * FROM $this->user_post_table WHERE `user_id` = $this->user_id ";
        if($posts_id->rowCount()){
            $posts_id = $posts_id->fetch();
	        $posts_id = $posts_id["posts_id_list"];
	     
	        // put in json-database
	        if(!is_dir("../../json-database/user/@{$this->user_id}")){
	           mkdir("../../json-database/user/@{$this->user_id}");
	        }
	        $file = fopen("../../json-database/user/@{$this->user_id}/post-id.txt","w");
	        fwrite($file,$posts_id);
        }
	}

	public function num_to_name_category($num) {
	    $categories = [1 => 'quotes',2 => 'stories',3 => 'from_book',4 => 'do_you_know',5 => 'recommendations',
	                 6 => 'question',7 => 'questionnaire',8 => 'series',10 => 'images',11 => 'videos'];
	    return $categories[$num] ?? null;
    }
}
?>