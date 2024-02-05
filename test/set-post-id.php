<?php
// set post id in user_post table
include("../include/config.php");
include("../include/crypt.php");
$categories_name = ["do_you_know","from_book","images","questionnaire","quotes","recommendations","series","stories"];
$user_table = "ahke_user.user";
$set_table = "ahke_user.user_follower";
$get_user_sql = "SELECT id FROM {$user_table}";
$get_user = $conn->query($get_user_sql);

while ($user = $get_user->fetch()) {
	// echo $user["id"];
	forEach ($categories_name as $category_name) {
	  // get_user_post_id($conn,$user["id"],$category_name);
	  set_post_id_in_user_post($conn,$user["id"]);
	}
}

function get_user_post_id($conn,$user_id,$category_name){
   $get_post_info = $conn->query("SELECT * from ahke_category.$category_name WHERE user_id = $user_id");
   while($post_info = $get_post_info->fetch()){
       if(!empty($post_info)){
   	        $category_num = $post_info["category_num"];
            $post_id = $post_info["id"]."-".$category_num;
            $date = $post_info["added_date"];
            $conn->exec("INSERT INTO ahke_user._user_post 
      	           (`user_id`,`post_id`,`date`)
                   VALUES ($user_id,'$post_id','$date')");
        }
   }
}

function set_post_id_in_user_post($conn,$user_id){
	$get_post_id = $conn->query("SELECT * FROM ahke_user._user_post 
		                         WHERE user_id = $user_id 
		                         ORDER BY `date` ASC");
	$post_id_list = '';
	while($post_id = $get_post_id->fetch()){
	   $post_id_list .= $post_id["post_id"].',';
	}
	$check_row = $conn->query("SELECT * FROM ahke_user.user_post 
		                        WHERE `user_id` = $user_id ");
	if($check_row->rowCount()){
       $conn->exec("UPDATE ahke_user.user_post SET `posts_id_list` = '$post_id_list' WHERE `user_id` = $user_id ");
	}else{
       $conn->exec("INSERT INTO ahke_user.user_post (`user_id`,`posts_id_list`) VALUES ($user_id,'$post_id_list')");
	}
}
?>