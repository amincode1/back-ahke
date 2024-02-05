<?php
include("../../api-setting.php");
include("../../include/config.php");
$user_table = "ahke_user.user";
$post_data = '';
for ($x=0; $x < 500; $x++) {
    $get_rand_user_sql = "SELECT id,name,username,profile_image FROM {$user_table} ORDER BY RAND() LIMIT 10";
    $rand_user = $conn->query($get_rand_user_sql);
    $rand_user = $rand_user->fetchAll(PDO::FETCH_ASSOC);
    $rand_user = json_encode($rand_user);

	$file = fopen("../../{$json_database}/website/want-to-follow/data-".$x.".json","w");
	// chmod("../../{$json_database}/website/want-to-follow/data-".$x.".json",0777);
	fwrite($file,$rand_user);
	// $post_data = '';
}
?> 