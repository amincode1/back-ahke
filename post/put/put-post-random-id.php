<?php
class RandomPostId{
	public $conn;
	public $category_name;
	public $category_table;
	public $limit;
	public $random_post_id_list = '';
	public $random_post_id_list_arr;

	// category 
	public $do_you_know_id;
	public $from_book_id;
	public $images_id;
	public $questionnaire_id;
	public $quotes;
	public $recommendations;
	public $series;
	public $stories;

	public function __construct($params){
		include('../../include/config.php');
		$this->conn = $conn;
      $this->category_name = $params["category_name"];
      $this->category_num = $this->c_na_to_nu($this->category_name);
      $this->limit = $params["limit"];
      $this->category_table = 'ahke_category.'.$this->category_name;
      if($this->category_name != 'all'){
         $this->random_post_id();
	   }else{
	   	$this->random_post_id_all();
	   }
	}

	public function random_post_id(){
      $category_table = 'ahke_category'.$this->category_name;
	   $sql = "SELECT id FROM $this->category_table WHERE status = 1 ORDER BY RAND() LIMIT $this->limit";
	   $posts_id = $this->conn->query($sql);
	   $posts_id = $posts_id->fetchAll();
	   forEach($posts_id as $post_id){
         $this->random_post_id_list .= $post_id['id'].'-'.$this->category_num.',';
	   }
	   $random_post_id_list_arr = explode(',',$this->random_post_id_list);
	   // save in file
	   $this->save_post_id($random_post_id_list_arr);
	}

   // get post id from all category
	public function random_post_id_all(){
		$post_id_all_category = '';
		$categories = ["quotes","stories","from_book","do_you_know","recommendations","questionnaire","series","images"];
		forEach($categories as $category){
			$path = "../../json-database/category/$category/random-post-id/";
         for ($i=0; $i < 15 ; $i++) { 
         	$post_id_all_category .= ','.file_get_contents($path.$i.'.txt');
         }
		}
		$post_id_all_category_arr = explode(',',$post_id_all_category);
		// save in file
      $this->save_post_id($post_id_all_category_arr);
	}

	// save post id in file
   public function save_post_id($post_id_array){
   	$this->path = "../../json-database/category/$this->category_name/random-post-id/";
   	if (count($post_id_array) >= 10) {
         for ($i=0; $i < $this->limit; $i++) { 
	      	// fetch 10 rendom result from array
	         $random_keys = array_rand($post_id_array, 10);
		    	$random_results = [];
			   foreach ($random_keys as $key) {
			     $random_results[] = $post_id_array[$key];
			   }
			   $random_results_txt = implode(',',$random_results);
	         // save it in file         
	      	$file = fopen($this->path.$i.".txt","w");
		      // chmod($this->path.$i.".txt",0777);
		      fwrite($file,$random_results_txt);
         }
         echo json_encode(["status_request" => 1, "category_name" => $this->category_name]);
   	}
   }

   // convert category name to category number
   public function c_na_to_nu($name){
      $categories = ["quotes" => 1,"stories" => 2,"from_book" => 3,"do_you_know" => 4,"recommendations" => 5,
                     "question" => 6,"questionnaire" => 7,"series" => 8,"images" => 10];
      return $categories[$name] ?? null;
   }

   //round a number to the nearest multiple of ten
   public function number_file(){
      $number = count($this->random_post_id_list_arr)/10;
      $number = round($number / 10) * 10;
      return $number;
   }
}

new RandomPostId(['category_name' => 'do_you_know','limit' => 500]);
new RandomPostId(['category_name' => 'from_book','limit' => 500]);
new RandomPostId(['category_name' => 'images','limit' => 500]);
new RandomPostId(['category_name' => 'questionnaire','limit' => 500]);
new RandomPostId(['category_name' => 'quotes','limit' => 500]);
new RandomPostId(['category_name' => 'recommendations','limit' => 500]);
new RandomPostId(['category_name' => 'series','limit' => 500]);
new RandomPostId(['category_name' => 'stories','limit' => 500]);
new RandomPostId(['category_name' => 'all','limit' => 500]);


// $random_post_id_list = new RandomPostId(['category_name' => 'quotes','limit' => 1000]);
// echo $random_post_id_list->random_post_id_list;
?>