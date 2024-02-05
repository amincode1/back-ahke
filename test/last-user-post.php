<?php
include("../include/config.php");
include("../include/crypt.php");

$min_date = strtotime('2023-02-07');
$max_date = strtotime('2023-02-12');

$get_users_sql = "SELECT * FROM ahke_user.user WHERE site_user = 1 ";
$get_users = $conn->query($get_users_sql);
while ($user = $get_users->fetch()) {
	$user_id = $user["id"];
	$random_timestamp = rand($min_date, $max_date);
    $random_date = date('Y-m-d', $random_timestamp);
	$update_sql = "UPDATE ahke_user.user_stats SET last_post_date = '{$random_date}' WHERE user_id = {$user_id} ";
	$update = $conn->exec($update_sql);
}
?>