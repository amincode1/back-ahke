<?php
include("../../../api-setting.php");
for ($i=0; $i < 4000 ; $i++) { 
	$url = "$api_host/website/archives/post/quotes.php?id=$i";

			// Send HTTP request using cURL
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($ch);
			curl_close($ch);
}
?>