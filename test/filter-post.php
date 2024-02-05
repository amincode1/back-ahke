<?php
include("../include/config.php");
include("../include/crypt.php");
$get_post_sql = "SELECT * FROM ahke_comment.question_comment ";
$get_post = $conn->query($get_post_sql);
while ($post = $get_post->fetch()) {
	$id = $post["id"];
	$text = $post["comment_text"];
	// $text = str_replace("</div>","<br>",$text);
	// $text = str_replace("<div>","",$text);
	$text = str_replace('(**)'," 0d0 ",$text);
	// $text = str_replace('`',"(*)",$text);
	$text = str_replace("(*)"," 0s0 ",$text);
	// $text = rtrim($text,"<br>");
	// $text = local_filter_post($text);
	$update_post_sql = "UPDATE ahke_comment.question_comment SET comment_text = '{$text}' WHERE id = $id ";
	$update_post = $conn->exec($update_post_sql);
}
?>