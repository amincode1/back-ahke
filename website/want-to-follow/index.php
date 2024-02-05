<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
if(isset($_SERVER["HTTP_REFERER"])){
	$file_num = rand(0,500);
    echo file_get_contents("../../{$json_database}/website/want-to-follow/data-$file_num.json");
}
?>