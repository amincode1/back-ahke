<?php
include("../include/config.php");
$get_users_sql = "SELECT * FROM ahke_user.user";
$get_users = $conn->query($get_users_sql);
$image_table = "ahke_website.profile_image";
$user_table = "ahke_user.user";
while ($user = $get_users->fetch()) {
	$user_id = $user["id"];
	$get_profile_image_path_sql = "SELECT * FROM $image_table WHERE 1 ORDER BY RAND() LIMIT 1";
	$profile_image = $conn->query($get_profile_image_path_sql);
	$profile_image = $profile_image->fetch();
	$profile_image=  "l/".$profile_image["path"];
	$update_profile_image_sql = "UPDATE $user_table set `profile_image` = '$profile_image' WHERE id = $user_id ";
	$update_profile_image = $conn->exec($update_profile_image_sql);

	// $conn->exec("INSERT INTO ahke_user.user_stats (`user_id`) VALUES ($user_id)");
}

// $get_profile_image_sql = "SELECT * FROM ahke_user.user";
// $profile_image = $conn->query($get_profile_image_sql);

// while ($image = $profile_image->fetch()) {
// 	$id = $image["id"];
// 	$path = trim($image["profile_image"]);

// 	$update_profile_image_sql = "UPDATE ahke_website.profile_image SET `path` = '$path' WHERE id = $id  ";
// 	$update_profile_image = $conn->exec($update_profile_image_sql);
// }
?>