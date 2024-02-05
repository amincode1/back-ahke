<?php
// include("api-setting.php");
include("../include/config.php");
include("../include/crypt.php");
$user_table = "ahke_user.user";
$set_table = "ahke_user.user_follower";
$get_user_sql = "SELECT id FROM {$user_table}";
$get_user = $conn->query($get_user_sql);
while ($user = $get_user->fetch()) {
	// echo "INSERT INTO {$user_setting_table} (user_id) VALUES ({$user['id']})";
	$insert_sql = "INSERT INTO {$set_table} (user_id) VALUES ({$user['id']})";
	$insert = $conn->exec($insert_sql);
}
?>
