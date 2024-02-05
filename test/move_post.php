<?php
include("../include/config.php");
include("../include/crypt.php");

$users = [];

$get_site_user_sql = "SELECT * FROM ahke_user.user WHERE site_user = 1";
$get_site_user = $conn->query($get_site_user_sql);
while($user = $get_site_user->fetch()){
   array_push($users, $user["id"]);
};


$get_post_sql = "SELECT * FROM ahke_category.recommendations WHERE user_id = 1537 ";
$get_post = $conn->query($get_post_sql);
while($post = $get_post->fetch()){
   $post_id = $post["id"];
   $key = array_rand($users);
   $new_user = $users[$key];
   $move_post_sql = "UPDATE ahke_category.recommendations SET user_id = {$new_user} WHERE id = {$post_id} ";
   $move_post = $conn->exec($move_post_sql);
   echo $move_post_sql."<br>";
}
?>