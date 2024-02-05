<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents("php://input"),1);
if(isset($_SERVER["HTTP_REFERER"])){
	$page_num = $data["page_num"];
	if($data["type"] == 'avater'){
      echo file_get_contents("json-data/avater/{$page_num}.json");
	}else{
	  echo file_get_contents("json-data/{$page_num}.json");
	}
}
?>