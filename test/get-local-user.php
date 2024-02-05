<?php
// include("api-setting.php");
include("../include/config.php");
include("../include/crypt.php");
$user_table = "ahke_user.user";
$set_table = "ahke_user.user_follower";
$get_user_sql = "SELECT name,username,unico_id FROM {$user_table} WHERE site_user = 1 and gender = 'أنثى'";
$get_user = $conn->query($get_user_sql);
$get_user = $get_user->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($get_user);
?>
