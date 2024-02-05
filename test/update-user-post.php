<?php
include("../include/config.php");
include("../include/crypt.php");
$get_users_sql = "SELECT * FROM ahke_user.user ";
$get_users = $conn->query($get_users_sql);
// $categories = ['do_you_know','from_book','images','questionnaire','quotes','recommendations','stories','series'];
while ($user = $get_users->fetch()) {
	$user_id = $user["id"];

	// set post id to user post
	$posts_id_list = $conn->query("SELECT `posts_id_list` FROM ahke_user.user_post WHERE `user_id` = $user_id");
	$posts_id_list = $posts_id_list->fetch();
	$posts_id_list = $posts_id_list['posts_id_list'];
    
	// save in local database
	if(!is_dir("../json-database/user/@$user_id/")){
        mkdir("../json-database/user/@$user_id/");
        chmod("../json-database/user/@$user_id/", 0777);
    }
    $file = fopen("../json-database/user/@$user_id/post-id.txt","w");
    // chmod("../json-database/user/@$user_id/post-id.txt",0777);
    fwrite($file,$posts_id_list);

    echo json_encode(["status_request" => 1]);
}
?>